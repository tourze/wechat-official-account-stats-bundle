<?php

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Repository\RebateGoodsDataRepository;

#[ORM\Entity(repositoryClass: RebateGoodsDataRepository::class)]
#[ORM\Table(name: 'wechat_official_rebate_goods_data', options: ['comment' => '获取公众号返佣商品数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_rebate_goods_data_uniq', columns: ['account_id', 'date'])]
class RebateGoodsData implements \Stringable
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

    #[ORM\Column(nullable: true, options: ['comment' => '曝光量'])]
    private ?int $exposureCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '点击量'])]
    private ?int $clickCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '点击率'])]
    private ?string $clickRate = null;

    #[ORM\Column(nullable: true, options: ['comment' => '订单量'])]
    private ?int $orderCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '下单率'])]
    private ?string $orderRate = null;

    #[ORM\Column(nullable: true, options: ['comment' => '订单金额(分)'])]
    private ?int $totalFee = null;

    #[ORM\Column(nullable: true, options: ['comment' => '预估收入(分)'])]
    private ?int $totalCommission = null;

        public function getTotalCommission(): ?int
    {
        return $this->totalCommission;
    }

    public function setTotalCommission(int $totalCommission): static
    {
        $this->totalCommission = $totalCommission;

        return $this;
    }

    public function getTotalFee(): ?int
    {
        return $this->totalFee;
    }

    public function setTotalFee(int $totalFee): static
    {
        $this->totalFee = $totalFee;

        return $this;
    }

    public function getOrderRate(): ?string
    {
        return $this->orderRate;
    }

    public function setOrderRate(string $orderRate): static
    {
        $this->orderRate = $orderRate;

        return $this;
    }

    public function getOrderCount(): ?int
    {
        return $this->orderCount;
    }

    public function setOrderCount(int $orderCount): static
    {
        $this->orderCount = $orderCount;

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

    public function getExposureCount(): ?int
    {
        return $this->exposureCount;
    }

    public function setExposureCount(int $exposureCount): static
    {
        $this->exposureCount = $exposureCount;

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
