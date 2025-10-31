<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummaryHour;
use WechatOfficialAccountStatsBundle\Repository\InterfaceSummaryHourRepository;

/**
 * 接口分析分时数据处理器
 */
class InterfaceSummaryHourProcessor
{
    public function __construct(
        private readonly InterfaceSummaryHourRepository $interfaceSummaryHourRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 处理接口分析分时数据响应
     *
     * @param array<string, mixed> $response API响应数据
     * @param Account $account 账户
     */
    public function processResponse(array $response, Account $account): void
    {
        if (!isset($response['list']) || !is_array($response['list'])) {
            $this->logger->error('获取接口分析分时数据发生错误', [
                'account' => $account,
                'response' => $response,
            ]);

            return;
        }

        foreach ($response['list'] as $item) {
            /** @var array<string, mixed> $item */

            try {
                $this->processItem($item, $account);
            } catch (\Throwable $e) {
                $this->logger->error('处理接口分析分时数据项失败', [
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
        $refHour = $this->extractInt($item, 'ref_hour');
        $interfaceSummaryHourData = $this->findOrCreateEntity($account, $date, $refHour);

        $this->updateEntity($interfaceSummaryHourData, $item);
        $this->entityManager->persist($interfaceSummaryHourData);
    }

    /**
     * 解析日期
     *
     * @param array<string, mixed> $item 数据项
     */
    private function parseDate(array $item): \DateTimeImmutable
    {
        $dateString = isset($item['ref_date']) && is_string($item['ref_date']) ? $item['ref_date'] : '';

        return new \DateTimeImmutable($dateString);
    }

    /**
     * 查找或创建实体
     */
    private function findOrCreateEntity(Account $account, \DateTimeImmutable $date, int $refHour): InterfaceSummaryHour
    {
        $interfaceSummaryHourData = $this->interfaceSummaryHourRepository->findOneBy([
            'account' => $account,
            'date' => $date,
            'refHour' => $refHour,
        ]);

        if (null === $interfaceSummaryHourData) {
            $interfaceSummaryHourData = new InterfaceSummaryHour();
            $interfaceSummaryHourData->setAccount($account);
            $interfaceSummaryHourData->setDate($date);
            $interfaceSummaryHourData->setRefHour($refHour);
        }

        return $interfaceSummaryHourData;
    }

    /**
     * 更新实体数据
     *
     * @param InterfaceSummaryHour $entity 实体
     * @param array<string, mixed> $item 数据项
     */
    private function updateEntity(InterfaceSummaryHour $entity, array $item): void
    {
        $entity->setCallbackCount($this->extractIntOrNull($item, 'callback_count'));
        $entity->setFailCount($this->extractIntOrNull($item, 'fail_count'));
        $entity->setMaxTimeCost($this->extractInt($item, 'max_time_cost'));
        $entity->setTotalTimeCost($this->extractInt($item, 'total_time_cost'));
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
     * 提取整数值或null
     *
     * @param array<string, mixed> $data 数据
     * @param string $key 键名
     */
    private function extractIntOrNull(array $data, string $key): ?int
    {
        if (isset($data[$key]) && is_int($data[$key])) {
            return $data[$key];
        }

        return null;
    }
}
