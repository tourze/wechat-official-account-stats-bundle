<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderStatusEnum;

/**
 * @internal
 */
#[CoversClass(SettlementIncomeOrderStatusEnum::class)]
final class SettlementIncomeOrderStatusEnumTest extends AbstractEnumTestCase
{
    public function testTraitMethodsExist(): void
    {
        $reflection = new \ReflectionClass(SettlementIncomeOrderStatusEnum::class);
        $this->assertTrue($reflection->hasMethod('toSelectItem'));
        $this->assertTrue($reflection->hasMethod('toArray'));
        $this->assertTrue($reflection->hasMethod('genOptions'));
    }

    public function testGenOptions(): void
    {
        $options = SettlementIncomeOrderStatusEnum::genOptions();
        $this->assertCount(5, $options);

        // 检查第一个选项的结构
        $firstOption = $options[0];
        $this->assertArrayHasKey('label', $firstOption);
        $this->assertArrayHasKey('value', $firstOption);
        $this->assertSame('结算中', $firstOption['label']);
        $this->assertSame(1, $firstOption['value']);
    }

    public function testToArray(): void
    {
        $array = SettlementIncomeOrderStatusEnum::SETTLING->toArray();
        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('value', $array);
        $this->assertSame('结算中', $array['label']);
        $this->assertSame(1, $array['value']);

        $array2 = SettlementIncomeOrderStatusEnum::SETTLED->toArray();
        $this->assertSame('已结算', $array2['label']);
        $this->assertSame(2, $array2['value']);
    }
}
