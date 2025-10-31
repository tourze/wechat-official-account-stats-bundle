<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Repository\InterfaceSummaryHourRepository;

#[ORM\Entity(repositoryClass: InterfaceSummaryHourRepository::class)]
#[ORM\Table(name: 'wechat_official_interface_summary_hour', options: ['comment' => '公众号-获取接口分析数据by hour'])]
#[ORM\UniqueConstraint(name: 'wechat_official_interface_summary_hour_uniq', columns: ['account_id', 'date', 'ref_hour'])]
class InterfaceSummaryHour implements \Stringable
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
    #[Assert\NotNull]
    private Account $account;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['comment' => '日期'])]
    #[Assert\NotNull]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '数据的小时'])]
    #[Assert\Range(min: 0, max: 23)]
    private ?int $refHour = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '通过服务器配置地址获得消息后，被动回复用户消息的次数'])]
    #[Assert\PositiveOrZero]
    private ?int $callbackCount = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '上述动作的失败次数'])]
    #[Assert\PositiveOrZero]
    private ?int $failCount = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '总耗时，除以callback_count即为平均耗时'])]
    #[Assert\PositiveOrZero]
    private ?int $totalTimeCost = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '最大耗时'])]
    #[Assert\PositiveOrZero]
    private ?int $maxTimeCost = null;

    public function getRefHour(): ?int
    {
        return $this->refHour;
    }

    public function setRefHour(?int $refHour): void
    {
        $this->refHour = $refHour;
    }

    public function getMaxTimeCost(): ?int
    {
        return $this->maxTimeCost;
    }

    public function setMaxTimeCost(?int $maxTimeCost): void
    {
        $this->maxTimeCost = $maxTimeCost;
    }

    public function getTotalTimeCost(): ?int
    {
        return $this->totalTimeCost;
    }

    public function setTotalTimeCost(?int $totalTimeCost): void
    {
        $this->totalTimeCost = $totalTimeCost;
    }

    public function getFailCount(): ?int
    {
        return $this->failCount;
    }

    public function setFailCount(?int $failCount): void
    {
        $this->failCount = $failCount;
    }

    public function getCallbackCount(): ?int
    {
        return $this->callbackCount;
    }

    public function setCallbackCount(?int $callbackCount): void
    {
        $this->callbackCount = $callbackCount;
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
