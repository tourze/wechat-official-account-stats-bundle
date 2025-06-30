<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\MessageSendMonthData;

class MessageSendMonthDataTest extends TestCase
{
    private MessageSendMonthData $entity;

    protected function setUp(): void
    {
        $this->entity = new MessageSendMonthData();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}