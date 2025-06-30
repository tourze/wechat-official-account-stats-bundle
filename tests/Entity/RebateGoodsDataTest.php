<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\RebateGoodsData;

class RebateGoodsDataTest extends TestCase
{
    private RebateGoodsData $entity;

    protected function setUp(): void
    {
        $this->entity = new RebateGoodsData();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}