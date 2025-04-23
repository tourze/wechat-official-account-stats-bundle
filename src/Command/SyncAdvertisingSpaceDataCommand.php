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
use WechatOfficialAccountStatsBundle\Entity\AdvertisingSpaceData;
use WechatOfficialAccountStatsBundle\Repository\AdvertisingSpaceDataRepository;
use WechatOfficialAccountStatsBundle\Request\GetAdvertisingSpaceDataRequest;

/**
 * 获取公众号分广告位数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Ad_Analysis.html
 */
#[AsCronTask('1 1 * * *')]
#[AsCommand(name: 'wechat:official-account:SyncAdvertisingSpaceDataCommand', description: '公众号-获取公众号分广告位数据')]
class SyncAdvertisingSpaceDataCommand extends Command
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly AdvertisingSpaceDataRepository $advertisingSpaceDataRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetAdvertisingSpaceDataRequest();
            $request->setAction('publisher_adpos_general');
            $request->setAccount($account);
            $request->setPage(1);
            $request->setPageSize(10);
            $request->setStartDate(Carbon::now()->weekday(1)->subDays(7));
            $request->setEndDate(Carbon::now()->weekday(6)->subDays(6));
            $result = $this->client->request($request);
            if (!isset($result['list'])) {
                $this->logger->error('获取公众号分广告位数据错误', [
                    'account' => $account,
                    'response' => $result,
                ]);
                continue;
            }

            foreach ($result['list'] as $item) {
                $date = Carbon::parse($item['date']);
                $advertisingSpaceData = $this->advertisingSpaceDataRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                ]);
                if (!$advertisingSpaceData) {
                    $advertisingSpaceData = new AdvertisingSpaceData();
                    $advertisingSpaceData->setAccount($account);
                    $advertisingSpaceData->setDate($date);
                }
                $advertisingSpaceData->setSlotId($item['slot_id']);
                $advertisingSpaceData->setAdSlot($item['ad_slot']);
                $advertisingSpaceData->setReqSuccCount($item['req_succ_count']);
                $advertisingSpaceData->setExposureCount($item['exposure_rate_count']);
                $advertisingSpaceData->setClickCount($item['click_count']);
                $advertisingSpaceData->setClickRate($item['click_rate']);
                $advertisingSpaceData->setIncome($item['income']);
                $advertisingSpaceData->setEcpm($item['ecpm']);
                $this->entityManager->persist($advertisingSpaceData);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
