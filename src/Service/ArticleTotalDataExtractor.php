<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Service;

use WechatOfficialAccountStatsBundle\Entity\ArticleTotal;

/**
 * 提取并设置 ArticleTotal 实体的数据
 */
final class ArticleTotalDataExtractor
{
    /**
     * @param array<string, mixed> $item
     * @param array<string, mixed> $detailValue
     */
    public function populate(ArticleTotal $articleTotal, array $item, array $detailValue): void
    {
        $this->setBasicInfo($articleTotal, $item);
        $this->setReadStatistics($articleTotal, $detailValue);
        $this->setSocialStatistics($articleTotal, $detailValue);
        $this->setReadSourceStatistics($articleTotal, $detailValue);
        $this->setShareSourceStatistics($articleTotal, $detailValue);
    }

    /**
     * @param array<string, mixed> $item
     */
    private function setBasicInfo(ArticleTotal $articleTotal, array $item): void
    {
        $articleTotal->setMsgId($this->extractString($item, 'msgId'));
        $articleTotal->setTitle($this->extractString($item, 'title'));
    }

    /**
     * @param array<string, mixed> $detailValue
     */
    private function setReadStatistics(ArticleTotal $articleTotal, array $detailValue): void
    {
        $articleTotal->setTargetUser($this->extractInt($detailValue, 'target_user'));
        $articleTotal->setIntPageReadUser($this->extractInt($detailValue, 'int_page_read_user'));
        $articleTotal->setIntPageReadCount($this->extractInt($detailValue, 'int_page_read_count'));
        $articleTotal->setOriPageReadUser($this->extractInt($detailValue, 'ori_page_read_user'));
        $articleTotal->setOriPageReadCount($this->extractInt($detailValue, 'ori_page_read_count'));
    }

    /**
     * @param array<string, mixed> $detailValue
     */
    private function setSocialStatistics(ArticleTotal $articleTotal, array $detailValue): void
    {
        $articleTotal->setShareUser($this->extractInt($detailValue, 'share_user'));
        $articleTotal->setShareCount($this->extractInt($detailValue, 'share_count'));
        $articleTotal->setAddToFavUser($this->extractInt($detailValue, 'add_to_fav_user'));
        $articleTotal->setAddToFavCount($this->extractInt($detailValue, 'add_to_fav_count'));
    }

    /**
     * @param array<string, mixed> $detailValue
     */
    private function setReadSourceStatistics(ArticleTotal $articleTotal, array $detailValue): void
    {
        $articleTotal->setIntPageFromSessionReadUser($this->extractInt($detailValue, 'int_page_from_session_read_user'));
        $articleTotal->setIntPageFromSessionReadCount($this->extractInt($detailValue, 'int_page_from_session_read_count'));
        $articleTotal->setIntPageFromHistMsgReadUser($this->extractInt($detailValue, 'int_page_from_hist_msg_read_user'));
        $articleTotal->setIntPageFromHistMsgReadCount($this->extractInt($detailValue, 'int_page_from_hist_msg_read_count'));
        $articleTotal->setIntPageFromFeedReadUser($this->extractInt($detailValue, 'int_page_from_feed_read_user'));
        $articleTotal->setIntPageFromFeedReadCount($this->extractInt($detailValue, 'int_page_from_feed_read_count'));
        $articleTotal->setIntPageFromFriendsReadUser($this->extractInt($detailValue, 'int_page_from_friends_read_user'));
        $articleTotal->setIntPageFromFriendsReadCount($this->extractInt($detailValue, 'int_page_from_friends_read_count'));
        $articleTotal->setIntPageFromOtherReadUser($this->extractInt($detailValue, 'int_page_from_other_read_user'));
        $articleTotal->setIntPageFromOtherReadCount($this->extractInt($detailValue, 'int_page_from_other_read_count'));
    }

    /**
     * @param array<string, mixed> $detailValue
     */
    private function setShareSourceStatistics(ArticleTotal $articleTotal, array $detailValue): void
    {
        $articleTotal->setFeedShareFromSessionUser($this->extractInt($detailValue, 'feed_share_from_session_user'));
        $articleTotal->setFeedShareFromSessionCnt($this->extractInt($detailValue, 'feed_share_from_session_cnt'));
        $articleTotal->setFeedShareFromFeedUser($this->extractInt($detailValue, 'feed_share_from_feed_user'));
        $articleTotal->setFeedShareFromFeedCnt($this->extractInt($detailValue, 'feed_share_from_feed_cnt'));
        $articleTotal->setFeedShareFromOtherUser($this->extractInt($detailValue, 'feed_share_from_other_user'));
        $articleTotal->setFeedShareFromOtherCnt($this->extractInt($detailValue, 'feed_share_from_other_cnt'));
    }

    /**
     * @param array<string, mixed> $data
     */
    private function extractString(array $data, string $key): string
    {
        return isset($data[$key]) && is_string($data[$key]) ? $data[$key] : '';
    }

    /**
     * @param array<string, mixed> $data
     */
    private function extractInt(array $data, string $key): int
    {
        return isset($data[$key]) && is_numeric($data[$key]) ? (int) $data[$key] : 0;
    }
}
