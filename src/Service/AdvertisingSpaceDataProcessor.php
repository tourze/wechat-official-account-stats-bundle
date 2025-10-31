<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\AdvertisingSpaceData;
use WechatOfficialAccountStatsBundle\Repository\AdvertisingSpaceDataRepository;

/**
 * 广告位数据处理器
 */
class AdvertisingSpaceDataProcessor
{
    public function __construct(
        private readonly AdvertisingSpaceDataRepository $advertisingSpaceDataRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 处理广告位数据响应
     *
     * @param array<string, mixed> $response API响应数据
     * @param Account $account 账户
     */
    public function processResponse(array $response, Account $account): void
    {
        if (!isset($response['list']) || !is_array($response['list'])) {
            $this->logger->error('获取公众号分广告位数据错误', [
                'account' => $account,
                'response' => $response,
            ]);

            return;
        }

        /** @var array<mixed> $list */
        $list = $response['list'];

        foreach ($list as $item) {
            if (!is_array($item)) {
                continue;
            }

            /** @var array<string, mixed> $item */
            try {
                $this->processItem($item, $account);
            } catch (\Throwable $e) {
                $this->logger->error('处理广告位数据项失败', [
                    'account' => $account,
                    'item' => $item,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * 处理单个数据项
     *
     * @param array<string, mixed> $item 单个数据项
     * @param Account $account 账户
     */
    private function processItem(array $item, Account $account): void
    {
        $date = $this->parseDate($item);
        $advertisingSpaceData = $this->findOrCreateEntity($account, $date);

        $this->updateEntity($advertisingSpaceData, $item);
        $this->entityManager->persist($advertisingSpaceData);
    }

    /**
     * 解析日期
     *
     * @param array<string, mixed> $item 数据项
     */
    private function parseDate(array $item): \DateTimeImmutable
    {
        $dateString = isset($item['date']) && is_string($item['date']) ? $item['date'] : '';

        return new \DateTimeImmutable($dateString);
    }

    /**
     * 查找或创建实体
     */
    private function findOrCreateEntity(Account $account, \DateTimeImmutable $date): AdvertisingSpaceData
    {
        $advertisingSpaceData = $this->advertisingSpaceDataRepository->findOneBy([
            'account' => $account,
            'date' => $date,
        ]);

        if (null === $advertisingSpaceData) {
            $advertisingSpaceData = new AdvertisingSpaceData();
            $advertisingSpaceData->setAccount($account);
            $advertisingSpaceData->setDate($date);
        }

        return $advertisingSpaceData;
    }

    /**
     * 更新实体数据
     *
     * @param AdvertisingSpaceData $entity 实体
     * @param array<string, mixed> $item 数据项
     */
    private function updateEntity(AdvertisingSpaceData $entity, array $item): void
    {
        $entity->setSlotId($this->extractInt($item, 'slot_id'));
        $entity->setAdSlot($this->extractString($item, 'ad_slot'));
        $entity->setReqSuccCount($this->extractInt($item, 'req_succ_count'));
        $entity->setExposureCount($this->extractInt($item, 'exposure_rate_count'));
        $entity->setClickCount($this->extractInt($item, 'click_count'));
        $entity->setClickRate($this->extractString($item, 'click_rate'));
        $entity->setIncome($this->extractInt($item, 'income'));
        $entity->setEcpm($this->extractString($item, 'ecpm'));
    }

    /**
     * 提取整数值
     *
     * @param array<string, mixed> $data 数据
     * @param string $key 键名
     */
    private function extractInt(array $data, string $key): int
    {
        return isset($data[$key]) && is_numeric($data[$key]) ? (int) $data[$key] : 0;
    }

    /**
     * 提取字符串值
     *
     * @param array<string, mixed> $data 数据
     * @param string $key 键名
     */
    private function extractString(array $data, string $key): string
    {
        return isset($data[$key]) && is_string($data[$key]) ? $data[$key] : '';
    }
}
