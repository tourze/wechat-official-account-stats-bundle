<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\RebateGoodsData;

#[When(env: 'test')]
#[When(env: 'dev')]
class RebateGoodsDataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号-返佣商品');
        $account->setAppId('test_official_account_rb_001');
        $account->setAppSecret('test_secret_rb_001');
        $account->setValid(true);
        $manager->persist($account);

        
        $rebateGoodsData = null;
        for ($i = 1; $i <= 10; ++$i) {
            $rebateGoodsData = new RebateGoodsData();
            $rebateGoodsData->setAccount($account);
            $rebateGoodsData->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $i)));
            $rebateGoodsData->setExposureCount(10000 + $i * 1000);
            $rebateGoodsData->setClickCount(500 + $i * 50);
            $rebateGoodsData->setClickRate(sprintf('%.2f%%', 5.0 + $i * 0.5));
            $rebateGoodsData->setOrderCount(50 + $i * 5);
            $rebateGoodsData->setOrderRate(sprintf('%.2f%%', 10.0 + $i));
            $rebateGoodsData->setTotalFee(10000 + $i * 1000);
            $rebateGoodsData->setTotalCommission(1000 + $i * 100);
            $manager->persist($rebateGoodsData);
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        // 循环至少执行一次，所以 $rebateGoodsData 一定有值
        /** @var RebateGoodsData $rebateGoodsData */
        $this->addReference(self::class . '_rebate_goods_data_1', $rebateGoodsData);
    }
}
