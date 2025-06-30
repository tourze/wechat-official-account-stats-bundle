<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareDataHour;

class ImageTextShareDataHourTest extends TestCase
{
    private ImageTextShareDataHour $entity;

    protected function setUp(): void
    {
        $this->entity = new ImageTextShareDataHour();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}