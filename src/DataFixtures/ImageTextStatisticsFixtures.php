<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatistics;
use WechatOfficialAccountStatsBundle\Enum\ImageTextUserSourceEnum;

#[When(env: 'test')]
#[When(env: 'dev')]
class ImageTextStatisticsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号');
        $account->setAppId('test_official_account_its2_001');
        $account->setAppSecret('test_secret_its2_001');
        $account->setValid(true);
        $manager->persist($account);

        // 创建测试数据
        $sources = ImageTextUserSourceEnum::cases();

        // 确保变量总是有定义
        $imageTextStatistics = null;

        // 创建几条记录，确保每个 account_id + date 组合都是唯一的
        $recordIndex = 1;
        foreach ($sources as $source) {
            for ($i = 1; $i <= 5; ++$i) {
                // 确保日期不超过 1 月 31 日
                if ($recordIndex > 31) {
                    break;
                }
                $imageTextStatistics = new ImageTextStatistics();
                $imageTextStatistics->setAccount($account);
                $imageTextStatistics->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $recordIndex)));
                $imageTextStatistics->setUserSource($source);
                $imageTextStatistics->setIntPageReadUser(500 + $i * 50 + $source->value * 10);
                $imageTextStatistics->setIntPageReadCount(800 + $i * 80 + $source->value * 15);
                $imageTextStatistics->setOriPageReadUser(100 + $i * 10 + $source->value * 5);
                $imageTextStatistics->setOriPageReadCount(150 + $i * 15 + $source->value * 8);
                $imageTextStatistics->setShareUser(25 + $i * 2 + $source->value);
                $imageTextStatistics->setShareCount(35 + $i * 3 + $source->value * 2);
                $imageTextStatistics->setAddToFavUser(5 + $i);
                $imageTextStatistics->setAddToFavCount(8 + $i);
                $manager->persist($imageTextStatistics);
                ++$recordIndex;
            }
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        if (null !== $imageTextStatistics) {
            $this->addReference(self::class . '_image_text_statistics_1', $imageTextStatistics);
        }
    }
}
