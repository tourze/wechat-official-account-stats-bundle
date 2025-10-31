<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountStatsBundle\Entity\MessageSendMonthData;

/**
 * @internal
 */
#[CoversClass(MessageSendMonthData::class)]
final class MessageSendMonthDataTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new MessageSendMonthData();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            // 只测试可以安全测试的简单属性
            'msgUser' => ['msgUser', 1000],
            'msgCount' => ['msgCount', 2000],
        ];
    }

    private MessageSendMonthData $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new MessageSendMonthData();
    }

    /**
     * 测试默认ID值
     */
    public function testGetIdReturnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }
}
