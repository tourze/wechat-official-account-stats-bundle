<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareData;

#[When(env: 'test')]
#[When(env: 'dev')]
class ImageTextShareDataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号');
        $account->setAppId('test_official_account_its_001');
        $account->setAppSecret('test_secret_its_001');
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

        // 确保变量总是有定义
        $imageTextShareData = null;

        // 为每个分享场景创建一条记录，使用不同的日期避免冲突
        $sceneIndex = 1;
        foreach ($shareScenes as $scene => $description) {
            $imageTextShareData = new ImageTextShareData();
            $imageTextShareData->setAccount($account);
            $imageTextShareData->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $sceneIndex)));
            $imageTextShareData->setShareScene($scene);
            $imageTextShareData->setShareCount(100 + $scene * 20);
            $imageTextShareData->setShareUser(80 + $scene * 15);
            $manager->persist($imageTextShareData);
            ++$sceneIndex;
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        $this->addReference(self::class . '_share_data_1', $imageTextShareData);
    }
}
