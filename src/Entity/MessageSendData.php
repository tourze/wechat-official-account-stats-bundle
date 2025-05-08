<?php

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSendDataRepository;

#[AsPermission(title: '消息发送数据')]
#[Deletable]
#[ORM\Entity(repositoryClass: MessageSendDataRepository::class)]
#[ORM\Table(name: 'wechat_official_message_send_data', options: ['comment' => '消息发送数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_message_send_data_uniq', columns: ['account_id', 'date'])]
class MessageSendData
{
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

    #[Keyword]
    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, enumType: MessageSendDataTypeEnum::class, options: ['comment' => '消息类型'])]
    private ?MessageSendDataTypeEnum $msgType = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '上行发送了（向公众号发送了）消息的用户数'])]
    private ?int $msgUser = null;

    #[ListColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '上行发送了消息的消息总数'])]
    private ?int $msgCount = null;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function getMsgType(): ?MessageSendDataTypeEnum
    {
        return $this->msgType;
    }

    public function setMsgType(?MessageSendDataTypeEnum $msgType): void
    {
        $this->msgType = $msgType;
    }

    public function getMsgCount(): ?int
    {
        return $this->msgCount;
    }

    public function setMsgCount(int $msgCount): static
    {
        $this->msgCount = $msgCount;

        return $this;
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
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }
}
