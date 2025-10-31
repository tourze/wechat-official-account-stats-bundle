<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatOfficialAccountStatsBundle\Enum\ImageTextUserSourceEnum;

/**
 * @internal
 */
#[CoversClass(ImageTextUserSourceEnum::class)]
final class ImageTextUserSourceEnumTest extends AbstractEnumTestCase
{
    public function testTraitMethodsExist(): void
    {
        $reflection = new \ReflectionClass(ImageTextUserSourceEnum::class);
        $this->assertTrue($reflection->hasMethod('toSelectItem'));
        $this->assertTrue($reflection->hasMethod('toArray'));
        $this->assertTrue($reflection->hasMethod('genOptions'));
    }

    public function testToArray(): void
    {
        $array = ImageTextUserSourceEnum::ALL->toArray();
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame(99999999, $array['value']);
        $this->assertSame('全部', $array['label']);
    }

    public function testGenOptions(): void
    {
        $options = ImageTextUserSourceEnum::genOptions();
        $this->assertCount(8, $options);

        // 检查第一个选项的结构
        $firstOption = $options[0];
        $this->assertArrayHasKey('label', $firstOption);
        $this->assertArrayHasKey('value', $firstOption);
        $this->assertSame('全部', $firstOption['label']);
        $this->assertSame(99999999, $firstOption['value']);
    }
}
