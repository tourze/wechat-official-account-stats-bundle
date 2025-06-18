<?php

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataCountIntervalEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSenDistDataRepository;

#[AsPermission(title: '消息发送分布数据')]
#[Deletable]
#[ORM\Entity(repositoryClass: MessageSenDistDataRepository::class)]
#[ORM\Table(name: 'wechat_official_message_send_dist_data', options: ['comment' => '消息发送分布数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_message_send_dist_data_uniq', columns: ['account_id', 'date'])]
class MessageSenDistData
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

    #[ListColumn]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Account $account;

    #[ListColumn]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, enumType: MessageSendDataCountIntervalEnum::class, options: ['comment' => '当日发送消息量分布的区间'])]
    private ?MessageSendDataCountIntervalEnum $countInterval = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '上行发送了（向公众号发送了）消息的用户数'])]
    private ?int $msgUser = null;

    #[Filterable]
    public function getCountInterval(): ?MessageSendDataCountIntervalEnum
    {
        return $this->countInterval;
    }

    public function setCountInterval(?MessageSendDataCountIntervalEnum $countInterval): void
    {
        $this->countInterval = $countInterval;
    }

    public function getMsgUser(): ?int
    {
        return $this->msgUser;
    }

    public function setMsgUser(int $msgUser): static
    {
        $this->msgUser = $msgUser;

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
    }}
