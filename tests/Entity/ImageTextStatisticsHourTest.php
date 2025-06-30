<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatisticsHour;

class ImageTextStatisticsHourTest extends TestCase
{
    private ImageTextStatisticsHour $entity;

    protected function setUp(): void
    {
        $this->entity = new ImageTextStatisticsHour();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}