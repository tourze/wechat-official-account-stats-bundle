<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareData;

class ImageTextShareDataTest extends TestCase
{
    private ImageTextShareData $entity;

    protected function setUp(): void
    {
        $this->entity = new ImageTextShareData();
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsDefaultValue(): void
    {
        $this->assertSame(0, $this->entity->getId());
    }

}