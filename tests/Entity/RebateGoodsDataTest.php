<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountStatsBundle\Entity\RebateGoodsData;

/**
 * @internal
 */
#[CoversClass(RebateGoodsData::class)]
final class RebateGoodsDataTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new RebateGoodsData();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            // 只测试可以安全测试的简单属性
            'exposureCount' => ['exposureCount', 1000],
            'clickCount' => ['clickCount', 100],
            'orderCount' => ['orderCount', 10],
        ];
    }

    private RebateGoodsData $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new RebateGoodsData();
    }

    /**
     * 测试默认ID值
     */
    public function testGetIdReturnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }
}
