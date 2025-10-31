<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Repository\AdvertisingSpaceDataRepository;

#[ORM\Entity(repositoryClass: AdvertisingSpaceDataRepository::class)]
#[ORM\Table(name: 'wechat_official_advertising_space_data', options: ['comment' => '获取公众号分广告位数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_advertising_space_data_uniq', columns: ['account_id', 'date'])]
class AdvertisingSpaceData implements \Stringable
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

    #[ORM\Column(options: ['comment' => '广告位类型id'])]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?int $slotId = null;

    #[ORM\Column(options: ['comment' => '广告位类型名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $adSlot = null;

    #[ORM\Column(nullable: true, options: ['comment' => '拉取量'])]
    #[Assert\PositiveOrZero]
    private ?int $reqSuccCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '曝光量'])]
    #[Assert\PositiveOrZero]
    private ?int $exposureCount = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '曝光率'])]
    #[Assert\Length(max: 60)]
    private ?string $exposureRate = null;

    #[ORM\Column(nullable: true, options: ['comment' => '点击量'])]
    #[Assert\PositiveOrZero]
    private ?int $clickCount = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '点击率'])]
    #[Assert\Length(max: 60)]
    private ?string $clickRate = null;

    #[ORM\Column(nullable: true, options: ['comment' => '收入(分)'])]
    #[Assert\PositiveOrZero]
    private ?int $income = null;

    #[ORM\Column(length: 70, nullable: true, options: ['comment' => '广告千次曝光收益(分)'])]
    #[Assert\Length(max: 70)]
    private ?string $ecpm = null;

    public function getEcpm(): ?string
    {
        return $this->ecpm;
    }

    public function setEcpm(string $ecpm): void
    {
        $this->ecpm = $ecpm;
    }

    public function getIncome(): ?int
    {
        return $this->income;
    }

    public function setIncome(int $income): void
    {
        $this->income = $income;
    }

    public function getClickRate(): ?string
    {
        return $this->clickRate;
    }

    public function setClickRate(string $clickRate): void
    {
        $this->clickRate = $clickRate;
    }

    public function getClickCount(): ?int
    {
        return $this->clickCount;
    }

    public function setClickCount(int $clickCount): void
    {
        $this->clickCount = $clickCount;
    }

    public function getExposureRate(): ?string
    {
        return $this->exposureRate;
    }

    public function setExposureRate(string $exposureRate): void
    {
        $this->exposureRate = $exposureRate;
    }

    public function getExposureCount(): ?int
    {
        return $this->exposureCount;
    }

    public function setExposureCount(int $exposureCount): void
    {
        $this->exposureCount = $exposureCount;
    }

    public function getReqSuccCount(): ?int
    {
        return $this->reqSuccCount;
    }

    public function setReqSuccCount(int $reqSuccCount): void
    {
        $this->reqSuccCount = $reqSuccCount;
    }

    public function getAdSlot(): ?string
    {
        return $this->adSlot;
    }

    public function setAdSlot(?string $adSlot): void
    {
        $this->adSlot = $adSlot;
    }

    public function getSlotId(): ?int
    {
        return $this->slotId;
    }

    public function setSlotId(int $slotId): void
    {
        $this->slotId = $slotId;
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
