<?php

namespace WechatOfficialAccountStatsBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Enum\UserSummarySource;

class UserSummarySourceTest extends TestCase
{
    /**
     * 测试枚举值是否正确定义
     */
    public function testEnumValues_areDefinedCorrectly(): void
    {
        $this->assertSame(0, UserSummarySource::OTHER->value);
        $this->assertSame(1, UserSummarySource::SEARCH->value);
        $this->assertSame(17, UserSummarySource::CARD->value);
        $this->assertSame(30, UserSummarySource::SCAN_QRCODE->value);
        $this->assertSame(57, UserSummarySource::ARTICLE_ACCOUNT->value);
        $this->assertSame(100, UserSummarySource::AD->value);
        $this->assertSame(161, UserSummarySource::REPRINT->value);
        $this->assertSame(149, UserSummarySource::MP->value);
        $this->assertSame(200, UserSummarySource::VIDEO->value);
        $this->assertSame(201, UserSummarySource::LIVE->value);
    }

    /**
     * 测试枚举的标签方法是否正确返回
     */
    public function testGetLabel_returnsCorrectLabels(): void
    {
        $this->assertSame('其他合计', UserSummarySource::OTHER->getLabel());
        $this->assertSame('公众号搜索', UserSummarySource::SEARCH->getLabel());
        $this->assertSame('名片分享', UserSummarySource::CARD->getLabel());
        $this->assertSame('扫描二维码', UserSummarySource::SCAN_QRCODE->getLabel());
        $this->assertSame('文章内账号名称', UserSummarySource::ARTICLE_ACCOUNT->getLabel());
        $this->assertSame('微信广告', UserSummarySource::AD->getLabel());
        $this->assertSame('他人转载', UserSummarySource::REPRINT->getLabel());
        $this->assertSame('小程序关注', UserSummarySource::MP->getLabel());
        $this->assertSame('视频号', UserSummarySource::VIDEO->getLabel());
        $this->assertSame('直播', UserSummarySource::LIVE->getLabel());
    }

    /**
     * 测试 tryFrom 方法对有效值的处理
     */
    public function testTryFrom_withValidValue_returnsCorrectEnum(): void
    {
        $this->assertSame(UserSummarySource::OTHER, UserSummarySource::tryFrom(0));
        $this->assertSame(UserSummarySource::SEARCH, UserSummarySource::tryFrom(1));
        $this->assertSame(UserSummarySource::CARD, UserSummarySource::tryFrom(17));
        $this->assertSame(UserSummarySource::SCAN_QRCODE, UserSummarySource::tryFrom(30));
        $this->assertSame(UserSummarySource::ARTICLE_ACCOUNT, UserSummarySource::tryFrom(57));
        $this->assertSame(UserSummarySource::AD, UserSummarySource::tryFrom(100));
        $this->assertSame(UserSummarySource::REPRINT, UserSummarySource::tryFrom(161));
        $this->assertSame(UserSummarySource::MP, UserSummarySource::tryFrom(149));
        $this->assertSame(UserSummarySource::VIDEO, UserSummarySource::tryFrom(200));
        $this->assertSame(UserSummarySource::LIVE, UserSummarySource::tryFrom(201));
    }

    /**
     * 测试 tryFrom 方法对无效值的处理
     */
    public function testTryFrom_withInvalidValue_returnsNull(): void
    {
        $this->assertNull(UserSummarySource::tryFrom(999));
        $this->assertNull(UserSummarySource::tryFrom(-1));
    }
}
