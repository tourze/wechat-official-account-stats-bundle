<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountStatsBundle\Entity\MessageSendWeekData;

/**
 * @internal
 */
#[CoversClass(MessageSendWeekData::class)]
final class MessageSendWeekDataTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new MessageSendWeekData();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            // 只测试可以安全测试的简单属性
            'msgUser' => ['msgUser', 500],
            'msgCount' => ['msgCount', 1000],
        ];
    }

    private MessageSendWeekData $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new MessageSendWeekData();
    }

    /**
     * 测试默认ID值
     */
    public function testGetIdReturnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }
}
