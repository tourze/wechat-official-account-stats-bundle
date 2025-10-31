<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderTypeEnum;

/**
 * @internal
 */
#[CoversClass(SettlementIncomeOrderTypeEnum::class)]
final class SettlementIncomeOrderTypeEnumTest extends AbstractEnumTestCase
{
    public function testTraitMethodsExist(): void
    {
        $reflection = new \ReflectionClass(SettlementIncomeOrderTypeEnum::class);
        $this->assertTrue($reflection->hasMethod('toSelectItem'));
        $this->assertTrue($reflection->hasMethod('toArray'));
        $this->assertTrue($reflection->hasMethod('genOptions'));
    }

    public function testGenOptions(): void
    {
        $options = SettlementIncomeOrderTypeEnum::genOptions();
        $this->assertCount(2, $options);

        // 检查第一个选项的结构
        $firstOption = $options[0];
        $this->assertArrayHasKey('label', $firstOption);
        $this->assertArrayHasKey('value', $firstOption);
        $this->assertSame('上半月', $firstOption['label']);
        $this->assertSame(1, $firstOption['value']);
    }

    public function testToArray(): void
    {
        $array = SettlementIncomeOrderTypeEnum::FIRST_HALF_OF_MONTH->toArray();
        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('value', $array);
        $this->assertSame('上半月', $array['label']);
        $this->assertSame(1, $array['value']);

        $array2 = SettlementIncomeOrderTypeEnum::SECOND_HALF_OF_MONTH->toArray();
        $this->assertSame('下半月', $array2['label']);
        $this->assertSame(2, $array2['value']);
    }
}
