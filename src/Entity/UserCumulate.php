<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Repository\UserCumulateRepository;

#[ORM\Entity(repositoryClass: UserCumulateRepository::class)]
#[ORM\Table(name: 'wechat_official_user_cumulate', options: ['comment' => '用户累计数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_user_cumulate_uniq', columns: ['account_id', 'date'])]
class UserCumulate implements \Stringable
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

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '总用户量'])]
    #[Assert\PositiveOrZero]
    private ?int $cumulateUser = null;

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

    public function getCumulateUser(): ?int
    {
        return $this->cumulateUser;
    }

    public function setCumulateUser(int $cumulateUser): void
    {
        $this->cumulateUser = $cumulateUser;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
