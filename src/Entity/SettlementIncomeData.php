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
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderStatusEnum;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\SettlementIncomeDataRepository;

#[AsPermission(title: '获取公众号结算收入数据')]
#[Deletable]
#[ORM\Entity(repositoryClass: SettlementIncomeDataRepository::class)]
#[ORM\Table(name: 'wechat_official_settlement_income_data', options: ['comment' => '获取公众号结算收入数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_settlement_income_data_uniq', columns: ['account_id', 'date', 'slot_id'])]
class SettlementIncomeData
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

    #[Keyword]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '主体名称'])]
    private ?string $body = null;

    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '扣除金额'])]
    private ?int $penaltyAll = null;

    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '累计收入'])]
    private ?int $revenueAll = null;

    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '已结算金额'])]
    private ?int $settledRevenueAll = null;

    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '	日期区间'])]
    private ?string $zone = null;

    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '收入月份'])]
    private ?string $month = null;

    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, enumType: SettlementIncomeOrderTypeEnum::class, options: ['comment' => '1 = 上半月，2 = 下半月'])]
    private ?SettlementIncomeOrderTypeEnum $order = null;

    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, enumType: SettlementIncomeOrderStatusEnum::class, options: ['comment' => '1 = 结算中；2、3 = 已结算；4 = 付款中；5 = 已付款'])]
    private ?SettlementIncomeOrderStatusEnum $settStatus = null;

    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '区间内结算收入'])]
    private ?int $settledRevenue = null;

    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '结算单编号'])]
    private ?string $settNo = null;

    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '申请补发结算单次数'])]
    private ?string $mailSendCnt = null;

    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '产生收入的广告位'])]
    private ?string $slotId = null;

    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '该广告位结算金额'])]
    private ?int $slotSettledRevenue = null;

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

    public function getSettStatus(): ?SettlementIncomeOrderStatusEnum
    {
        return $this->settStatus;
    }

    public function setSettStatus(?SettlementIncomeOrderStatusEnum $settStatus): void
    {
        $this->settStatus = $settStatus;
    }

    public function getOrder(): ?SettlementIncomeOrderTypeEnum
    {
        return $this->order;
    }

    public function setOrder(?SettlementIncomeOrderTypeEnum $order): void
    {
        $this->order = $order;
    }

    public function getSlotSettledRevenue(): ?int
    {
        return $this->slotSettledRevenue;
    }

    public function setSlotSettledRevenue(string $slotSettledRevenue): static
    {
        $this->slotSettledRevenue = $slotSettledRevenue;

        return $this;
    }

    public function getSlotId(): ?int
    {
        return $this->slotId;
    }

    public function setSlotId(string $slotId): static
    {
        $this->slotId = $slotId;

        return $this;
    }

    public function getMailSendCnt(): ?int
    {
        return $this->mailSendCnt;
    }

    public function setMailSendCnt(string $mailSendCnt): static
    {
        $this->mailSendCnt = $mailSendCnt;

        return $this;
    }

    public function getSettNo(): ?int
    {
        return $this->settNo;
    }

    public function setSettNo(string $settNo): static
    {
        $this->settNo = $settNo;

        return $this;
    }

    public function getSettledRevenue(): ?int
    {
        return $this->settledRevenue;
    }

    public function setSettledRevenue(string $settledRevenue): static
    {
        $this->settledRevenue = $settledRevenue;

        return $this;
    }

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function setMonth(string $month): static
    {
        $this->month = $month;

        return $this;
    }

    public function getZone(): ?string
    {
        return $this->zone;
    }

    public function setZone(string $zone): static
    {
        $this->zone = $zone;

        return $this;
    }

    public function getSettledRevenueAll(): ?int
    {
        return $this->settledRevenueAll;
    }

    public function setSettledRevenueAll(int $settledRevenueAll): static
    {
        $this->settledRevenueAll = $settledRevenueAll;

        return $this;
    }

    public function getRevenueAll(): ?int
    {
        return $this->revenueAll;
    }

    public function setRevenueAll(int $revenueAll): static
    {
        $this->revenueAll = $revenueAll;

        return $this;
    }

    public function getPenaltyAll(): ?int
    {
        return $this->penaltyAll;
    }

    public function setPenaltyAll(int $penaltyAll): static
    {
        $this->penaltyAll = $penaltyAll;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

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
