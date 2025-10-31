<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Request\GetMessageSendMonthDataRequest;
use WechatOfficialAccountStatsBundle\Service\MessageSendMonthDataProcessor;

/**
 * 获取消息发送月数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Message_analysis_data_interface.html
 */
// 每个月1号执行
#[AsCronTask(expression: '22 4 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取消息发送月数据')]
class SyncMessageSendMonthDataCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-message-send-month-data';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly MessageSendMonthDataProcessor $dataProcessor,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetMessageSendMonthDataRequest();
            $request->setAccount($account);
            $request->setBeginDate(CarbonImmutable::now()->subMonth()->startOfMonth());
            $request->setEndDate(CarbonImmutable::now()->subMonth()->endOfMonth());

            $response = $this->client->request($request);

            if (is_array($response)) {
                $this->dataProcessor->processResponse($account, $response);
            }

            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
