<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatistics;

class ImageTextStatisticsTest extends TestCase
{
    private ImageTextStatistics $entity;

    protected function setUp(): void
    {
        $this->entity = new ImageTextStatistics();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}