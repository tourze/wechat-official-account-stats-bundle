<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountStatsBundle\Entity\MessageSendHourData;

/**
 * @internal
 */
#[CoversClass(MessageSendHourData::class)]
final class MessageSendHourDataTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new MessageSendHourData();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            // 只测试可以安全测试的简单属性
            'refHour' => ['refHour', 10],
            'msgUser' => ['msgUser', 50],
            'msgCount' => ['msgCount', 100],
        ];
    }

    private MessageSendHourData $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new MessageSendHourData();
    }

    /**
     * 测试默认ID值
     */
    public function testGetIdReturnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }
}
