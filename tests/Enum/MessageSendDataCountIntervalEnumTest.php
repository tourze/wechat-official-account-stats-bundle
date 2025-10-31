<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataCountIntervalEnum;

/**
 * @internal
 */
#[CoversClass(MessageSendDataCountIntervalEnum::class)]
final class MessageSendDataCountIntervalEnumTest extends AbstractEnumTestCase
{
    public function testTraitMethodsExist(): void
    {
        $reflection = new \ReflectionClass(MessageSendDataCountIntervalEnum::class);
        $this->assertTrue($reflection->hasMethod('toSelectItem'));
        $this->assertTrue($reflection->hasMethod('toArray'));
        $this->assertTrue($reflection->hasMethod('genOptions'));
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

    public function testToArray(): void
    {
        $array = MessageSendDataCountIntervalEnum::ZERO->toArray();
        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('value', $array);
        $this->assertSame('0', $array['label']);
        $this->assertSame(0, $array['value']);

        $array2 = MessageSendDataCountIntervalEnum::ONE_TO_FIVE->toArray();
        $this->assertSame('1-5', $array2['label']);
        $this->assertSame(1, $array2['value']);
    }
}
