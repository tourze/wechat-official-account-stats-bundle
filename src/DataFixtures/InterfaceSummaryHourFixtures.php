<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummaryHour;

#[When(env: 'test')]
#[When(env: 'dev')]
class InterfaceSummaryHourFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号');
        $account->setAppId('test_official_account_ifh_001');
        $account->setAppSecret('test_secret_ifh_001');
        $account->setValid(true);
        $manager->persist($account);

        // 创建测试数据

        $interfaceSummaryHour = null;
        for ($i = 1; $i <= 3; ++$i) {
            for ($hour = 0; $hour < 24; ++$hour) {
                $interfaceSummaryHour = new InterfaceSummaryHour();
                $interfaceSummaryHour->setAccount($account);
                $interfaceSummaryHour->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $i)));
                $interfaceSummaryHour->setRefHour($hour);
                $interfaceSummaryHour->setCallbackCount(100 + $hour * 5 + $i * 10);
                $interfaceSummaryHour->setFailCount(1 + $hour + $i);
                $interfaceSummaryHour->setTotalTimeCost(5000 + $hour * 200 + $i * 500);
                $interfaceSummaryHour->setMaxTimeCost(100 + $hour * 10 + $i * 20);
                $manager->persist($interfaceSummaryHour);
            }
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        // 循环至少执行一次，所以 $interfaceSummaryHour 一定有值
        /** @var InterfaceSummaryHour $interfaceSummaryHour */
        $this->addReference(self::class . '_interface_summary_hour_1', $interfaceSummaryHour);
    }
}
