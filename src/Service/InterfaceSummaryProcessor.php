<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummary;
use WechatOfficialAccountStatsBundle\Repository\InterfaceSummaryRepository;

class InterfaceSummaryProcessor
{
    public function __construct(
        private readonly InterfaceSummaryRepository $interfaceSummaryRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 处理API响应数据
     *
     * @param Account $account 账户信息
     * @param array<mixed> $response API响应数据
     */
    public function processResponse(Account $account, array $response): void
    {
        if (!$this->validateResponse($response)) {
            $this->logger->error('获取接口分析数据发生错误', [
                'account' => $account,
                'response' => $response,
            ]);

            return;
        }

        $list = $response['list'];
        assert(is_array($list));
        $this->processDataList($account, $list);
    }

    /**
     * 验证响应格式
     *
     * @param array<mixed> $response API响应数据
     * @return bool
     */
    private function validateResponse(array $response): bool
    {
        return isset($response['list']) && is_array($response['list']);
    }

    /**
     * 处理数据列表
     *
     * @param Account $account 账户信息
     * @param array<mixed> $list 数据列表
     */
    private function processDataList(Account $account, array $list): void
    {
        foreach ($list as $item) {
            $this->processDataItem($account, $item);
        }
    }

    /**
     * 处理单个数据项
     *
     * @param Account $account 账户信息
     * @param mixed $item 数据项
     */
    private function processDataItem(Account $account, mixed $item): void
    {
        if (!is_array($item)) {
            return;
        }

        /** @var array<string, mixed> $item */
        $date = $this->extractDate($item);
        $callbackCount = $this->extractCallbackCount($item);
        $failCount = $this->extractFailCount($item);
        $maxTimeCost = $this->extractMaxTimeCost($item);
        $totalTimeCost = $this->extractTotalTimeCost($item);

        $interfaceSummary = $this->findOrCreateInterfaceSummary($account, $date);
        $interfaceSummary->setCallbackCount($callbackCount);
        $interfaceSummary->setFailCount($failCount);
        $interfaceSummary->setMaxTimeCost($maxTimeCost);
        $interfaceSummary->setTotalTimeCost($totalTimeCost);

        $this->entityManager->persist($interfaceSummary);
    }

    /**
     * 提取日期
     *
     * @param array<string, mixed> $item 数据项
     * @return \DateTimeImmutable
     */
    private function extractDate(array $item): \DateTimeImmutable
    {
        $refDate = '';
        if (isset($item['ref_date']) && is_string($item['ref_date'])) {
            $refDate = $item['ref_date'];
        }

        $dateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $refDate);
        if (false === $dateTime) {
            $dateTime = new \DateTimeImmutable();
        }

        return $dateTime->setTime(0, 0, 0);
    }

    /**
     * 提取回调次数
     *
     * @param array<string, mixed> $item 数据项
     * @return int|null
     */
    private function extractCallbackCount(array $item): ?int
    {
        if (isset($item['callback_count']) && is_int($item['callback_count'])) {
            return $item['callback_count'];
        }

        return null;
    }

    /**
     * 提取失败次数
     *
     * @param array<string, mixed> $item 数据项
     * @return int|null
     */
    private function extractFailCount(array $item): ?int
    {
        if (isset($item['fail_count']) && is_int($item['fail_count'])) {
            return $item['fail_count'];
        }

        return null;
    }

    /**
     * 提取最大时间消耗
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractMaxTimeCost(array $item): int
    {
        if (isset($item['max_time_cost']) && is_numeric($item['max_time_cost'])) {
            return (int) $item['max_time_cost'];
        }

        return 0;
    }

    /**
     * 提取总时间消耗
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractTotalTimeCost(array $item): int
    {
        if (isset($item['total_time_cost']) && is_numeric($item['total_time_cost'])) {
            return (int) $item['total_time_cost'];
        }

        return 0;
    }

    /**
     * 查找或创建接口汇总数据实体
     *
     * @param Account $account 账户信息
     * @param \DateTimeImmutable $date 日期
     * @return InterfaceSummary
     */
    private function findOrCreateInterfaceSummary(
        Account $account,
        \DateTimeImmutable $date,
    ): InterfaceSummary {
        $interfaceSummary = $this->interfaceSummaryRepository->findOneBy([
            'account' => $account,
            'date' => $date,
        ]);

        if (null === $interfaceSummary) {
            $interfaceSummary = new InterfaceSummary();
            $interfaceSummary->setAccount($account);
            $interfaceSummary->setDate($date);
        }

        return $interfaceSummary;
    }
}
