<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\UserCumulate;

class UserCumulateTest extends TestCase
{
    private UserCumulate $entity;

    protected function setUp(): void
    {
        $this->entity = new UserCumulate();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}