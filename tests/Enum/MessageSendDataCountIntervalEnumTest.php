<?php

namespace WechatOfficialAccountStatsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataCountIntervalEnum;

class MessageSendDataCountIntervalEnumTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, MessageSendDataCountIntervalEnum::ZERO->value);
        $this->assertSame(1, MessageSendDataCountIntervalEnum::ONE_TO_FIVE->value);
        $this->assertSame(2, MessageSendDataCountIntervalEnum::SIX_TO_TEN->value);
        $this->assertSame(3, MessageSendDataCountIntervalEnum::MORE_THAN_TEN->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('0', MessageSendDataCountIntervalEnum::ZERO->getLabel());
        $this->assertSame('1-5', MessageSendDataCountIntervalEnum::ONE_TO_FIVE->getLabel());
        $this->assertSame('6-10', MessageSendDataCountIntervalEnum::SIX_TO_TEN->getLabel());
        $this->assertSame('10次以上', MessageSendDataCountIntervalEnum::MORE_THAN_TEN->getLabel());
    }

    public function testTraitMethodsExist(): void
    {
        $reflection = new \ReflectionClass(MessageSendDataCountIntervalEnum::class);
        $this->assertTrue($reflection->hasMethod('toSelectItem'));
        $this->assertTrue($reflection->hasMethod('toArray'));
        $this->assertTrue($reflection->hasMethod('genOptions'));
    }

    public function testToSelectItem(): void
    {
        $item = MessageSendDataCountIntervalEnum::ZERO->toSelectItem();
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('label', $item);
        $this->assertSame(0, $item['value']);
        $this->assertSame('0', $item['label']);
    }

    public function testGenOptions(): void
    {
        $options = MessageSendDataCountIntervalEnum::genOptions();
        $this->assertCount(4, $options);
        
        // 检查第一个选项的结构
        $firstOption = $options[0];
        $this->assertArrayHasKey('label', $firstOption);
        $this->assertArrayHasKey('value', $firstOption);
        $this->assertSame('0', $firstOption['label']);
        $this->assertSame(0, $firstOption['value']);
    }
}