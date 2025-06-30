<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\MessageSenDistData;

class MessageSenDistDataTest extends TestCase
{
    private MessageSenDistData $entity;

    protected function setUp(): void
    {
        $this->entity = new MessageSenDistData();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}