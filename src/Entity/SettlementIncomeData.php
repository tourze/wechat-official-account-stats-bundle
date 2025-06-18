<?php

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderStatusEnum;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\SettlementIncomeDataRepository;

#[ORM\Entity(repositoryClass: SettlementIncomeDataRepository::class)]
#[ORM\Table(name: 'wechat_official_settlement_income_data', options: ['comment' => '获取公众号结算收入数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_settlement_income_data_uniq', columns: ['account_id', 'date', 'slot_id'])]
class SettlementIncomeData implements \Stringable
{
    use TimestampableAware;
            #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

        #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Account $account;

        #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['comment' => '日期'])]
    private ?\DateTimeInterface $date = null;

            #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '主体名称'])]
    private ?string $body = null;

        #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '扣除金额'])]
    private ?int $penaltyAll = null;

        #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '累计收入'])]
    private ?int $revenueAll = null;

        #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '已结算金额'])]
    private ?int $settledRevenueAll = null;

        #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '	日期区间'])]
    private ?string $zone = null;

        #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '收入月份'])]
    private ?string $month = null;

        #[ORM\Column(type: Types::INTEGER, enumType: SettlementIncomeOrderTypeEnum::class, options: ['comment' => '1 = 上半月，2 = 下半月'])]
    private ?SettlementIncomeOrderTypeEnum $order = null;

        #[ORM\Column(type: Types::INTEGER, enumType: SettlementIncomeOrderStatusEnum::class, options: ['comment' => '1 = 结算中；2、3 = 已结算；4 = 付款中；5 = 已付款'])]
    private ?SettlementIncomeOrderStatusEnum $settStatus = null;

        #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '区间内结算收入'])]
    private ?int $settledRevenue = null;

        #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '结算单编号'])]
    private ?string $settNo = null;

        #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '申请补发结算单次数'])]
    private ?string $mailSendCnt = null;

        #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '产生收入的广告位'])]
    private ?string $slotId = null;

        #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '该广告位结算金额'])]
    private ?int $slotSettledRevenue = null;

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

    public function setSlotSettledRevenue(?int $slotSettledRevenue): static
    {
        $this->slotSettledRevenue = $slotSettledRevenue;

        return $this;
    }

    public function getSlotId(): ?string
    {
        return $this->slotId;
    }

    public function setSlotId(?string $slotId): static
    {
        $this->slotId = $slotId;

        return $this;
    }

    public function getMailSendCnt(): ?string
    {
        return $this->mailSendCnt;
    }

    public function setMailSendCnt(?string $mailSendCnt): static
    {
        $this->mailSendCnt = $mailSendCnt;

        return $this;
    }

    public function getSettNo(): ?string
    {
        return $this->settNo;
    }

    public function setSettNo(?string $settNo): static
    {
        $this->settNo = $settNo;

        return $this;
    }

    public function getSettledRevenue(): ?int
    {
        return $this->settledRevenue;
    }

    public function setSettledRevenue(?int $settledRevenue): static
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
    public function __toString(): string
    {
        return (string) $this->id;
    }
}
