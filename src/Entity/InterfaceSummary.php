<?php

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Repository\InterfaceSummaryRepository;

#[ORM\Entity(repositoryClass: InterfaceSummaryRepository::class)]
#[ORM\Table(name: 'wechat_official_interface_summary', options: ['comment' => '公众号-获取接口分析数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_interface_summary_uniq', columns: ['account_id', 'date'])]
class InterfaceSummary implements \Stringable
{
    use TimestampableAware;
            #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

            #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Account $account;

        #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['comment' => '日期'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '通过服务器配置地址获得消息后，被动回复用户消息的次数'])]
    private ?int $callbackCount = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '上述动作的失败次数'])]
    private ?int $failCount = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '总耗时，除以callback_count即为平均耗时'])]
    private ?int $totalTimeCost = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '最大耗时'])]
    private ?int $maxTimeCost = null;

        public function getId(): ?int
    {
        return $this->id;
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
