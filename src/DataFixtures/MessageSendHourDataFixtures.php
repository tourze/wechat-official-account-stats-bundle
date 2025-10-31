<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\MessageSendHourData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;

#[When(env: 'test')]
#[When(env: 'dev')]
class MessageSendHourDataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号-消息分时');
        $account->setAppId('test_official_account_msgh_001');
        $account->setAppSecret('test_secret_msgh_001');
        $account->setValid(true);
        $manager->persist($account);

        // 创建测试数据 - 为每个小时创建记录

        $messageSendHourData = null;
        for ($hour = 0; $hour < 24; ++$hour) {
            $messageSendHourData = new MessageSendHourData();
            $messageSendHourData->setAccount($account);
            $messageSendHourData->setDate(new \DateTimeImmutable('2024-01-01'));
            $messageSendHourData->setRefHour($hour);
            $messageSendHourData->setMsgType(MessageSendDataTypeEnum::TEXT);
            $messageSendHourData->setMsgUser($hour * 10);
            $messageSendHourData->setMsgCount($hour * 15);
            $manager->persist($messageSendHourData);
        }

        // 创建第二天的数据（部分小时）
        for ($hour = 0; $hour < 12; ++$hour) {
            $messageSendHourData = new MessageSendHourData();
            $messageSendHourData->setAccount($account);
            $messageSendHourData->setDate(new \DateTimeImmutable('2024-01-02'));
            $messageSendHourData->setRefHour($hour);
            $messageSendHourData->setMsgType(MessageSendDataTypeEnum::IMAGE);
            $messageSendHourData->setMsgUser($hour * 5);
            $messageSendHourData->setMsgCount($hour * 8);
            $manager->persist($messageSendHourData);
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        // 循环至少执行一次，所以 $messageSendHourData 一定有值
        /** @var MessageSendHourData $messageSendHourData */
        $this->addReference(self::class . '_data_1', $messageSendHourData);
    }
}
