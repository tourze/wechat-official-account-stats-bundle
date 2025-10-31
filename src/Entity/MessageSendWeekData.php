<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSendWeekDataRepository;

#[ORM\Entity(repositoryClass: MessageSendWeekDataRepository::class)]
#[ORM\Table(name: 'wechat_official_message_send_week_data', options: ['comment' => '消息发送周数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_message_send_week_data_uniq', columns: ['account_id', 'date', 'msg_type'])]
class MessageSendWeekData implements \Stringable
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

    #[ORM\Column(type: Types::INTEGER, enumType: MessageSendDataTypeEnum::class, options: ['comment' => '消息类型'])]
    #[Assert\Choice(callback: [MessageSendDataTypeEnum::class, 'cases'])]
    private ?MessageSendDataTypeEnum $msgType = null;

    #[ORM\Column(nullable: true, options: ['comment' => '上行发送了（向公众号发送了）消息的用户数'])]
    #[Assert\PositiveOrZero]
    private ?int $msgUser = null;

    #[ORM\Column(nullable: true, options: ['comment' => '上行发送了消息的消息总数'])]
    #[Assert\PositiveOrZero]
    private ?int $msgCount = null;

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

    public function setMsgCount(int $msgCount): void
    {
        $this->msgCount = $msgCount;
    }

    public function getMsgUser(): ?int
    {
        return $this->msgUser;
    }

    public function setMsgUser(int $msgUser): void
    {
        $this->msgUser = $msgUser;
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
