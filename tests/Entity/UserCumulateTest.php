<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountStatsBundle\Entity\UserCumulate;

/**
 * @internal
 */
#[CoversClass(UserCumulate::class)]
final class UserCumulateTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new UserCumulate();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            // 只测试可以安全测试的简单属性
            'cumulateUser' => ['cumulateUser', 1000],
        ];
    }

    private UserCumulate $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new UserCumulate();
    }

    /**
     * 测试默认ID值
     */
    public function testGetIdReturnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }
}
