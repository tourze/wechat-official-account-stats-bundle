<?php

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Enum\ImageTextUserSourceEnum;
use WechatOfficialAccountStatsBundle\Repository\ImageTextStatisticsRepository;

#[AsPermission(title: '图文统计数据')]
#[Deletable]
#[ORM\Entity(repositoryClass: ImageTextStatisticsRepository::class)]
#[ORM\Table(name: 'wechat_official_image_text_statistics', options: ['comment' => '图文统计数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_image_text_statistics_uniq', columns: ['account_id', 'date'])]
class ImageTextStatistics
{
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

    #[Keyword]
    #[ListColumn]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Account $account;

    #[ListColumn]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, enumType: ImageTextUserSourceEnum::class, options: ['comment' => '获取图文统计数据、图文阅读分时数据时才有该字段,代表用户从哪里进入来阅读该图文'])]
    private ?ImageTextUserSourceEnum $userSource = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '图文页（点击群发图文卡片进入的页面）的阅读人数'])]
    private ?int $intPageReadUser = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '图文页的阅读次数'])]
    private ?int $intPageReadCount = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '原文页（点击图文页“阅读原文”进入的页面）的阅读人数，无原文页时此处数据为0'])]
    private ?int $oriPageReadUser = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '原文页的阅读次数'])]
    private ?int $oriPageReadCount = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '分享的人数'])]
    private ?int $shareUser = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '分享的次数'])]
    private ?int $shareCount = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '收藏的人数'])]
    private ?int $addToFavUser = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '收藏的次数'])]
    private ?int $addToFavCount = null;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function getUserSource(): ?ImageTextUserSourceEnum
    {
        return $this->userSource;
    }

    public function setUserSource(?ImageTextUserSourceEnum $userSource): void
    {
        $this->userSource = $userSource;
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
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }
}
