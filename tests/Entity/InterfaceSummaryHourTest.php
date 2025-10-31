<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummaryHour;

/**
 * @internal
 */
#[CoversClass(InterfaceSummaryHour::class)]
final class InterfaceSummaryHourTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new InterfaceSummaryHour();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            // 只测试可以安全测试的简单属性
            'refHour' => ['refHour', 10],
            'callbackCount' => ['callbackCount', 50],
            'failCount' => ['failCount', 2],
        ];
    }

    private InterfaceSummaryHour $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new InterfaceSummaryHour();
    }

    /**
     * 测试默认ID值
     */
    public function testGetIdReturnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }
}
