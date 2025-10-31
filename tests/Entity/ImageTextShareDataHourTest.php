<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareDataHour;

/**
 * @internal
 */
#[CoversClass(ImageTextShareDataHour::class)]
final class ImageTextShareDataHourTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ImageTextShareDataHour();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            // 只测试可以安全测试的简单属性
            'refHour' => ['refHour', 10],
            'shareScene' => ['shareScene', 1],
            'shareCount' => ['shareCount', 50],
            'shareUser' => ['shareUser', 25],
        ];
    }

    private ImageTextShareDataHour $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new ImageTextShareDataHour();
    }

    /**
     * 测试默认ID值
     */
    public function testGetIdReturnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }
}
