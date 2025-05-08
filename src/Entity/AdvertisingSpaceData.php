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
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Repository\AdvertisingSpaceDataRepository;

#[AsPermission(title: '获取公众号分广告位数据')]
#[Deletable]
#[ORM\Entity(repositoryClass: AdvertisingSpaceDataRepository::class)]
#[ORM\Table(name: 'wechat_official_advertising_space_data', options: ['comment' => '获取公众号分广告位数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_advertising_space_data_uniq', columns: ['account_id', 'date'])]
class AdvertisingSpaceData
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

    #[ListColumn]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Account $account;

    #[ListColumn]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ListColumn]
    #[ORM\Column(options: ['comment' => '广告位类型id'])]
    private ?int $slotId = null;

    #[ListColumn]
    #[ORM\Column(options: ['comment' => '广告位类型名称'])]
    private ?string $adSlot = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '拉取量'])]
    private ?int $reqSuccCount = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '曝光量'])]
    private ?int $exposureCount = null;

    #[ListColumn]
    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '曝光率'])]
    private ?string $exposureRate = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '点击量'])]
    private ?int $clickCount = null;

    #[ListColumn]
    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '点击率'])]
    private ?string $clickRate = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '收入(分)'])]
    private ?int $income = null;

    #[ListColumn]
    #[ORM\Column(length: 70, nullable: true, options: ['comment' => '广告千次曝光收益(分)'])]
    private ?string $ecpm = null;

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

    public function getEcpm(): ?string
    {
        return $this->ecpm;
    }

    public function setEcpm(string $ecpm): static
    {
        $this->ecpm = $ecpm;

        return $this;
    }

    public function getIncome(): ?int
    {
        return $this->income;
    }

    public function setIncome(int $income): static
    {
        $this->income = $income;

        return $this;
    }

    public function getClickRate(): ?string
    {
        return $this->clickRate;
    }

    public function setClickRate(string $clickRate): static
    {
        $this->clickRate = $clickRate;

        return $this;
    }

    public function getClickCount(): ?int
    {
        return $this->clickCount;
    }

    public function setClickCount(int $clickCount): static
    {
        $this->clickCount = $clickCount;

        return $this;
    }

    public function getExposureRate(): ?string
    {
        return $this->exposureRate;
    }

    public function setExposureRate(string $exposureRate): static
    {
        $this->exposureRate = $exposureRate;

        return $this;
    }

    public function getExposureCount(): ?int
    {
        return $this->exposureCount;
    }

    public function setExposureCount(int $exposureCount): static
    {
        $this->exposureCount = $exposureCount;

        return $this;
    }

    public function getReqSuccCount(): ?int
    {
        return $this->reqSuccCount;
    }

    public function setReqSuccCount(int $reqSuccCount): static
    {
        $this->reqSuccCount = $reqSuccCount;

        return $this;
    }

    public function getAdSlot(): ?string
    {
        return $this->adSlot;
    }

    public function setAdSlot(?string $adSlot): self
    {
        $this->adSlot = $adSlot;

        return $this;
    }

    public function getSlotId(): ?int
    {
        return $this->slotId;
    }

    public function setSlotId(int $slotId): static
    {
        $this->slotId = $slotId;

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
