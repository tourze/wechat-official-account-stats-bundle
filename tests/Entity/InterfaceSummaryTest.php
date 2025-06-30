<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummary;

class InterfaceSummaryTest extends TestCase
{
    private InterfaceSummary $entity;

    protected function setUp(): void
    {
        $this->entity = new InterfaceSummary();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}