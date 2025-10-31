<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;

/**
 * @internal
 */
#[CoversClass(MessageSendDataTypeEnum::class)]
final class MessageSendDataTypeEnumTest extends AbstractEnumTestCase
{
    public function testTraitMethodsExist(): void
    {
        $reflection = new \ReflectionClass(MessageSendDataTypeEnum::class);
        $this->assertTrue($reflection->hasMethod('toSelectItem'));
        $this->assertTrue($reflection->hasMethod('toArray'));
        $this->assertTrue($reflection->hasMethod('genOptions'));
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

    public function testToArray(): void
    {
        $array = MessageSendDataTypeEnum::TEXT->toArray();
        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('value', $array);
        $this->assertSame('文字', $array['label']);
        $this->assertSame(1, $array['value']);

        $array2 = MessageSendDataTypeEnum::IMAGE->toArray();
        $this->assertSame('图片', $array2['label']);
        $this->assertSame(2, $array2['value']);
    }
}
