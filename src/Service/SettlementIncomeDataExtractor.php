<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Service;

use WechatOfficialAccountStatsBundle\Entity\SettlementIncomeData;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderStatusEnum;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderTypeEnum;

/**
 * 提取并设置 SettlementIncomeData 实体的数据
 */
final class SettlementIncomeDataExtractor
{
    /**
     * @param array<string, mixed> $result
     * @param array<string, mixed> $item
     * @param array<string, mixed> $value
     */
    public function populate(SettlementIncomeData $entity, array $result, array $item, array $value): void
    {
        $this->setGlobalStatistics($entity, $result);
        $this->setSettlementInfo($entity, $item);
        $this->setSlotRevenue($entity, $value);
    }

    /**
     * @param array<string, mixed> $result
     */
    private function setGlobalStatistics(SettlementIncomeData $entity, array $result): void
    {
        $entity->setBody($this->extractString($result, 'body'));
        $entity->setPenaltyAll($this->extractInt($result, 'penalty_all'));
        $entity->setRevenueAll($this->extractInt($result, 'revenue_all'));
        $entity->setSettledRevenueAll($this->extractInt($result, 'settled_revenue_all'));
    }

    /**
     * @param array<string, mixed> $item
     */
    private function setSettlementInfo(SettlementIncomeData $entity, array $item): void
    {
        $entity->setZone($this->extractString($item, 'zone'));
        $entity->setMonth($this->extractString($item, 'month'));
        $entity->setSettledRevenue($this->extractInt($item, 'settled_revenue'));
        $entity->setSettNo($this->extractString($item, 'sett_no'));
        $entity->setMailSendCnt($this->extractMailSendCnt($item));

        $this->setOrderType($entity, $item);
        $this->setSettlementStatus($entity, $item);
    }

    /**
     * @param array<string, mixed> $value
     */
    private function setSlotRevenue(SettlementIncomeData $entity, array $value): void
    {
        $entity->setSlotSettledRevenue($this->extractInt($value, 'slot_settled_revenue'));
    }

    /**
     * @param array<string, mixed> $item
     */
    private function setOrderType(SettlementIncomeData $entity, array $item): void
    {
        $order = SettlementIncomeOrderTypeEnum::tryFrom($this->extractInt($item, 'order'));
        if (null !== $order) {
            $entity->setOrder($order);
        }
    }

    /**
     * @param array<string, mixed> $item
     */
    private function setSettlementStatus(SettlementIncomeData $entity, array $item): void
    {
        $settStatus = SettlementIncomeOrderStatusEnum::tryFrom($this->extractInt($item, 'sett_status'));
        if (null !== $settStatus) {
            $entity->setSettStatus($settStatus);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function extractString(array $data, string $key): string
    {
        return isset($data[$key]) && is_string($data[$key]) ? $data[$key] : '';
    }

    /**
     * @param array<string, mixed> $data
     */
    private function extractInt(array $data, string $key): int
    {
        return isset($data[$key]) && is_numeric($data[$key]) ? (int) $data[$key] : 0;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function extractMailSendCnt(array $data): ?string
    {
        if (!isset($data['mail_send_cnt'])) {
            return null;
        }

        return is_numeric($data['mail_send_cnt']) ? (string) $data['mail_send_cnt'] : null;
    }
}
