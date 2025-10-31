<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\RebateGoodsData;
use WechatOfficialAccountStatsBundle\Repository\RebateGoodsDataRepository;

class RebateGoodsDataProcessor
{
    public function __construct(
        private readonly RebateGoodsDataRepository $rebateGoodsDataRepository,
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
            $this->logger->error('获取公众号返佣商品数据发生错误', [
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
        $exposureCount = $this->extractExposureCount($item);
        $clickCount = $this->extractClickCount($item);
        $clickRate = $this->extractClickRate($item);
        $orderCount = $this->extractOrderCount($item);
        $orderRate = $this->extractOrderRate($item);
        $totalFee = $this->extractTotalFee($item);
        $totalCommission = $this->extractTotalCommission($item);

        $rebateGoodsData = $this->findOrCreateRebateGoodsData($account, $date);
        $rebateGoodsData->setExposureCount($exposureCount);
        $rebateGoodsData->setClickCount($clickCount);
        $rebateGoodsData->setClickRate($clickRate);
        $rebateGoodsData->setOrderCount($orderCount);
        $rebateGoodsData->setOrderRate($orderRate);
        $rebateGoodsData->setTotalFee($totalFee);
        $rebateGoodsData->setTotalCommission($totalCommission);

        $this->entityManager->persist($rebateGoodsData);
    }

    /**
     * 提取日期
     *
     * @param array<string, mixed> $item 数据项
     * @return \DateTimeImmutable
     */
    private function extractDate(array $item): \DateTimeImmutable
    {
        $dateString = '';
        if (isset($item['date']) && is_string($item['date'])) {
            $dateString = $item['date'];
        }

        $dateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $dateString);
        if (false === $dateTime) {
            $dateTime = new \DateTimeImmutable();
        }

        return $dateTime->setTime(0, 0, 0);
    }

    /**
     * 提取曝光次数
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractExposureCount(array $item): int
    {
        if (isset($item['exposure_rate_count']) && is_int($item['exposure_rate_count'])) {
            return $item['exposure_rate_count'];
        }

        return 0;
    }

    /**
     * 提取点击次数
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractClickCount(array $item): int
    {
        if (isset($item['click_count']) && is_int($item['click_count'])) {
            return $item['click_count'];
        }

        return 0;
    }

    /**
     * 提取点击率
     *
     * @param array<string, mixed> $item 数据项
     * @return string
     */
    private function extractClickRate(array $item): string
    {
        if (isset($item['click_rate']) && is_string($item['click_rate'])) {
            return $item['click_rate'];
        }

        return '';
    }

    /**
     * 提取订单数量
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractOrderCount(array $item): int
    {
        if (isset($item['order_count']) && is_int($item['order_count'])) {
            return $item['order_count'];
        }

        return 0;
    }

    /**
     * 提取订单转化率
     *
     * @param array<string, mixed> $item 数据项
     * @return string
     */
    private function extractOrderRate(array $item): string
    {
        if (isset($item['order_rate']) && is_string($item['order_rate'])) {
            return $item['order_rate'];
        }

        return '';
    }

    /**
     * 提取总费用
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractTotalFee(array $item): int
    {
        if (isset($item['total_fee']) && is_numeric($item['total_fee'])) {
            return (int) $item['total_fee'];
        }

        return 0;
    }

    /**
     * 提取总佣金
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractTotalCommission(array $item): int
    {
        if (isset($item['total_commission']) && is_numeric($item['total_commission'])) {
            return (int) $item['total_commission'];
        }

        return 0;
    }

    /**
     * 查找或创建返佣商品数据实体
     *
     * @param Account $account 账户信息
     * @param \DateTimeImmutable $date 日期
     * @return RebateGoodsData
     */
    private function findOrCreateRebateGoodsData(
        Account $account,
        \DateTimeImmutable $date,
    ): RebateGoodsData {
        $rebateGoodsData = $this->rebateGoodsDataRepository->findOneBy([
            'account' => $account,
            'date' => $date,
        ]);

        if (null === $rebateGoodsData) {
            $rebateGoodsData = new RebateGoodsData();
            $rebateGoodsData->setAccount($account);
            $rebateGoodsData->setDate($date);
        }

        return $rebateGoodsData;
    }
}
