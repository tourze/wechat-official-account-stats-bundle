<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\SettlementIncomeData;

class SettlementIncomeDataTest extends TestCase
{
    private SettlementIncomeData $entity;

    protected function setUp(): void
    {
        $this->entity = new SettlementIncomeData();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}