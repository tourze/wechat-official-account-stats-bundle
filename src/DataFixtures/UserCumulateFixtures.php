<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\UserCumulate;

#[When(env: 'test')]
#[When(env: 'dev')]
class UserCumulateFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号');
        $account->setAppId('test_official_account_uc_001');
        $account->setAppSecret('test_secret_uc_001');
        $account->setValid(true);
        $manager->persist($account);

        // 创建测试数据

        $userCumulate = null;
        for ($i = 1; $i <= 10; ++$i) {
            $userCumulate = new UserCumulate();
            $userCumulate->setAccount($account);
            $userCumulate->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $i)));
            $userCumulate->setCumulateUser(10000 + $i * 1000);
            $manager->persist($userCumulate);
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        // 循环至少执行一次，所以 $userCumulate 一定有值
        /** @var UserCumulate $userCumulate */
        $this->addReference(self::class . '_user_cumulate_1', $userCumulate);
    }
}
