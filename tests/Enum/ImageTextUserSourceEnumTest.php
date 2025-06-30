<?php

namespace WechatOfficialAccountStatsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Enum\ImageTextUserSourceEnum;

class ImageTextUserSourceEnumTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(99999999, ImageTextUserSourceEnum::ALL->value);
        $this->assertSame(0, ImageTextUserSourceEnum::CONVERSATION->value);
        $this->assertSame(1, ImageTextUserSourceEnum::FRIENDS->value);
        $this->assertSame(2, ImageTextUserSourceEnum::MOMENTS->value);
        $this->assertSame(4, ImageTextUserSourceEnum::HISTORICAL_MESSAGES_PAGE->value);
        $this->assertSame(5, ImageTextUserSourceEnum::OTHER->value);
        $this->assertSame(6, ImageTextUserSourceEnum::VIEW_AND_DISCOVER->value);
        $this->assertSame(7, ImageTextUserSourceEnum::SEARCH->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('全部', ImageTextUserSourceEnum::ALL->getLabel());
        $this->assertSame('会话', ImageTextUserSourceEnum::CONVERSATION->getLabel());
        $this->assertSame('好友', ImageTextUserSourceEnum::FRIENDS->getLabel());
        $this->assertSame('朋友圈', ImageTextUserSourceEnum::MOMENTS->getLabel());
        $this->assertSame('历史消息页', ImageTextUserSourceEnum::HISTORICAL_MESSAGES_PAGE->getLabel());
        $this->assertSame('其他', ImageTextUserSourceEnum::OTHER->getLabel());
        $this->assertSame('看一看', ImageTextUserSourceEnum::VIEW_AND_DISCOVER->getLabel());
        $this->assertSame('搜一搜', ImageTextUserSourceEnum::SEARCH->getLabel());
    }

    public function testTraitMethodsExist(): void
    {
        $reflection = new \ReflectionClass(ImageTextUserSourceEnum::class);
        $this->assertTrue($reflection->hasMethod('toSelectItem'));
        $this->assertTrue($reflection->hasMethod('toArray'));
        $this->assertTrue($reflection->hasMethod('genOptions'));
    }

    public function testToSelectItem(): void
    {
        $item = ImageTextUserSourceEnum::ALL->toSelectItem();
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('label', $item);
        $this->assertSame(99999999, $item['value']);
        $this->assertSame('全部', $item['label']);
    }

    public function testToArray(): void
    {
        $array = ImageTextUserSourceEnum::ALL->toArray();
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame(99999999, $array['value']);
        $this->assertSame('全部', $array['label']);
    }

    public function testGenOptions(): void
    {
        $options = ImageTextUserSourceEnum::genOptions();
        $this->assertCount(8, $options);
        
        // 检查第一个选项的结构
        $firstOption = $options[0];
        $this->assertArrayHasKey('label', $firstOption);
        $this->assertArrayHasKey('value', $firstOption);
        $this->assertSame('全部', $firstOption['label']);
        $this->assertSame(99999999, $firstOption['value']);
    }
}