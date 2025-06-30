<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\MessageSendWeekData;

class MessageSendWeekDataTest extends TestCase
{
    private MessageSendWeekData $entity;

    protected function setUp(): void
    {
        $this->entity = new MessageSendWeekData();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}