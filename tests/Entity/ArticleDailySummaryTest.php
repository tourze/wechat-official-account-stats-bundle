<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\ArticleDailySummary;

class ArticleDailySummaryTest extends TestCase
{
    private ArticleDailySummary $entity;

    protected function setUp(): void
    {
        $this->entity = new ArticleDailySummary();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}