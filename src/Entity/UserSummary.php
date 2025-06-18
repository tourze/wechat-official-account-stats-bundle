<?php

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Enum\UserSummarySource;
use WechatOfficialAccountStatsBundle\Repository\UserSummaryRepository;

#[AsPermission(title: '用户统计数据')]
#[Deletable]
#[ORM\Entity(repositoryClass: UserSummaryRepository::class)]
#[ORM\Table(name: 'wechat_official_user_summary', options: ['comment' => '用户统计数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_user_summary_uniq', columns: ['account_id', 'date', 'source'])]
class UserSummary
{
    use TimestampableAware;
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

    #[Keyword]
    #[ListColumn]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Account $account;

    #[ListColumn]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ListColumn]
    #[ORM\Column(enumType: UserSummarySource::class)]
    private ?UserSummarySource $source = null;

    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '新增的用户量'])]
    private ?int $newUser = null;

    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '取消关注的用户数量，new_user减去cancel_user即为净增用户数量'])]
    private ?int $cancelUser = null;

    #[Filterable]
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
    }}
