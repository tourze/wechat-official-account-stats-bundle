<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Command\SyncMessageSendMonthDataCommand;
use WechatOfficialAccountStatsBundle\Entity\MessageSendMonthData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSendMonthDataRepository;

/**
 * @internal
 */
#[CoversClass(SyncMessageSendMonthDataCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncMessageSendMonthDataCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getContainer()->get(SyncMessageSendMonthDataCommand::class);
        $this->assertInstanceOf(SyncMessageSendMonthDataCommand::class, $command);

        return new CommandTester($command);
    }

    private function createCommand(?OfficialAccountClient $mockClient = null): SyncMessageSendMonthDataCommand
    {
        if (null !== $mockClient) {
            self::getContainer()->set(OfficialAccountClient::class, $mockClient);
        }

        return self::getService(SyncMessageSendMonthDataCommand::class);
    }

    public function testConstruct(): void
    {
        $command = self::getService(SyncMessageSendMonthDataCommand::class);
        $this->assertInstanceOf(SyncMessageSendMonthDataCommand::class, $command);
        $this->assertSame('wechat:official-account:sync-message-send-month-data', $command->getName());
        $this->assertSame('公众号-获取消息发送月数据', $command->getDescription());
    }

    public function testExecuteWithNoValidAccounts(): void
    {
        $mockClient = $this->createMock(OfficialAccountClient::class);
        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $result);
        $output = $commandTester->getDisplay();
        $this->assertIsString($output);
    }

    public function testExecuteWithValidAccountsButNoApiResponse(): void
    {
        $account = new Account();
        $account->setAppId('test_app_id');
        $account->setAppSecret('test_app_secret');
        $account->setValid(true);
        $account->setName('测试账号');

        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->persist($account);
        $entityManager->flush();

        $mockClient = $this->createMock(OfficialAccountClient::class);
        $mockClient->method('request')
            ->willReturn([])
        ;
        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $result);
        $output = $commandTester->getDisplay();
        $this->assertIsString($output);

        $entityManager->remove($account);
        $entityManager->flush();
    }

    public function testExecuteWithMockedSuccessfulApiResponse(): void
    {
        $account = new Account();
        $account->setAppId('test_app_id');
        $account->setAppSecret('test_app_secret');
        $account->setValid(true);
        $account->setName('测试账号');

        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->persist($account);
        $entityManager->flush();

        $mockClient = $this->createMock(OfficialAccountClient::class);
        $mockClient->method('request')
            ->willReturn([
                'list' => [
                    [
                        'ref_date' => '2024-01',
                        'msg_type' => 1,
                        'msg_user' => 15000,
                        'msg_count' => 30000,
                    ],
                ],
            ])
        ;

        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $result);

        $messageSendMonthDataRepository = self::getService(MessageSendMonthDataRepository::class);
        $savedData = $messageSendMonthDataRepository->findOneBy(['account' => $account]);
        $this->assertInstanceOf(MessageSendMonthData::class, $savedData);
        $this->assertSame(MessageSendDataTypeEnum::TEXT, $savedData->getMsgType());
        $this->assertSame(15000, $savedData->getMsgUser());
        $this->assertSame(30000, $savedData->getMsgCount());

        $entityManager->remove($account);
        $entityManager->remove($savedData);
        $entityManager->flush();
    }

    public function testExecuteWithApiErrorResponse(): void
    {
        $account = new Account();
        $account->setAppId('test_app_id');
        $account->setAppSecret('test_app_secret');
        $account->setValid(true);
        $account->setName('测试账号');

        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->persist($account);
        $entityManager->flush();

        $mockClient = $this->createMock(OfficialAccountClient::class);
        $mockClient->method('request')
            ->willReturn([
                'errcode' => 40001,
                'errmsg' => 'invalid credential',
            ])
        ;

        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $result);

        $messageSendMonthDataRepository = self::getService(MessageSendMonthDataRepository::class);
        $savedData = $messageSendMonthDataRepository->findOneBy(['account' => $account]);
        $this->assertNull($savedData);

        $entityManager->remove($account);
        $entityManager->flush();
    }
}
