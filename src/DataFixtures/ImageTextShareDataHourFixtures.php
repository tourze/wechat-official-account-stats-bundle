<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareDataHour;

#[When(env: 'test')]
#[When(env: 'dev')]
class ImageTextShareDataHourFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号');
        $account->setAppId('test_official_account_itsh_001');
        $account->setAppSecret('test_secret_itsh_001');
        $account->setValid(true);
        $manager->persist($account);

        // 分享场景数据
        $shareScenes = [
            1 => '好友分享',
            2 => '朋友圈分享',
            3 => '腾讯微博分享',
            4 => 'QQ分享',
            5 => 'QQ空间分享',
        ];

        
        $imageTextShareDataHour = null;
        // 创建几条记录，确保每个 account_id + date 组合都是唯一的
        $recordIndex = 1;
        foreach ($shareScenes as $scene => $description) {
            for ($hour = 0; $hour < 2; ++$hour) {
                $imageTextShareDataHour = new ImageTextShareDataHour();
                $imageTextShareDataHour->setAccount($account);
                $imageTextShareDataHour->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $recordIndex)));
                $imageTextShareDataHour->setRefHour($hour);
                $imageTextShareDataHour->setShareScene($scene);
                $imageTextShareDataHour->setShareCount(10 + $hour + $scene);
                $imageTextShareDataHour->setShareUser(8 + $hour + $scene);
                $manager->persist($imageTextShareDataHour);
                ++$recordIndex;
            }
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        // 循环至少执行一次，所以 $imageTextShareDataHour 一定有值
        /** @var ImageTextShareDataHour $imageTextShareDataHour */
        $this->addReference(self::class . '_share_data_hour_1', $imageTextShareDataHour);
    }
}
