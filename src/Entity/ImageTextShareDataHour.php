<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Repository\ImageTextShareDataHourRepository;

#[ORM\Entity(repositoryClass: ImageTextShareDataHourRepository::class)]
#[ORM\Table(name: 'wechat_official_image_text_share_hour', options: ['comment' => '图文分享转发分时数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_image_text_share_hour_uniq', columns: ['account_id', 'date', 'ref_hour', 'share_scene'])]
class ImageTextShareDataHour implements \Stringable
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

    #[ORM\Column(nullable: true, options: ['comment' => '小时'])]
    #[Assert\Range(min: 0, max: 23)]
    private ?int $refHour = null;

    #[ORM\Column(nullable: true, options: ['comment' => '分享的场景'])]
    #[Assert\PositiveOrZero]
    private ?int $shareScene = null;

    #[ORM\Column(nullable: true, options: ['comment' => '分享的次数'])]
    #[Assert\PositiveOrZero]
    private ?int $shareCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '分享的人数'])]
    #[Assert\PositiveOrZero]
    private ?int $shareUser = null;

    public function getRefHour(): ?int
    {
        return $this->refHour;
    }

    public function setRefHour(int $refHour): void
    {
        $this->refHour = $refHour;
    }

    public function getShareUser(): ?int
    {
        return $this->shareUser;
    }

    public function setShareUser(int $shareUser): void
    {
        $this->shareUser = $shareUser;
    }

    public function getShareCount(): ?int
    {
        return $this->shareCount;
    }

    public function setShareCount(int $shareCount): void
    {
        $this->shareCount = $shareCount;
    }

    public function getShareScene(): ?int
    {
        return $this->shareScene;
    }

    public function setShareScene(int $shareScene): void
    {
        $this->shareScene = $shareScene;
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
