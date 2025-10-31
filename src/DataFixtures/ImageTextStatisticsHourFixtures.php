<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatisticsHour;
use WechatOfficialAccountStatsBundle\Enum\ImageTextUserSourceEnum;

#[When(env: 'test')]
#[When(env: 'dev')]
class ImageTextStatisticsHourFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号-图文分时统计');
        $account->setAppId('test_official_account_itsh2_001');
        $account->setAppSecret('test_secret_itsh2_001');
        $account->setValid(true);
        $manager->persist($account);

        // 创建测试数据
        $sources = ImageTextUserSourceEnum::cases();

        // 确保变量总是有定义
        $imageTextStatisticsHour = null;

        // 创建几条记录，确保每个 account_id + date 组合都是唯一的
        $recordIndex = 1;
        foreach ($sources as $source) {
            for ($hour = 0; $hour < 3; ++$hour) {
                // 确保日期不超过 1 月 31 日
                if ($recordIndex > 31) {
                    break;
                }
                $imageTextStatisticsHour = new ImageTextStatisticsHour();
                $imageTextStatisticsHour->setAccount($account);
                $imageTextStatisticsHour->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $recordIndex)));
                $imageTextStatisticsHour->setRefHour($hour);
                $imageTextStatisticsHour->setIntPageReadUser(20 + $hour + $source->value * 2);
                $imageTextStatisticsHour->setIntPageReadCount(30 + $hour + $source->value * 3);
                $imageTextStatisticsHour->setOriPageReadUser(5 + $hour + $source->value);
                $imageTextStatisticsHour->setOriPageReadCount(8 + $hour + $source->value * 2);
                $imageTextStatisticsHour->setShareUser(2 + $hour);
                $imageTextStatisticsHour->setShareCount(3 + $hour);
                $imageTextStatisticsHour->setAddToFavUser(1);
                $imageTextStatisticsHour->setAddToFavCount(1);
                $manager->persist($imageTextStatisticsHour);
                ++$recordIndex;
            }
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        if (null !== $imageTextStatisticsHour) {
            $this->addReference(self::class . '_image_text_statistics_hour_1', $imageTextStatisticsHour);
        }
    }
}
