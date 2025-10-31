<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataCountIntervalEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSenDistDataRepository;

#[ORM\Entity(repositoryClass: MessageSenDistDataRepository::class)]
#[ORM\Table(name: 'wechat_official_message_send_dist_data', options: ['comment' => '消息发送分布数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_message_send_dist_data_uniq', columns: ['account_id', 'date', 'count_interval'])]
class MessageSenDistData implements \Stringable
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

    #[ORM\Column(type: Types::INTEGER, enumType: MessageSendDataCountIntervalEnum::class, options: ['comment' => '当日发送消息量分布的区间'])]
    #[Assert\Choice(callback: [MessageSendDataCountIntervalEnum::class, 'cases'])]
    #[Assert\NotNull]
    private MessageSendDataCountIntervalEnum $countInterval;

    #[ORM\Column(nullable: true, options: ['comment' => '上行发送了（向公众号发送了）消息的用户数'])]
    #[Assert\PositiveOrZero]
    private ?int $msgUser = null;

    public function getCountInterval(): MessageSendDataCountIntervalEnum
    {
        return $this->countInterval;
    }

    public function setCountInterval(MessageSendDataCountIntervalEnum $countInterval): void
    {
        $this->countInterval = $countInterval;
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
