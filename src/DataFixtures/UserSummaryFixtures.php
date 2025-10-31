<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\UserSummary;
use WechatOfficialAccountStatsBundle\Enum\UserSummarySource;

#[When(env: 'test')]
#[When(env: 'dev')]
class UserSummaryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号');
        $account->setAppId('test_official_account_us_001');
        $account->setAppSecret('test_secret_us_001');
        $account->setValid(true);
        $manager->persist($account);

        // 创建测试数据
        $sources = UserSummarySource::cases();

        
        $userSummary = null;
        for ($i = 1; $i <= 10; ++$i) {
            foreach ($sources as $source) {
                $userSummary = new UserSummary();
                $userSummary->setAccount($account);
                $userSummary->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $i)));
                $userSummary->setSource($source);
                $userSummary->setNewUser(100 + $i * 10 + $source->value * 5);
                $userSummary->setCancelUser(5 + $i + $source->value);
                $manager->persist($userSummary);
            }
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        // 循环至少执行一次，所以 $userSummary 一定有值
        /** @var UserSummary $userSummary */
        $this->addReference(self::class . '_user_summary_1', $userSummary);
    }
}
