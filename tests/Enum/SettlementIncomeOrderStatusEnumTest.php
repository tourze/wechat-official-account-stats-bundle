<?php

namespace WechatOfficialAccountStatsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderStatusEnum;

class SettlementIncomeOrderStatusEnumTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(1, SettlementIncomeOrderStatusEnum::SETTLING->value);
        $this->assertSame(2, SettlementIncomeOrderStatusEnum::SETTLED->value);
        $this->assertSame(3, SettlementIncomeOrderStatusEnum::SETTLED_TWO->value);
        $this->assertSame(4, SettlementIncomeOrderStatusEnum::PAYMENT_PENDING->value);
        $this->assertSame(5, SettlementIncomeOrderStatusEnum::PAID->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('结算中', SettlementIncomeOrderStatusEnum::SETTLING->getLabel());
        $this->assertSame('已结算', SettlementIncomeOrderStatusEnum::SETTLED->getLabel());
        $this->assertSame('已结算', SettlementIncomeOrderStatusEnum::SETTLED_TWO->getLabel());
        $this->assertSame('付款中', SettlementIncomeOrderStatusEnum::PAYMENT_PENDING->getLabel());
        $this->assertSame('已付款', SettlementIncomeOrderStatusEnum::PAID->getLabel());
    }

    public function testTraitMethodsExist(): void
    {
        $reflection = new \ReflectionClass(SettlementIncomeOrderStatusEnum::class);
        $this->assertTrue($reflection->hasMethod('toSelectItem'));
        $this->assertTrue($reflection->hasMethod('toArray'));
        $this->assertTrue($reflection->hasMethod('genOptions'));
    }

    public function testToSelectItem(): void
    {
        $item = SettlementIncomeOrderStatusEnum::SETTLING->toSelectItem();
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('label', $item);
        $this->assertSame(1, $item['value']);
        $this->assertSame('结算中', $item['label']);
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
}