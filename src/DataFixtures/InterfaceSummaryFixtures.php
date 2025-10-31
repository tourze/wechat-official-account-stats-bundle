<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummary;

#[When(env: 'test')]
#[When(env: 'dev')]
class InterfaceSummaryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号');
        $account->setAppId('test_official_account_if_001');
        $account->setAppSecret('test_secret_if_001');
        $account->setValid(true);
        $manager->persist($account);

        // 创建测试数据
        $interfaceSummary = new InterfaceSummary();
        $interfaceSummary->setAccount($account);
        $interfaceSummary->setDate(new \DateTimeImmutable('2024-01-01'));
        $interfaceSummary->setCallbackCount(1000);
        $interfaceSummary->setFailCount(5);
        $interfaceSummary->setTotalTimeCost(50000);
        $interfaceSummary->setMaxTimeCost(200);
        $manager->persist($interfaceSummary);

        // 添加更多测试数据
        for ($i = 2; $i <= 5; ++$i) {
            $interfaceSummary = new InterfaceSummary();
            $interfaceSummary->setAccount($account);
            $interfaceSummary->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $i)));
            $interfaceSummary->setCallbackCount(1000 + $i * 100);
            $interfaceSummary->setFailCount($i);
            $interfaceSummary->setTotalTimeCost(50000 + $i * 5000);
            $interfaceSummary->setMaxTimeCost(200 + $i * 50);
            $manager->persist($interfaceSummary);
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        $this->addReference(self::class . '_summary_1', $interfaceSummary);
    }
}
