<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountStatsBundle\Entity\SettlementIncomeData;

/**
 * @internal
 */
#[CoversClass(SettlementIncomeData::class)]
final class SettlementIncomeDataTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new SettlementIncomeData();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            // 只测试可以安全测试的简单属性
            'body' => ['body', '测试主体'],
            'penaltyAll' => ['penaltyAll', 100],
            'revenueAll' => ['revenueAll', 1000],
        ];
    }

    private SettlementIncomeData $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new SettlementIncomeData();
    }

    /**
     * 测试默认ID值
     */
    public function testGetIdReturnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }
}
