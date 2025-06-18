<?php

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Repository\ArticleTotalRepository;

#[AsPermission(title: '获取图文群发总数据')]
#[Deletable]
#[ORM\Entity(repositoryClass: ArticleTotalRepository::class)]
#[ORM\Table(name: 'wechat_official_article_total', options: ['comment' => '获取图文群发总数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_article_total_uniq', columns: ['account_id', 'date', 'stat_date'])]
class ArticleTotal
{
    use TimestampableAware;
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ListColumn]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Account $account;

    #[ListColumn]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ListColumn]
    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '这里的msgid实际上是由msgid和index组成'])]
    private ?string $msgId = null;

    #[Keyword]
    #[ListColumn]
    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '图文消息的标题'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, options: ['comment' => '统计的日期，在getarticletotal接口中，ref_date指的是文章群发出日期， 而stat_date是数据统计日期'])]
    private ?\DateTimeInterface $statDate = null;

    #[ORM\Column(nullable: true, options: ['comment' => '送达人数，一般约等于总粉丝数（需排除黑名单或其他异常情况下无法收到消息的粉丝）'])]
    private ?int $targetUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '图文页（点击群发图文卡片进入的页面）的阅读人数'])]
    private ?int $intPageReadUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '图文页的阅读次数'])]
    private ?int $intPageReadCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '原文页（点击图文页“阅读原文”进入的页面）的阅读人数，无原文页时此处数据为0'])]
    private ?int $oriPageReadUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '原文页的阅读次数'])]
    private ?int $oriPageReadCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '分享的人数'])]
    private ?int $shareUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '分享的次数'])]
    private ?int $shareCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '收藏的人数'])]
    private ?int $addToFavUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '收藏的次数'])]
    private ?int $addToFavCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从会话中读取的用户数量'])]
    private ?int $intPageFromSessionReadUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从会话中读取的总数'])]
    private ?int $intPageFromSessionReadCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从历史消息中读取的用户数量'])]
    private ?int $intPageFromHistMsgReadUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '历史消息中读取的总数'])]
    private ?int $intPageFromHistMsgReadCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从动态中读取的用户数量'])]
    private ?int $intPageFromFeedReadUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从动态中读取的总数'])]
    private ?int $intPageFromFeedReadCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从好友中读取的用户数量'])]
    private ?int $intPageFromFriendsReadUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从好友中读取的总数'])]
    private ?int $intPageFromFriendsReadCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从其他来源读取的用户数量'])]
    private ?int $intPageFromOtherReadUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从其他来源读取的总数'])]
    private ?int $intPageFromOtherReadCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从会话中分享的用户数量'])]
    private ?int $feedShareFromSessionUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从会话中分享的总数'])]
    private ?int $feedShareFromSessionCnt = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从动态中分享的用户数量'])]
    private ?int $feedShareFromFeedUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从动态中分享的总数'])]
    private ?int $feedShareFromFeedCnt = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从其他来源分享的用户数量'])]
    private ?int $feedShareFromOtherUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '从其他来源分享的总数'])]
    private ?int $feedShareFromOtherCnt = null;

    #[Filterable]
    public function getFeedShareFromOtherCnt(): ?int
    {
        return $this->feedShareFromOtherCnt;
    }

    public function setFeedShareFromOtherCnt(int $feedShareFromOtherCnt): static
    {
        $this->feedShareFromOtherCnt = $feedShareFromOtherCnt;

        return $this;
    }

    public function getFeedShareFromOtherUser(): ?int
    {
        return $this->feedShareFromOtherUser;
    }

    public function setFeedShareFromOtherUser(int $feedShareFromOtherUser): static
    {
        $this->feedShareFromOtherUser = $feedShareFromOtherUser;

        return $this;
    }

    public function getFeedShareFromFeedCnt(): ?int
    {
        return $this->feedShareFromFeedCnt;
    }

    public function setFeedShareFromFeedCnt(int $feedShareFromFeedCnt): static
    {
        $this->feedShareFromFeedCnt = $feedShareFromFeedCnt;

        return $this;
    }

    public function getFeedShareFromFeedUser(): ?int
    {
        return $this->feedShareFromFeedUser;
    }

    public function setFeedShareFromFeedUser(int $feedShareFromFeedUser): static
    {
        $this->feedShareFromFeedUser = $feedShareFromFeedUser;

        return $this;
    }

    public function getFeedShareFromSessionCnt(): ?int
    {
        return $this->feedShareFromSessionCnt;
    }

    public function setFeedShareFromSessionCnt(int $feedShareFromSessionCnt): static
    {
        $this->feedShareFromSessionCnt = $feedShareFromSessionCnt;

        return $this;
    }

    public function getFeedShareFromSessionUser(): ?int
    {
        return $this->feedShareFromSessionUser;
    }

    public function setFeedShareFromSessionUser(int $feedShareFromSessionUser): static
    {
        $this->feedShareFromSessionUser = $feedShareFromSessionUser;

        return $this;
    }

    public function getIntPageFromOtherReadCount(): ?int
    {
        return $this->intPageFromOtherReadCount;
    }

    public function setIntPageFromOtherReadCount(int $intPageFromOtherReadCount): static
    {
        $this->intPageFromOtherReadCount = $intPageFromOtherReadCount;

        return $this;
    }

    public function getIntPageFromOtherReadUser(): ?int
    {
        return $this->intPageFromOtherReadUser;
    }

    public function setIntPageFromOtherReadUser(int $intPageFromOtherReadUser): static
    {
        $this->intPageFromOtherReadUser = $intPageFromOtherReadUser;

        return $this;
    }

    public function getIntPageFromFriendsReadCount(): ?int
    {
        return $this->intPageFromFriendsReadCount;
    }

    public function setIntPageFromFriendsReadCount(int $intPageFromFriendsReadCount): static
    {
        $this->intPageFromFriendsReadCount = $intPageFromFriendsReadCount;

        return $this;
    }

    public function getIntPageFromFriendsReadUser(): ?int
    {
        return $this->intPageFromFriendsReadUser;
    }

    public function setIntPageFromFriendsReadUser(int $intPageFromFriendsReadUser): static
    {
        $this->intPageFromFriendsReadUser = $intPageFromFriendsReadUser;

        return $this;
    }

    public function getIntPageFromFeedReadCount(): ?int
    {
        return $this->intPageFromFeedReadCount;
    }

    public function setIntPageFromFeedReadCount(int $intPageFromFeedReadCount): static
    {
        $this->intPageFromFeedReadCount = $intPageFromFeedReadCount;

        return $this;
    }

    public function getIntPageFromFeedReadUser(): ?int
    {
        return $this->intPageFromFeedReadUser;
    }

    public function setIntPageFromFeedReadUser(int $intPageFromFeedReadUser): static
    {
        $this->intPageFromFeedReadUser = $intPageFromFeedReadUser;

        return $this;
    }

    public function getIntPageFromHistMsgReadCount(): ?int
    {
        return $this->intPageFromHistMsgReadCount;
    }

    public function setIntPageFromHistMsgReadCount(int $intPageFromHistMsgReadCount): static
    {
        $this->intPageFromHistMsgReadCount = $intPageFromHistMsgReadCount;

        return $this;
    }

    public function getIntPageFromHistMsgReadUser(): ?int
    {
        return $this->intPageFromHistMsgReadUser;
    }

    public function setIntPageFromHistMsgReadUser(int $intPageFromHistMsgReadUser): static
    {
        $this->intPageFromHistMsgReadUser = $intPageFromHistMsgReadUser;

        return $this;
    }

    public function getIntPageFromSessionReadCount(): ?int
    {
        return $this->intPageFromSessionReadCount;
    }

    public function setIntPageFromSessionReadCount(int $intPageFromSessionReadCount): static
    {
        $this->intPageFromSessionReadCount = $intPageFromSessionReadCount;

        return $this;
    }

    public function getIntPageFromSessionReadUser(): ?int
    {
        return $this->intPageFromSessionReadUser;
    }

    public function setIntPageFromSessionReadUser(int $intPageFromSessionReadUser): static
    {
        $this->intPageFromSessionReadUser = $intPageFromSessionReadUser;

        return $this;
    }

    public function getAddToFavCount(): ?int
    {
        return $this->addToFavCount;
    }

    public function setAddToFavCount(int $addToFavCount): static
    {
        $this->addToFavCount = $addToFavCount;

        return $this;
    }

    public function getAddToFavUser(): ?int
    {
        return $this->addToFavUser;
    }

    public function setAddToFavUser(int $addToFavUser): static
    {
        $this->addToFavUser = $addToFavUser;

        return $this;
    }

    public function getShareCount(): ?int
    {
        return $this->shareCount;
    }

    public function setShareCount(int $shareCount): static
    {
        $this->shareCount = $shareCount;

        return $this;
    }

    public function getShareUser(): ?int
    {
        return $this->shareUser;
    }

    public function setShareUser(int $shareUser): static
    {
        $this->shareUser = $shareUser;

        return $this;
    }

    public function getOriPageReadCount(): ?int
    {
        return $this->oriPageReadCount;
    }

    public function setOriPageReadCount(int $oriPageReadCount): static
    {
        $this->oriPageReadCount = $oriPageReadCount;

        return $this;
    }

    public function getOriPageReadUser(): ?int
    {
        return $this->oriPageReadUser;
    }

    public function setOriPageReadUser(int $oriPageReadUser): static
    {
        $this->oriPageReadUser = $oriPageReadUser;

        return $this;
    }

    public function getIntPageReadCount(): ?int
    {
        return $this->intPageReadCount;
    }

    public function setIntPageReadCount(int $intPageReadCount): static
    {
        $this->intPageReadCount = $intPageReadCount;

        return $this;
    }

    public function getIntPageReadUser(): ?int
    {
        return $this->intPageReadUser;
    }

    public function setIntPageReadUser(int $intPageReadUser): static
    {
        $this->intPageReadUser = $intPageReadUser;

        return $this;
    }

    public function getTargetUser(): ?int
    {
        return $this->targetUser;
    }

    public function setTargetUser(int $targetUser): static
    {
        $this->targetUser = $targetUser;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStatDate(): ?\DateTimeInterface
    {
        return $this->statDate;
    }

    public function setStatDate(\DateTimeInterface $statDate): static
    {
        $this->statDate = $statDate;

        return $this;
    }

    public function getMsgId(): ?string
    {
        return $this->msgId;
    }

    public function setMsgId(string $msgId): self
    {
        $this->msgId = $msgId;

        return $this;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }}
