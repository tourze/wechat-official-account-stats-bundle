<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Enum\ImageTextUserSourceEnum;
use WechatOfficialAccountStatsBundle\Repository\ImageTextStatisticsRepository;

#[ORM\Entity(repositoryClass: ImageTextStatisticsRepository::class)]
#[ORM\Table(name: 'wechat_official_image_text_statistics', options: ['comment' => '图文统计数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_image_text_statistics_uniq', columns: ['account_id', 'date', 'user_source'])]
class ImageTextStatistics implements \Stringable
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

    #[ORM\Column(type: Types::INTEGER, enumType: ImageTextUserSourceEnum::class, nullable: true, options: ['comment' => '获取图文统计数据、图文阅读分时数据时才有该字段,代表用户从哪里进入来阅读该图文'])]
    #[Assert\Choice(callback: [ImageTextUserSourceEnum::class, 'cases'])]
    private ?ImageTextUserSourceEnum $userSource = null;

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
