<?php

namespace WechatOfficialAccountStatsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;

class MessageSendDataTypeEnumTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(1, MessageSendDataTypeEnum::TEXT->value);
        $this->assertSame(2, MessageSendDataTypeEnum::IMAGE->value);
        $this->assertSame(3, MessageSendDataTypeEnum::AUDIO->value);
        $this->assertSame(4, MessageSendDataTypeEnum::VIDEO->value);
        $this->assertSame(5, MessageSendDataTypeEnum::THIRD_PARTY_APP_MESSAGE->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('文字', MessageSendDataTypeEnum::TEXT->getLabel());
        $this->assertSame('图片', MessageSendDataTypeEnum::IMAGE->getLabel());
        $this->assertSame('语音', MessageSendDataTypeEnum::AUDIO->getLabel());
        $this->assertSame('视频', MessageSendDataTypeEnum::VIDEO->getLabel());
        $this->assertSame('第三方应用消息', MessageSendDataTypeEnum::THIRD_PARTY_APP_MESSAGE->getLabel());
    }

    public function testTraitMethodsExist(): void
    {
        $reflection = new \ReflectionClass(MessageSendDataTypeEnum::class);
        $this->assertTrue($reflection->hasMethod('toSelectItem'));
        $this->assertTrue($reflection->hasMethod('toArray'));
        $this->assertTrue($reflection->hasMethod('genOptions'));
    }

    public function testToSelectItem(): void
    {
        $item = MessageSendDataTypeEnum::TEXT->toSelectItem();
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('label', $item);
        $this->assertSame(1, $item['value']);
        $this->assertSame('文字', $item['label']);
    }

    public function testGenOptions(): void
    {
        $options = MessageSendDataTypeEnum::genOptions();
        $this->assertCount(5, $options);
        
        // 检查第一个选项的结构
        $firstOption = $options[0];
        $this->assertArrayHasKey('label', $firstOption);
        $this->assertArrayHasKey('value', $firstOption);
        $this->assertSame('文字', $firstOption['label']);
        $this->assertSame(1, $firstOption['value']);
    }
}