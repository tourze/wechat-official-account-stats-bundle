<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountStatsBundle\Entity\MessageSenDistData;

/**
 * @internal
 */
#[CoversClass(MessageSenDistData::class)]
final class MessageSenDistDataTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new MessageSenDistData();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            // 只测试可以安全测试的简单属性
            'msgUser' => ['msgUser', 100],
        ];
    }

    private MessageSenDistData $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new MessageSenDistData();
    }

    /**
     * 测试默认ID值
     */
    public function testGetIdReturnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }
}
