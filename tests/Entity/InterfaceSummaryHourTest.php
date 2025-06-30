<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummaryHour;

class InterfaceSummaryHourTest extends TestCase
{
    private InterfaceSummaryHour $entity;

    protected function setUp(): void
    {
        $this->entity = new InterfaceSummaryHour();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}