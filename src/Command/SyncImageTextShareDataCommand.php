<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Request\GetUserShareRequest;
use WechatOfficialAccountStatsBundle\Service\ImageTextShareDataProcessor;

/**
 * 获取图文分享转发数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
 */
#[WithMonologChannel(channel: 'wechat_official_account_stats')]
// 每周一跑
#[AsCronTask(expression: '11 1 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取图文分享转发数据')]
class SyncImageTextShareDataCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-image-text-share-data';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly ImageTextShareDataProcessor $dataProcessor,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetUserShareRequest();
            $request->setAccount($account);
            $request->setBeginDate(CarbonImmutable::now()->startOfWeek()->subWeek());
            $request->setEndDate(CarbonImmutable::now()->startOfWeek()->subDay());
            $response = $this->client->request($request);

            if (!is_array($response)) {
                continue;
            }

            $this->dataProcessor->processResponse($account, $response);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
