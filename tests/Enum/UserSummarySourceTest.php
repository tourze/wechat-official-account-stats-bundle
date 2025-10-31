<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatOfficialAccountStatsBundle\Enum\UserSummarySource;

/**
 * @internal
 */
#[CoversClass(UserSummarySource::class)]
final class UserSummarySourceTest extends AbstractEnumTestCase
{
    /**
     * 测试 tryFrom 方法对有效值的处理
     */
    public function testTryFromWithValidValueReturnsCorrectEnum(): void
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

    public function testToArray(): void
    {
        $array = UserSummarySource::OTHER->toArray();
        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('value', $array);
        $this->assertSame('其他合计', $array['label']);
        $this->assertSame(0, $array['value']);

        $array2 = UserSummarySource::SEARCH->toArray();
        $this->assertSame('公众号搜索', $array2['label']);
        $this->assertSame(1, $array2['value']);
    }
}
