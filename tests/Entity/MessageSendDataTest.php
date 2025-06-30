<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\MessageSendData;

class MessageSendDataTest extends TestCase
{
    private MessageSendData $entity;

    protected function setUp(): void
    {
        $this->entity = new MessageSendData();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}