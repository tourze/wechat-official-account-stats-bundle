<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\MessageSendData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;

#[When(env: 'test')]
#[When(env: 'dev')]
class MessageSendDataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号');
        $account->setAppId('test_official_account_msg_001');
        $account->setAppSecret('test_secret_msg_001');
        $account->setValid(true);
        $manager->persist($account);

        // 创建测试数据
        $msgTypes = MessageSendDataTypeEnum::cases();

        
        $messageSendData = null;
        for ($i = 1; $i <= 10; ++$i) {
            foreach ($msgTypes as $msgType) {
                $messageSendData = new MessageSendData();
                $messageSendData->setAccount($account);
                $messageSendData->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $i)));
                $messageSendData->setMsgType($msgType);
                $messageSendData->setMsgUser(50 + $i * 5 + $msgType->value * 10);
                $messageSendData->setMsgCount(80 + $i * 8 + $msgType->value * 15);
                $manager->persist($messageSendData);
            }
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        // 循环至少执行一次，所以 $messageSendData 一定有值
        /** @var MessageSendData $messageSendData */
        $this->addReference(self::class . '_message_send_data_1', $messageSendData);
    }
}
