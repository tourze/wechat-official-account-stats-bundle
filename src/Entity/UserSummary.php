<?php

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Enum\UserSummarySource;
use WechatOfficialAccountStatsBundle\Repository\UserSummaryRepository;

#[ORM\Entity(repositoryClass: UserSummaryRepository::class)]
#[ORM\Table(name: 'wechat_official_user_summary', options: ['comment' => '用户统计数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_user_summary_uniq', columns: ['account_id', 'date', 'source'])]
class UserSummary implements \Stringable
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

        #[ORM\Column(enumType: UserSummarySource::class, options: ['comment' => '用户的渠道'])]
    private ?UserSummarySource $source = null;

        #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '新增的用户量'])]
    private ?int $newUser = null;

        #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '取消关注的用户数量，new_user减去cancel_user即为净增用户数量'])]
    private ?int $cancelUser = null;

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

    public function getSource(): ?UserSummarySource
    {
        return $this->source;
    }

    public function setSource(UserSummarySource $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getNewUser(): ?int
    {
        return $this->newUser;
    }

    public function setNewUser(int $newUser): static
    {
        $this->newUser = $newUser;

        return $this;
    }

    public function getCancelUser(): ?int
    {
        return $this->cancelUser;
    }

    public function setCancelUser(int $cancelUser): static
    {
        $this->cancelUser = $cancelUser;

        return $this;
    }
    public function __toString(): string
    {
        return (string) $this->id;
    }
}
