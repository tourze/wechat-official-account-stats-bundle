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
use WechatOfficialAccountStatsBundle\Request\GetUserCumulateRequest;
use WechatOfficialAccountStatsBundle\Service\UserCumulateDataProcessor;

/**
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/User_Analysis_Data_Interface.html
 */
#[WithMonologChannel(channel: 'wechat_official_account_stats')]
#[AsCronTask(expression: '4 3 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取累计用户数据')]
class SyncUserCumulateCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-user-cumulate';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly UserCumulateDataProcessor $dataProcessor,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $endDate = CarbonImmutable::yesterday();
        $startDate = $endDate->subDays(6);

        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetUserCumulateRequest();
            $request->setAccount($account);
            $request->setBeginDate($startDate);
            $request->setEndDate($endDate);

            $response = $this->client->request($request);

            if (is_array($response)) {
                $this->dataProcessor->processResponse($account, $response);
            }

            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
