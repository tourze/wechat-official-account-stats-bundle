<?php

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Repository\UserCumulateRepository;

#[ORM\Entity(repositoryClass: UserCumulateRepository::class)]
#[ORM\Table(name: 'wechat_official_user_cumulate', options: ['comment' => '用户累计数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_user_summary_uniq', columns: ['account_id', 'date'])]
class UserCumulate implements \Stringable
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

        #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '总用户量'])]
    private ?int $cumulateUser = null;

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

    public function getCumulateUser(): ?int
    {
        return $this->cumulateUser;
    }

    public function setCumulateUser(int $cumulateUser): static
    {
        $this->cumulateUser = $cumulateUser;

        return $this;
    }
    public function __toString(): string
    {
        return (string) $this->id;
    }
}
