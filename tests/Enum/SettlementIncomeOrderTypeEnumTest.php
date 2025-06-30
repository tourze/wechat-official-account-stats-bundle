<?php

namespace WechatOfficialAccountStatsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderTypeEnum;

class SettlementIncomeOrderTypeEnumTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(1, SettlementIncomeOrderTypeEnum::FIRST_HALF_OF_MONTH->value);
        $this->assertSame(2, SettlementIncomeOrderTypeEnum::SECOND_HALF_OF_MONTH->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('上半月', SettlementIncomeOrderTypeEnum::FIRST_HALF_OF_MONTH->getLabel());
        $this->assertSame('下半月', SettlementIncomeOrderTypeEnum::SECOND_HALF_OF_MONTH->getLabel());
    }

    public function testTraitMethodsExist(): void
    {
        $reflection = new \ReflectionClass(SettlementIncomeOrderTypeEnum::class);
        $this->assertTrue($reflection->hasMethod('toSelectItem'));
        $this->assertTrue($reflection->hasMethod('toArray'));
        $this->assertTrue($reflection->hasMethod('genOptions'));
    }

    public function testToSelectItem(): void
    {
        $item = SettlementIncomeOrderTypeEnum::FIRST_HALF_OF_MONTH->toSelectItem();
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('label', $item);
        $this->assertSame(1, $item['value']);
        $this->assertSame('上半月', $item['label']);
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
}