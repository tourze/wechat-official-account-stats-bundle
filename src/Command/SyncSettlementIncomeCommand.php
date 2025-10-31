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
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Entity\SettlementIncomeData;
use WechatOfficialAccountStatsBundle\Repository\SettlementIncomeDataRepository;
use WechatOfficialAccountStatsBundle\Request\GetAdvertisingSpaceDataRequest;
use WechatOfficialAccountStatsBundle\Service\SettlementIncomeDataExtractor;

/**
 * 获取公众号结算收入数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Ad_Analysis.html
 */
#[WithMonologChannel(channel: 'wechat_official_account_stats')]
#[AsCronTask(expression: '41 0 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取公众号结算收入数据')]
class SyncSettlementIncomeCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-settlement-income';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly SettlementIncomeDataRepository $settlementIncomeDataRepository,
        private readonly SettlementIncomeDataExtractor $dataExtractor,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $this->syncSettlementIncomeForAccount($account);
        }

        return Command::SUCCESS;
    }

    private function syncSettlementIncomeForAccount(Account $account): void
    {
        $request = $this->createRequest($account);
        $result = $this->client->request($request);

        if (!is_array($result) || !isset($result['list'])) {
            $this->logError($account, $result);

            return;
        }

        /** @var array<string, mixed> $result */
        $this->processSettlementList($account, $result);
    }

    private function createRequest(Account $account): GetAdvertisingSpaceDataRequest
    {
        $request = new GetAdvertisingSpaceDataRequest();
        $request->setAction('publisher_settlement');
        $request->setAccount($account);
        $request->setPage('1');
        $request->setPageSize('10');
        $request->setStartDate(CarbonImmutable::now()->startOfWeek()->subWeek()->format('Y-m-d'));
        $request->setEndDate(CarbonImmutable::now()->startOfWeek()->subDay()->format('Y-m-d'));

        return $request;
    }

    /**
     * @param mixed $result
     */
    private function logError(Account $account, mixed $result): void
    {
        $this->logger->error('获取公众号结算收入数据发生错误', [
            'account' => $account,
            'response' => $result,
        ]);
    }

    /**
     * @param array<string, mixed> $result
     */
    private function processSettlementList(Account $account, array $result): void
    {
        $settlementList = $this->extractSettlementList($result);
        if (null === $settlementList) {
            return;
        }

        foreach ($settlementList as $item) {
            if (!is_array($item)) {
                continue;
            }
            /** @var array<string, mixed> $item */
            $this->processSettlementItem($account, $result, $item);
        }
        $this->entityManager->flush();
    }

    /**
     * @param array<string, mixed> $result
     *
     * @return array<mixed>|null
     */
    private function extractSettlementList(array $result): ?array
    {
        if (!isset($result['settlement_list']) || !is_array($result['settlement_list'])) {
            return null;
        }

        return $result['settlement_list'];
    }

    /**
     * @param array<string, mixed> $result
     * @param array<string, mixed> $item
     */
    private function processSettlementItem(Account $account, array $result, array $item): void
    {
        $date = $this->extractDate($item);
        $slotRevenue = $this->extractSlotRevenue($item);

        if (null === $slotRevenue) {
            return;
        }

        foreach ($slotRevenue as $value) {
            if (!is_array($value)) {
                continue;
            }
            /** @var array<string, mixed> $value */
            $this->processSlotRevenue($account, $date, $result, $item, $value);
        }
    }

    /**
     * @param array<string, mixed> $item
     */
    private function extractDate(array $item): CarbonImmutable
    {
        $dateString = isset($item['date']) && is_string($item['date']) ? $item['date'] : '';

        return CarbonImmutable::parse($dateString);
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array<mixed>|null
     */
    private function extractSlotRevenue(array $item): ?array
    {
        if (!isset($item['slot_revenue']) || !is_array($item['slot_revenue'])) {
            return null;
        }

        return $item['slot_revenue'];
    }

    /**
     * @param array<string, mixed> $result
     * @param array<string, mixed> $item
     * @param array<string, mixed> $value
     */
    private function processSlotRevenue(Account $account, CarbonImmutable $date, array $result, array $item, array $value): void
    {
        $settlementIncomeData = $this->findOrCreateSettlementIncomeData($account, $date, $value);
        $this->updateSettlementIncomeData($settlementIncomeData, $result, $item, $value);
        $this->entityManager->persist($settlementIncomeData);
    }

    /**
     * @param array<string, mixed> $value
     */
    private function findOrCreateSettlementIncomeData(Account $account, CarbonImmutable $date, array $value): SettlementIncomeData
    {
        $settlementIncomeData = $this->settlementIncomeDataRepository->findOneBy([
            'account' => $account,
            'date' => $date,
            'slot_id' => $date,
        ]);

        if (null === $settlementIncomeData) {
            $settlementIncomeData = new SettlementIncomeData();
            $settlementIncomeData->setAccount($account);
            $settlementIncomeData->setDate($date);
            $settlementIncomeData->setSlotId(isset($value['slot_id']) && is_string($value['slot_id']) ? $value['slot_id'] : '');
        }

        return $settlementIncomeData;
    }

    /**
     * @param array<string, mixed> $result
     * @param array<string, mixed> $item
     * @param array<string, mixed> $value
     */
    private function updateSettlementIncomeData(SettlementIncomeData $settlementIncomeData, array $result, array $item, array $value): void
    {
        $this->dataExtractor->populate($settlementIncomeData, $result, $item, $value);
    }
}
