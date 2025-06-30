<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\ArticleTotal;

class ArticleTotalTest extends TestCase
{
    private ArticleTotal $entity;

    protected function setUp(): void
    {
        $this->entity = new ArticleTotal();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}