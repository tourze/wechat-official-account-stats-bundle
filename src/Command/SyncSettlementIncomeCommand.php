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
use WechatOfficialAccountStatsBundle\Entity\SettlementIncomeData;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderStatusEnum;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\SettlementIncomeDataRepository;
use WechatOfficialAccountStatsBundle\Request\GetAdvertisingSpaceDataRequest;

/**
 * 获取公众号结算收入数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Ad_Analysis.html
 */
#[AsCronTask('41 0 * * *')]
#[AsCommand(name: 'wechat:official-account:SyncSettlementIncomeCommand', description: '公众号-获取公众号结算收入数据')]
class SyncSettlementIncomeCommand extends Command
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly SettlementIncomeDataRepository $settlementIncomeDataRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetAdvertisingSpaceDataRequest();
            $request->setAction('publisher_settlement');
            $request->setAccount($account);
            $request->setPage(1);
            $request->setPageSize(10);
            $request->setStartDate(Carbon::now()->weekday(1)->subDays(7));
            $request->setEndDate(Carbon::now()->weekday(6)->subDays(6));
            $result = $this->client->request($request);
            if (!isset($result['list'])) {
                $this->logger->error('获取公众号结算收入数据发生错误', [
                    'account' => $account,
                    'response' => $result,
                ]);
                continue;
            }

            foreach ($result['settlement_list'] as $item) {
                $date = Carbon::parse($item['date']);
                foreach ($item['slot_revenue'] as $value) {
                    $settlementIncomeData = $this->settlementIncomeDataRepository->findOneBy([
                        'account' => $account,
                        'date' => $date,
                        'slot_id' => $date,
                    ]);
                    if (!$settlementIncomeData) {
                        $settlementIncomeData = new SettlementIncomeData();
                        $settlementIncomeData->setAccount($account);
                        $settlementIncomeData->setDate($date);
                        $settlementIncomeData->setSlotId($value['slot_id']);
                    }
                    $settlementIncomeData->setBody($result['body']);
                    $settlementIncomeData->setPenaltyAll($result['penalty_all']);
                    $settlementIncomeData->setRevenueAll($result['revenue_all']);
                    $settlementIncomeData->setSettledRevenueAll($result['settled_revenue_all']);
                    $settlementIncomeData->setZone($item['zone']);
                    $settlementIncomeData->setMonth($item['month']);
                    $settlementIncomeData->setOrder(SettlementIncomeOrderTypeEnum::tryFrom($item['order']));
                    $settlementIncomeData->setSettStatus(SettlementIncomeOrderStatusEnum::tryFrom($item['sett_status']));
                    $settlementIncomeData->setSettledRevenue($item['settled_revenue']);
                    $settlementIncomeData->setSettNo($item['sett_no']);
                    $settlementIncomeData->setMailSendCnt($item['mail_send_cnt']);
                    $settlementIncomeData->setSlotSettledRevenue($value['slot_settled_revenue']);
                    $this->entityManager->persist($settlementIncomeData);
                    $this->entityManager->flush();
                }
            }
        }

        return Command::SUCCESS;
    }
}
