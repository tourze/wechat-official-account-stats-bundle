<?php

namespace WechatOfficialAccountStatsBundle\Command;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Entity\RebateGoodsData;
use WechatOfficialAccountStatsBundle\Repository\RebateGoodsDataRepository;
use WechatOfficialAccountStatsBundle\Request\GetAdvertisingSpaceDataRequest;

/**
 * 获取公众号返佣商品数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Ad_Analysis.html
 */
#[AsCronTask('11 3 * * *')]
#[AsCommand(name: 'wechat:official-account:SyncRebateGoodsDataCommand', description: '公众号-获取公众号返佣商品数据')]
class SyncRebateGoodsDataCommand extends Command
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly RebateGoodsDataRepository $rebateGoodsDataRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly OfficialAccountClient $client,
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
            $request->setStartDate(Carbon::now()->weekday(1)->subDays(7));
            $request->setEndDate(Carbon::now()->weekday(6)->subDays(6));
            $result = $this->client->request($request);
            if (!isset($result['list'])) {
                $this->logger->error('获取公众号返佣商品数据发生错误', [
                    'account' => $account,
                    'response' => $result,
                ]);
                continue;
            }

            foreach ($result['list'] as $item) {
                $date = Carbon::parse($item['date']);
                $rebateGoodsData = $this->rebateGoodsDataRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                ]);
                if (!$rebateGoodsData) {
                    $rebateGoodsData = new RebateGoodsData();
                    $rebateGoodsData->setAccount($account);
                    $rebateGoodsData->setDate($date);
                }
                $rebateGoodsData->setExposureCount($item['exposure_rate_count']);
                $rebateGoodsData->setClickCount($item['click_count']);
                $rebateGoodsData->setClickRate($item['click_rate']);
                $rebateGoodsData->setOrderCount($item['order_count']);
                $rebateGoodsData->setOrderRate($item['order_rate']);
                $rebateGoodsData->setTotalFee($item['total_fee']);
                $rebateGoodsData->setTotalCommission($item['total_commission']);
                $this->entityManager->persist($rebateGoodsData);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
