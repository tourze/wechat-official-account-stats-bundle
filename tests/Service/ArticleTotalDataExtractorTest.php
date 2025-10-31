<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Entity\ArticleTotal;
use WechatOfficialAccountStatsBundle\Service\ArticleTotalDataExtractor;

/**
 * @internal
 */
#[CoversClass(ArticleTotalDataExtractor::class)]
final class ArticleTotalDataExtractorTest extends TestCase
{
    private ArticleTotalDataExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new ArticleTotalDataExtractor();
    }

    public function testPopulateWithCompleteData(): void
    {
        $articleTotal = new ArticleTotal();

        $item = [
            'msgId' => 'test_msg_id',
            'title' => 'Test Article',
        ];

        $detailValue = [
            'target_user' => 100,
            'int_page_read_user' => 80,
            'int_page_read_count' => 120,
            'ori_page_read_user' => 50,
            'ori_page_read_count' => 60,
            'share_user' => 30,
            'share_count' => 40,
            'add_to_fav_user' => 20,
            'add_to_fav_count' => 25,
            'int_page_from_session_read_user' => 10,
            'int_page_from_session_read_count' => 15,
            'int_page_from_hist_msg_read_user' => 5,
            'int_page_from_hist_msg_read_count' => 8,
            'int_page_from_feed_read_user' => 12,
            'int_page_from_feed_read_count' => 18,
            'int_page_from_friends_read_user' => 7,
            'int_page_from_friends_read_count' => 10,
            'int_page_from_other_read_user' => 3,
            'int_page_from_other_read_count' => 5,
            'feed_share_from_session_user' => 8,
            'feed_share_from_session_cnt' => 12,
            'feed_share_from_feed_user' => 6,
            'feed_share_from_feed_cnt' => 9,
            'feed_share_from_other_user' => 4,
            'feed_share_from_other_cnt' => 6,
        ];

        $this->extractor->populate($articleTotal, $item, $detailValue);

        $this->assertSame('test_msg_id', $articleTotal->getMsgId());
        $this->assertSame('Test Article', $articleTotal->getTitle());
        $this->assertSame(100, $articleTotal->getTargetUser());
        $this->assertSame(80, $articleTotal->getIntPageReadUser());
        $this->assertSame(120, $articleTotal->getIntPageReadCount());
        $this->assertSame(50, $articleTotal->getOriPageReadUser());
        $this->assertSame(60, $articleTotal->getOriPageReadCount());
        $this->assertSame(30, $articleTotal->getShareUser());
        $this->assertSame(40, $articleTotal->getShareCount());
        $this->assertSame(20, $articleTotal->getAddToFavUser());
        $this->assertSame(25, $articleTotal->getAddToFavCount());
    }

    public function testPopulateWithEmptyData(): void
    {
        $articleTotal = new ArticleTotal();

        $item = [];
        $detailValue = [];

        $this->extractor->populate($articleTotal, $item, $detailValue);

        $this->assertSame('', $articleTotal->getMsgId());
        $this->assertSame('', $articleTotal->getTitle());
        $this->assertSame(0, $articleTotal->getTargetUser());
        $this->assertSame(0, $articleTotal->getIntPageReadUser());
        $this->assertSame(0, $articleTotal->getIntPageReadCount());
    }

    #[DataProvider('invalidDataProvider')]
    public function testPopulateWithInvalidData(mixed $value): void
    {
        $articleTotal = new ArticleTotal();

        $item = [
            'msgId' => $value,
            'title' => $value,
        ];

        $detailValue = [
            'target_user' => $value,
            'int_page_read_user' => $value,
            'int_page_read_count' => $value,
        ];

        $this->extractor->populate($articleTotal, $item, $detailValue);

        $this->assertSame('', $articleTotal->getMsgId());
        $this->assertSame('', $articleTotal->getTitle());
        $this->assertSame(0, $articleTotal->getTargetUser());
        $this->assertSame(0, $articleTotal->getIntPageReadUser());
        $this->assertSame(0, $articleTotal->getIntPageReadCount());
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function invalidDataProvider(): array
    {
        return [
            'null value' => [null],
            'boolean value' => [true],
            'array value' => [[]],
            'object value' => [new \stdClass()],
        ];
    }

    public function testPopulateWithNumericStrings(): void
    {
        $articleTotal = new ArticleTotal();

        $item = [
            'msgId' => '123',
            'title' => 'Test',
        ];

        $detailValue = [
            'target_user' => '100',
            'int_page_read_user' => '80',
            'int_page_read_count' => '120',
        ];

        $this->extractor->populate($articleTotal, $item, $detailValue);

        $this->assertSame('123', $articleTotal->getMsgId());
        $this->assertSame('Test', $articleTotal->getTitle());
        $this->assertSame(100, $articleTotal->getTargetUser());
        $this->assertSame(80, $articleTotal->getIntPageReadUser());
        $this->assertSame(120, $articleTotal->getIntPageReadCount());
    }
}
