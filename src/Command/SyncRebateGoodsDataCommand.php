<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Request\GetAdvertisingSpaceDataRequest;
use WechatOfficialAccountStatsBundle\Service\RebateGoodsDataProcessor;

/**
 * 获取公众号返佣商品数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Ad_Analysis.html
 */
#[WithMonologChannel(channel: 'wechat_official_account_stats')]
#[AsCronTask(expression: '11 3 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取公众号返佣商品数据')]
class SyncRebateGoodsDataCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-rebate-goods-data';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly RebateGoodsDataProcessor $dataProcessor,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetAdvertisingSpaceDataRequest();
            $request->setAction('publisher_cps_general');
            $request->setAccount($account);
            $request->setPage('1');
            $request->setPageSize('10');
            $request->setStartDate(CarbonImmutable::now()->startOfWeek()->subWeek()->format('Y-m-d'));
            $request->setEndDate(CarbonImmutable::now()->startOfWeek()->subDay()->format('Y-m-d'));
            $result = $this->client->request($request);

            if (!is_array($result)) {
                continue;
            }

            $this->dataProcessor->processResponse($account, $result);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
