<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\MessageSendHourData;

class MessageSendHourDataTest extends TestCase
{
    private MessageSendHourData $entity;

    protected function setUp(): void
    {
        $this->entity = new MessageSendHourData();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}