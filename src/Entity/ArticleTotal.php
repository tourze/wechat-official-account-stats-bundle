<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Repository\ArticleTotalRepository;

#[ORM\Entity(repositoryClass: ArticleTotalRepository::class)]
#[ORM\Table(name: 'wechat_official_article_total', options: ['comment' => '获取图文群发总数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_article_total_uniq', columns: ['account_id', 'date', 'stat_date'])]
class ArticleTotal implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    public function getId(): int
    {
        return $this->id;
    }

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Account $account;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['comment' => '日期'])]
    #[Assert\NotNull]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '这里的msgid实际上是由msgid和index组成'])]
    #[Assert\Length(max: 60)]
    private ?string $msgId = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '图文消息的标题'])]
    #[Assert\Length(max: 60)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['comment' => '统计的日期，在getarticletotal接口中，ref_date指的是文章群发出日期， 而stat_date是数据统计日期'])]
    #[Assert\NotNull]
    private ?\DateTimeInterface $statDate = null;

    #[ORM\Column(nullable: true, options: ['comment' => '送达人数，一般约等于总粉丝数（需排除黑名单或其他异常情况下无法收到消息的粉丝）'])]
    #[Assert\PositiveOrZero]
    private ?int $targetUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '图文页（点击群发图文卡片进入的页面）的阅读人数'])]
    #[Assert\PositiveOrZero]
    private ?int $intPageReadUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '图文页的阅读次数'])]
    #[Assert\PositiveOrZero]
    private ?int $intPageReadCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '原文页（点击图文页“阅读原文”进入的页面）的阅读人数，无原文页时此处数据为0'])]
    #[Assert\PositiveOrZero]
    private ?int $oriPageReadUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '原文页的阅读次数'])]
    #[Assert\PositiveOrZero]
    private ?int $oriPageReadCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '分享的人数'])]
    #[Assert\PositiveOrZero]
    private ?int $shareUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '分享的次数'])]
    #[Assert\PositiveOrZero]
    private ?int $shareCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '收藏的人数'])]
    #[Assert\PositiveOrZero]
    private ?int $addToFavUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '收藏的次数'])]
    #[Assert\PositiveOrZero]
    private ?int $addToFavCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从会话中读取的用户数量'])]
    #[Assert\PositiveOrZero]
    private ?int $intPageFromSessionReadUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从会话中读取的总数'])]
    #[Assert\PositiveOrZero]
    private ?int $intPageFromSessionReadCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从历史消息中读取的用户数量'])]
    #[Assert\PositiveOrZero]
    private ?int $intPageFromHistMsgReadUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '历史消息中读取的总数'])]
    #[Assert\PositiveOrZero]
    private ?int $intPageFromHistMsgReadCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从动态中读取的用户数量'])]
    #[Assert\PositiveOrZero]
    private ?int $intPageFromFeedReadUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从动态中读取的总数'])]
    #[Assert\PositiveOrZero]
    private ?int $intPageFromFeedReadCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从好友中读取的用户数量'])]
    #[Assert\PositiveOrZero]
    private ?int $intPageFromFriendsReadUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从好友中读取的总数'])]
    #[Assert\PositiveOrZero]
    private ?int $intPageFromFriendsReadCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从其他来源读取的用户数量'])]
    #[Assert\PositiveOrZero]
    private ?int $intPageFromOtherReadUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从其他来源读取的总数'])]
    #[Assert\PositiveOrZero]
    private ?int $intPageFromOtherReadCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从会话中分享的用户数量'])]
    #[Assert\PositiveOrZero]
    private ?int $feedShareFromSessionUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从会话中分享的总数'])]
    #[Assert\PositiveOrZero]
    private ?int $feedShareFromSessionCnt = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从动态中分享的用户数量'])]
    #[Assert\PositiveOrZero]
    private ?int $feedShareFromFeedUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从动态中分享的总数'])]
    #[Assert\PositiveOrZero]
    private ?int $feedShareFromFeedCnt = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从其他来源分享的用户数量'])]
    #[Assert\PositiveOrZero]
    private ?int $feedShareFromOtherUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从其他来源分享的总数'])]
    #[Assert\PositiveOrZero]
    private ?int $feedShareFromOtherCnt = null;

    public function getFeedShareFromOtherCnt(): ?int
    {
        return $this->feedShareFromOtherCnt;
    }

    public function setFeedShareFromOtherCnt(int $feedShareFromOtherCnt): void
    {
        $this->feedShareFromOtherCnt = $feedShareFromOtherCnt;
    }

    public function getFeedShareFromOtherUser(): ?int
    {
        return $this->feedShareFromOtherUser;
    }

    public function setFeedShareFromOtherUser(int $feedShareFromOtherUser): void
    {
        $this->feedShareFromOtherUser = $feedShareFromOtherUser;
    }

    public function getFeedShareFromFeedCnt(): ?int
    {
        return $this->feedShareFromFeedCnt;
    }

    public function setFeedShareFromFeedCnt(int $feedShareFromFeedCnt): void
    {
        $this->feedShareFromFeedCnt = $feedShareFromFeedCnt;
    }

    public function getFeedShareFromFeedUser(): ?int
    {
        return $this->feedShareFromFeedUser;
    }

    public function setFeedShareFromFeedUser(int $feedShareFromFeedUser): void
    {
        $this->feedShareFromFeedUser = $feedShareFromFeedUser;
    }

    public function getFeedShareFromSessionCnt(): ?int
    {
        return $this->feedShareFromSessionCnt;
    }

    public function setFeedShareFromSessionCnt(int $feedShareFromSessionCnt): void
    {
        $this->feedShareFromSessionCnt = $feedShareFromSessionCnt;
    }

    public function getFeedShareFromSessionUser(): ?int
    {
        return $this->feedShareFromSessionUser;
    }

    public function setFeedShareFromSessionUser(int $feedShareFromSessionUser): void
    {
        $this->feedShareFromSessionUser = $feedShareFromSessionUser;
    }

    public function getIntPageFromOtherReadCount(): ?int
    {
        return $this->intPageFromOtherReadCount;
    }

    public function setIntPageFromOtherReadCount(int $intPageFromOtherReadCount): void
    {
        $this->intPageFromOtherReadCount = $intPageFromOtherReadCount;
    }

    public function getIntPageFromOtherReadUser(): ?int
    {
        return $this->intPageFromOtherReadUser;
    }

    public function setIntPageFromOtherReadUser(int $intPageFromOtherReadUser): void
    {
        $this->intPageFromOtherReadUser = $intPageFromOtherReadUser;
    }

    public function getIntPageFromFriendsReadCount(): ?int
    {
        return $this->intPageFromFriendsReadCount;
    }

    public function setIntPageFromFriendsReadCount(int $intPageFromFriendsReadCount): void
    {
        $this->intPageFromFriendsReadCount = $intPageFromFriendsReadCount;
    }

    public function getIntPageFromFriendsReadUser(): ?int
    {
        return $this->intPageFromFriendsReadUser;
    }

    public function setIntPageFromFriendsReadUser(int $intPageFromFriendsReadUser): void
    {
        $this->intPageFromFriendsReadUser = $intPageFromFriendsReadUser;
    }

    public function getIntPageFromFeedReadCount(): ?int
    {
        return $this->intPageFromFeedReadCount;
    }

    public function setIntPageFromFeedReadCount(int $intPageFromFeedReadCount): void
    {
        $this->intPageFromFeedReadCount = $intPageFromFeedReadCount;
    }

    public function getIntPageFromFeedReadUser(): ?int
    {
        return $this->intPageFromFeedReadUser;
    }

    public function setIntPageFromFeedReadUser(int $intPageFromFeedReadUser): void
    {
        $this->intPageFromFeedReadUser = $intPageFromFeedReadUser;
    }

    public function getIntPageFromHistMsgReadCount(): ?int
    {
        return $this->intPageFromHistMsgReadCount;
    }

    public function setIntPageFromHistMsgReadCount(int $intPageFromHistMsgReadCount): void
    {
        $this->intPageFromHistMsgReadCount = $intPageFromHistMsgReadCount;
    }

    public function getIntPageFromHistMsgReadUser(): ?int
    {
        return $this->intPageFromHistMsgReadUser;
    }

    public function setIntPageFromHistMsgReadUser(int $intPageFromHistMsgReadUser): void
    {
        $this->intPageFromHistMsgReadUser = $intPageFromHistMsgReadUser;
    }

    public function getIntPageFromSessionReadCount(): ?int
    {
        return $this->intPageFromSessionReadCount;
    }

    public function setIntPageFromSessionReadCount(int $intPageFromSessionReadCount): void
    {
        $this->intPageFromSessionReadCount = $intPageFromSessionReadCount;
    }

    public function getIntPageFromSessionReadUser(): ?int
    {
        return $this->intPageFromSessionReadUser;
    }

    public function setIntPageFromSessionReadUser(int $intPageFromSessionReadUser): void
    {
        $this->intPageFromSessionReadUser = $intPageFromSessionReadUser;
    }

    public function getAddToFavCount(): ?int
    {
        return $this->addToFavCount;
    }

    public function setAddToFavCount(int $addToFavCount): void
    {
        $this->addToFavCount = $addToFavCount;
    }

    public function getAddToFavUser(): ?int
    {
        return $this->addToFavUser;
    }

    public function setAddToFavUser(int $addToFavUser): void
    {
        $this->addToFavUser = $addToFavUser;
    }

    public function getShareCount(): ?int
    {
        return $this->shareCount;
    }

    public function setShareCount(int $shareCount): void
    {
        $this->shareCount = $shareCount;
    }

    public function getShareUser(): ?int
    {
        return $this->shareUser;
    }

    public function setShareUser(int $shareUser): void
    {
        $this->shareUser = $shareUser;
    }

    public function getOriPageReadCount(): ?int
    {
        return $this->oriPageReadCount;
    }

    public function setOriPageReadCount(int $oriPageReadCount): void
    {
        $this->oriPageReadCount = $oriPageReadCount;
    }

    public function getOriPageReadUser(): ?int
    {
        return $this->oriPageReadUser;
    }

    public function setOriPageReadUser(int $oriPageReadUser): void
    {
        $this->oriPageReadUser = $oriPageReadUser;
    }

    public function getIntPageReadCount(): ?int
    {
        return $this->intPageReadCount;
    }

    public function setIntPageReadCount(int $intPageReadCount): void
    {
        $this->intPageReadCount = $intPageReadCount;
    }

    public function getIntPageReadUser(): ?int
    {
        return $this->intPageReadUser;
    }

    public function setIntPageReadUser(int $intPageReadUser): void
    {
        $this->intPageReadUser = $intPageReadUser;
    }

    public function getTargetUser(): ?int
    {
        return $this->targetUser;
    }

    public function setTargetUser(int $targetUser): void
    {
        $this->targetUser = $targetUser;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getStatDate(): ?\DateTimeInterface
    {
        return $this->statDate;
    }

    public function setStatDate(\DateTimeInterface $statDate): void
    {
        $this->statDate = $statDate;
    }

    public function getMsgId(): ?string
    {
        return $this->msgId;
    }

    public function setMsgId(string $msgId): void
    {
        $this->msgId = $msgId;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
