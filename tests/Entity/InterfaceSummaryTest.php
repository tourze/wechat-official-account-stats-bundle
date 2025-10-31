<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummary;

/**
 * @internal
 */
#[CoversClass(InterfaceSummary::class)]
final class InterfaceSummaryTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new InterfaceSummary();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            // 只测试可以安全测试的简单属性
            'callbackCount' => ['callbackCount', 100],
            'failCount' => ['failCount', 5],
            'totalTimeCost' => ['totalTimeCost', 1000],
        ];
    }

    private InterfaceSummary $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new InterfaceSummary();
    }

    /**
     * 测试默认ID值
     */
    public function testGetIdReturnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }
}
