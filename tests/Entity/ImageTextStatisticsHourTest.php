<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatisticsHour;

/**
 * @internal
 */
#[CoversClass(ImageTextStatisticsHour::class)]
final class ImageTextStatisticsHourTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ImageTextStatisticsHour();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            // 只测试可以安全测试的简单属性
            'refHour' => ['refHour', 10],
            'intPageReadUser' => ['intPageReadUser', 100],
            'intPageReadCount' => ['intPageReadCount', 200],
        ];
    }

    private ImageTextStatisticsHour $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new ImageTextStatisticsHour();
    }

    /**
     * 测试默认ID值
     */
    public function testGetIdReturnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }
}
