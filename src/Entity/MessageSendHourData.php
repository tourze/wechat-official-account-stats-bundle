<?php

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSendHourDataRepository;

#[ORM\Entity(repositoryClass: MessageSendHourDataRepository::class)]
#[ORM\Table(name: 'wechat_official_message_send_hour_data', options: ['comment' => '获取消息发送分时数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_message_send_hour_data_uniq', columns: ['account_id', 'date', 'ref_hour'])]
class MessageSendHourData implements \Stringable
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

        #[ORM\Column(options: ['comment' => '小时'])]
    private ?int $refHour = null;

            #[ORM\Column(type: Types::INTEGER, enumType: MessageSendDataTypeEnum::class, options: ['comment' => '消息类型'])]
    private ?MessageSendDataTypeEnum $msgType = null;

        #[ORM\Column(nullable: true, options: ['comment' => '上行发送了（向公众号发送了）消息的用户数'])]
    private ?int $msgUser = null;

        #[ORM\Column(nullable: true, options: ['comment' => '上行发送了消息的消息总数'])]
    private ?int $msgCount = null;

        public function getMsgType(): ?MessageSendDataTypeEnum
    {
        return $this->msgType;
    }

    public function setMsgType(?MessageSendDataTypeEnum $msgType): void
    {
        $this->msgType = $msgType;
    }

    public function getRefHour(): ?int
    {
        return $this->refHour;
    }

    public function setRefHour(int $refHour): static
    {
        $this->refHour = $refHour;

        return $this;
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
    public function __toString(): string
    {
        return (string) $this->id;
    }
}
