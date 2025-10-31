<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\MessageSenDistData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataCountIntervalEnum;

#[When(env: 'test')]
#[When(env: 'dev')]
class MessageSenDistDataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号');
        $account->setAppId('test_official_account_msgd_001');
        $account->setAppSecret('test_secret_msgd_001');
        $account->setValid(true);
        $manager->persist($account);

        // 创建测试数据
        $intervals = MessageSendDataCountIntervalEnum::cases();

        
        $messageSenDistData = null;
        for ($i = 1; $i <= 8; ++$i) {
            foreach ($intervals as $interval) {
                $messageSenDistData = new MessageSenDistData();
                $messageSenDistData->setAccount($account);
                $messageSenDistData->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $i)));
                $messageSenDistData->setCountInterval($interval);
                $messageSenDistData->setMsgUser(100 + $i * 10 + $interval->value * 20);
                $manager->persist($messageSenDistData);
            }
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        // 循环至少执行一次，所以 $messageSenDistData 一定有值
        /** @var MessageSenDistData $messageSenDistData */
        $this->addReference(self::class . '_message_send_dist_data_1', $messageSenDistData);
    }
}
