<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\MessageSendMonthData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;

#[When(env: 'test')]
#[When(env: 'dev')]
class MessageSendMonthDataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号-消息月数据');
        $account->setAppId('test_official_account_msgm_001');
        $account->setAppSecret('test_secret_msgm_001');
        $account->setValid(true);
        $manager->persist($account);

        // 创建测试数据
        $msgTypes = MessageSendDataTypeEnum::cases();

        
        $messageSendMonthData = null;
        for ($i = 1; $i <= 6; ++$i) {
            foreach ($msgTypes as $msgType) {
                $messageSendMonthData = new MessageSendMonthData();
                $messageSendMonthData->setAccount($account);
                $messageSendMonthData->setDate(new \DateTimeImmutable(sprintf('2024-%02d-01', $i)));
                $messageSendMonthData->setMsgType($msgType);
                $messageSendMonthData->setMsgUser(500 + $i * 50 + $msgType->value * 100);
                $messageSendMonthData->setMsgCount(800 + $i * 80 + $msgType->value * 150);
                $manager->persist($messageSendMonthData);
            }
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        // 循环至少执行一次，所以 $messageSendMonthData 一定有值
        /** @var MessageSendMonthData $messageSendMonthData */
        $this->addReference(self::class . '_message_send_month_data_1', $messageSendMonthData);
    }
}
