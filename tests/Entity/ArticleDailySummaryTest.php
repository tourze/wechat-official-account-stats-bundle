<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountStatsBundle\Entity\ArticleDailySummary;

/**
 * @internal
 */
#[CoversClass(ArticleDailySummary::class)]
final class ArticleDailySummaryTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ArticleDailySummary();
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
        ];
    }

    private ArticleDailySummary $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new ArticleDailySummary();
    }

    /**
     * 测试默认ID值
     */
    public function testGetIdReturnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }
}
