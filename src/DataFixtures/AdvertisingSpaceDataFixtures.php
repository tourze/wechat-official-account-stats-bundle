<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\AdvertisingSpaceData;

#[When(env: 'test')]
#[When(env: 'dev')]
class AdvertisingSpaceDataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号-广告位');
        $account->setAppId('test_official_account_ad_001');
        $account->setAppSecret('test_secret_ad_001');
        $account->setValid(true);
        $manager->persist($account);

        // 创建测试数据
        $adData = new AdvertisingSpaceData();
        $adData->setAccount($account);
        $adData->setDate(new \DateTimeImmutable('2024-01-01'));
        $adData->setSlotId(1);
        $adData->setAdSlot(' banner');
        $adData->setReqSuccCount(10000);
        $adData->setExposureCount(8000);
        $adData->setExposureRate('80.00%');
        $adData->setClickCount(400);
        $adData->setClickRate('5.00%');
        $adData->setIncome(50000);
        $adData->setEcpm('6.25');
        $manager->persist($adData);

        // 添加更多测试数据
        for ($i = 2; $i <= 5; ++$i) {
            $adData = new AdvertisingSpaceData();
            $adData->setAccount($account);
            $adData->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $i)));
            $adData->setSlotId($i);
            $adData->setAdSlot("广告位类型{$i}");
            $adData->setReqSuccCount(10000 + $i * 1000);
            $adData->setExposureCount(8000 + $i * 800);
            $adData->setExposureRate(sprintf('%.2f%%', 80 + $i));
            $adData->setClickCount(400 + $i * 40);
            $adData->setClickRate(sprintf('%.2f%%', 5 + $i * 0.5));
            $adData->setIncome(50000 + $i * 5000);
            $adData->setEcpm(sprintf('%.2f', 6.25 + $i * 0.5));
            $manager->persist($adData);
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        $this->addReference(self::class . '_ad_data_1', $adData);
    }
}
