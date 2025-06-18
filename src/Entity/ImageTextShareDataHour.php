<?php

namespace WechatOfficialAccountStatsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Repository\ImageTextShareDataHourRepository;

#[ORM\Entity(repositoryClass: ImageTextShareDataHourRepository::class)]
#[ORM\Table(name: 'wechat_official_image_text_share_hour', options: ['comment' => '图文分享转发分时数据'])]
#[ORM\UniqueConstraint(name: 'wechat_official_image_text_share_hour_uniq', columns: ['account_id', 'date'])]
class ImageTextShareDataHour implements \Stringable
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

        #[ORM\Column(nullable: true, options: ['comment' => '小时'])]
    private ?int $refHour = null;

        #[ORM\Column(nullable: true, options: ['comment' => '分享的场景'])]
    private ?int $shareScene = null;

        #[ORM\Column(nullable: true, options: ['comment' => '分享的次数'])]
    private ?int $shareCount = null;

        #[ORM\Column(nullable: true, options: ['comment' => '分享的人数'])]
    private ?int $shareUser = null;

        public function getRefHour(): ?int
    {
        return $this->refHour;
    }

    public function setRefHour(int $refHour): static
    {
        $this->refHour = $refHour;

        return $this;
    }

    public function getShareUser(): ?int
    {
        return $this->shareUser;
    }

    public function setShareUser(int $shareUser): static
    {
        $this->shareUser = $shareUser;

        return $this;
    }

    public function getShareCount(): ?int
    {
        return $this->shareCount;
    }

    public function setShareCount(int $shareCount): static
    {
        $this->shareCount = $shareCount;

        return $this;
    }

    public function getShareScene(): ?int
    {
        return $this->shareScene;
    }

    public function setShareScene(int $shareScene): static
    {
        $this->shareScene = $shareScene;

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
