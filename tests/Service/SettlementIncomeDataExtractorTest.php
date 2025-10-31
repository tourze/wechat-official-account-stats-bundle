<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\SettlementIncomeData;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderStatusEnum;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderTypeEnum;
use WechatOfficialAccountStatsBundle\Service\SettlementIncomeDataExtractor;

/**
 * @internal
 */
#[CoversClass(SettlementIncomeDataExtractor::class)]
final class SettlementIncomeDataExtractorTest extends TestCase
{
    private SettlementIncomeDataExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new SettlementIncomeDataExtractor();
    }

    public function testPopulateWithCompleteData(): void
    {
        $entity = new SettlementIncomeData();

        $result = [
            'body' => 'Test Body',
            'penalty_all' => 100,
            'revenue_all' => 1000,
            'settled_revenue_all' => 900,
        ];

        $item = [
            'zone' => '2024-01-01 to 2024-01-15',
            'month' => '2024-01',
            'order' => 1,
            'sett_status' => 5,
            'settled_revenue' => 500,
            'sett_no' => 'SETT123456',
            'mail_send_cnt' => 2,
        ];

        $value = [
            'slot_settled_revenue' => 450,
        ];

        $this->extractor->populate($entity, $result, $item, $value);

        $this->assertSame('Test Body', $entity->getBody());
        $this->assertSame(100, $entity->getPenaltyAll());
        $this->assertSame(1000, $entity->getRevenueAll());
        $this->assertSame(900, $entity->getSettledRevenueAll());
        $this->assertSame('2024-01-01 to 2024-01-15', $entity->getZone());
        $this->assertSame('2024-01', $entity->getMonth());
        $this->assertSame(SettlementIncomeOrderTypeEnum::FIRST_HALF_OF_MONTH, $entity->getOrder());
        $this->assertSame(SettlementIncomeOrderStatusEnum::PAID, $entity->getSettStatus());
        $this->assertSame(500, $entity->getSettledRevenue());
        $this->assertSame('SETT123456', $entity->getSettNo());
        $this->assertSame('2', $entity->getMailSendCnt());
        $this->assertSame(450, $entity->getSlotSettledRevenue());
    }

    public function testPopulateWithEmptyData(): void
    {
        $entity = new SettlementIncomeData();

        $result = [];
        $item = [];
        $value = [];

        $this->extractor->populate($entity, $result, $item, $value);

        $this->assertSame('', $entity->getBody());
        $this->assertSame(0, $entity->getPenaltyAll());
        $this->assertSame(0, $entity->getRevenueAll());
        $this->assertSame(0, $entity->getSettledRevenueAll());
        $this->assertSame('', $entity->getZone());
        $this->assertSame('', $entity->getMonth());
        $this->assertNull($entity->getOrder());
        $this->assertNull($entity->getSettStatus());
        $this->assertSame(0, $entity->getSettledRevenue());
        $this->assertSame('', $entity->getSettNo());
        $this->assertNull($entity->getMailSendCnt());
        $this->assertSame(0, $entity->getSlotSettledRevenue());
    }

    #[DataProvider('invalidEnumDataProvider')]
    public function testPopulateWithInvalidEnumValues(int $invalidValue): void
    {
        $entity = new SettlementIncomeData();

        $result = [];
        $item = [
            'order' => $invalidValue,
            'sett_status' => $invalidValue,
        ];
        $value = [];

        $this->extractor->populate($entity, $result, $item, $value);

        $this->assertNull($entity->getOrder());
        $this->assertNull($entity->getSettStatus());
    }

    /**
     * @return array<string, array<int>>
     */
    public static function invalidEnumDataProvider(): array
    {
        return [
            'zero' => [0],
            'negative' => [-1],
            'out of range' => [999],
        ];
    }

    public function testPopulateWithValidEnumValues(): void
    {
        $entity = new SettlementIncomeData();

        $result = [];
        $item = [
            'order' => 2,
            'sett_status' => 3,
        ];
        $value = [];

        $this->extractor->populate($entity, $result, $item, $value);

        $this->assertSame(SettlementIncomeOrderTypeEnum::SECOND_HALF_OF_MONTH, $entity->getOrder());
        $this->assertSame(SettlementIncomeOrderStatusEnum::SETTLED_TWO, $entity->getSettStatus());
    }

    public function testPopulateWithMailSendCntAsNull(): void
    {
        $entity = new SettlementIncomeData();

        $result = [];
        $item = [];
        $value = [];

        $this->extractor->populate($entity, $result, $item, $value);

        $this->assertNull($entity->getMailSendCnt());
    }

    public function testPopulateWithMailSendCntAsNumeric(): void
    {
        $entity = new SettlementIncomeData();

        $result = [];
        $item = [
            'mail_send_cnt' => 5,
        ];
        $value = [];

        $this->extractor->populate($entity, $result, $item, $value);

        $this->assertSame('5', $entity->getMailSendCnt());
    }

    #[DataProvider('invalidDataProvider')]
    public function testPopulateWithInvalidData(mixed $invalidValue): void
    {
        $entity = new SettlementIncomeData();

        $result = [
            'body' => $invalidValue,
            'penalty_all' => $invalidValue,
            'revenue_all' => $invalidValue,
            'settled_revenue_all' => $invalidValue,
        ];

        $item = [
            'zone' => $invalidValue,
            'month' => $invalidValue,
            'settled_revenue' => $invalidValue,
            'sett_no' => $invalidValue,
        ];

        $value = [
            'slot_settled_revenue' => $invalidValue,
        ];

        $this->extractor->populate($entity, $result, $item, $value);

        $this->assertSame('', $entity->getBody());
        $this->assertSame(0, $entity->getPenaltyAll());
        $this->assertSame(0, $entity->getRevenueAll());
        $this->assertSame(0, $entity->getSettledRevenueAll());
        $this->assertSame('', $entity->getZone());
        $this->assertSame('', $entity->getMonth());
        $this->assertSame(0, $entity->getSettledRevenue());
        $this->assertSame('', $entity->getSettNo());
        $this->assertSame(0, $entity->getSlotSettledRevenue());
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function invalidDataProvider(): array
    {
        return [
            'null value' => [null],
            'boolean value' => [true],
            'array value' => [[]],
            'object value' => [new \stdClass()],
        ];
    }

    public function testPopulateWithNumericStrings(): void
    {
        $entity = new SettlementIncomeData();

        $result = [
            'body' => 'Body',
            'penalty_all' => '100',
            'revenue_all' => '1000',
            'settled_revenue_all' => '900',
        ];

        $item = [
            'zone' => 'Zone',
            'month' => 'Month',
            'settled_revenue' => '500',
            'sett_no' => 'NO123',
        ];

        $value = [
            'slot_settled_revenue' => '450',
        ];

        $this->extractor->populate($entity, $result, $item, $value);

        $this->assertSame('Body', $entity->getBody());
        $this->assertSame(100, $entity->getPenaltyAll());
        $this->assertSame(1000, $entity->getRevenueAll());
        $this->assertSame(900, $entity->getSettledRevenueAll());
        $this->assertSame('Zone', $entity->getZone());
        $this->assertSame('Month', $entity->getMonth());
        $this->assertSame(500, $entity->getSettledRevenue());
        $this->assertSame('NO123', $entity->getSettNo());
        $this->assertSame(450, $entity->getSlotSettledRevenue());
    }
}
