<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '主体名称'])]
    #[Assert\Length(max: 255)]
    private ?string $body = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '扣除金额'])]
    #[Assert\PositiveOrZero]
    private ?int $penaltyAll = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '累计收入'])]
    #[Assert\PositiveOrZero]
    private ?int $revenueAll = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '已结算金额'])]
    #[Assert\PositiveOrZero]
    private ?int $settledRevenueAll = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '	日期区间'])]
    #[Assert\Length(max: 255)]
    private ?string $zone = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '收入月份'])]
    #[Assert\Length(max: 255)]
    private ?string $month = null;

    #[ORM\Column(name: 'order_type', type: Types::INTEGER, enumType: SettlementIncomeOrderTypeEnum::class, options: ['comment' => '1 = 上半月，2 = 下半月'])]
    #[Assert\Choice(callback: [SettlementIncomeOrderTypeEnum::class, 'cases'])]
    private ?SettlementIncomeOrderTypeEnum $order = null;

    #[ORM\Column(type: Types::INTEGER, enumType: SettlementIncomeOrderStatusEnum::class, options: ['comment' => '1 = 结算中；2、3 = 已结算；4 = 付款中；5 = 已付款'])]
    #[Assert\Choice(callback: [SettlementIncomeOrderStatusEnum::class, 'cases'])]
    private ?SettlementIncomeOrderStatusEnum $settStatus = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '区间内结算收入'])]
    #[Assert\PositiveOrZero]
    private ?int $settledRevenue = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '结算单编号'])]
    #[Assert\Length(max: 255)]
    private ?string $settNo = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '申请补发结算单次数'])]
    #[Assert\Length(max: 255)]
    private ?string $mailSendCnt = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '产生收入的广告位'])]
    #[Assert\Length(max: 255)]
    private ?string $slotId = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '该广告位结算金额'])]
    #[Assert\PositiveOrZero]
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

    public function setSlotSettledRevenue(?int $slotSettledRevenue): void
    {
        $this->slotSettledRevenue = $slotSettledRevenue;
    }

    public function getSlotId(): ?string
    {
        return $this->slotId;
    }

    public function setSlotId(?string $slotId): void
    {
        $this->slotId = $slotId;
    }

    public function getMailSendCnt(): ?string
    {
        return $this->mailSendCnt;
    }

    public function setMailSendCnt(?string $mailSendCnt): void
    {
        $this->mailSendCnt = $mailSendCnt;
    }

    public function getSettNo(): ?string
    {
        return $this->settNo;
    }

    public function setSettNo(?string $settNo): void
    {
        $this->settNo = $settNo;
    }

    public function getSettledRevenue(): ?int
    {
        return $this->settledRevenue;
    }

    public function setSettledRevenue(?int $settledRevenue): void
    {
        $this->settledRevenue = $settledRevenue;
    }

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function setMonth(string $month): void
    {
        $this->month = $month;
    }

    public function getZone(): ?string
    {
        return $this->zone;
    }

    public function setZone(string $zone): void
    {
        $this->zone = $zone;
    }

    public function getSettledRevenueAll(): ?int
    {
        return $this->settledRevenueAll;
    }

    public function setSettledRevenueAll(int $settledRevenueAll): void
    {
        $this->settledRevenueAll = $settledRevenueAll;
    }

    public function getRevenueAll(): ?int
    {
        return $this->revenueAll;
    }

    public function setRevenueAll(int $revenueAll): void
    {
        $this->revenueAll = $revenueAll;
    }

    public function getPenaltyAll(): ?int
    {
        return $this->penaltyAll;
    }

    public function setPenaltyAll(int $penaltyAll): void
    {
        $this->penaltyAll = $penaltyAll;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
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
