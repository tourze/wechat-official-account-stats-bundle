<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountStatsBundle\Entity\ArticleTotal;

/**
 * @internal
 */
#[CoversClass(ArticleTotal::class)]
final class ArticleTotalTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ArticleTotal();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            // 只测试可以安全测试的简单属性
            'msgId' => ['msgId', 'test_msg_id'],
            'title' => ['title', '测试标题'],
            'targetUser' => ['targetUser', 1000],
        ];
    }

    private ArticleTotal $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new ArticleTotal();
    }

    /**
     * 测试默认ID值
     */
    public function testGetIdReturnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }
}
