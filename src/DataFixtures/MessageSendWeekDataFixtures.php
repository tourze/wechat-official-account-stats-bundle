<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\MessageSendWeekData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;

#[When(env: 'test')]
#[When(env: 'dev')]
class MessageSendWeekDataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号-消息周数据');
        $account->setAppId('test_official_account_msgw_001');
        $account->setAppSecret('test_secret_msgw_001');
        $account->setValid(true);
        $manager->persist($account);

        // 创建测试数据
        $msgTypes = MessageSendDataTypeEnum::cases();

        
        $messageSendWeekData = null;
        for ($i = 1; $i <= 12; ++$i) {
            foreach ($msgTypes as $msgType) {
                $messageSendWeekData = new MessageSendWeekData();
                $messageSendWeekData->setAccount($account);
                // 每周的第一天（周一）
                $date = new \DateTimeImmutable('2024-01-01');
                $date = $date->modify('+' . ($i - 1) . ' weeks');
                $messageSendWeekData->setDate($date);
                $messageSendWeekData->setMsgType($msgType);
                $messageSendWeekData->setMsgUser(200 + $i * 20 + $msgType->value * 40);
                $messageSendWeekData->setMsgCount(300 + $i * 30 + $msgType->value * 60);
                $manager->persist($messageSendWeekData);
            }
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        // 循环至少执行一次，所以 $messageSendWeekData 一定有值
        /** @var MessageSendWeekData $messageSendWeekData */
        $this->addReference(self::class . '_message_send_week_data_1', $messageSendWeekData);
    }
}
