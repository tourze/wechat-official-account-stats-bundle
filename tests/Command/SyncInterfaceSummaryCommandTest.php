<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Command\SyncInterfaceSummaryCommand;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummary;
use WechatOfficialAccountStatsBundle\Repository\InterfaceSummaryRepository;

/**
 * @internal
 */
#[CoversClass(SyncInterfaceSummaryCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncInterfaceSummaryCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getContainer()->get(SyncInterfaceSummaryCommand::class);
        $this->assertInstanceOf(SyncInterfaceSummaryCommand::class, $command);

        return new CommandTester($command);
    }

    private function createCommand(?OfficialAccountClient $mockClient = null): SyncInterfaceSummaryCommand
    {
        if (null !== $mockClient) {
            self::getContainer()->set(OfficialAccountClient::class, $mockClient);
        }

        return self::getService(SyncInterfaceSummaryCommand::class);
    }

    public function testConstruct(): void
    {
        $command = self::getService(SyncInterfaceSummaryCommand::class);
        $this->assertInstanceOf(SyncInterfaceSummaryCommand::class, $command);
        $this->assertSame('wechat:official-account:sync-interface-summary', $command->getName());
        $this->assertSame('公众号-获取接口分析数据', $command->getDescription());
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
                        'ref_date' => '2024-01-01',
                        'callback_count' => 100,
                        'fail_count' => 5,
                        'max_time_cost' => 1500,
                        'total_time_cost' => 50000,
                    ],
                ],
            ])
        ;

        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $result);

        $interfaceSummaryRepository = self::getService(InterfaceSummaryRepository::class);
        $savedData = $interfaceSummaryRepository->findOneBy(['account' => $account]);
        $this->assertInstanceOf(InterfaceSummary::class, $savedData);
        $this->assertSame(100, $savedData->getCallbackCount());
        $this->assertSame(5, $savedData->getFailCount());
        $this->assertSame(1500, $savedData->getMaxTimeCost());
        $this->assertSame(50000, $savedData->getTotalTimeCost());

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

        $interfaceSummaryRepository = self::getService(InterfaceSummaryRepository::class);
        $savedData = $interfaceSummaryRepository->findOneBy(['account' => $account]);
        $this->assertNull($savedData);

        $entityManager->remove($account);
        $entityManager->flush();
    }

    public function testArgumentStartTime(): void
    {
        $mockClient = $this->createMock(OfficialAccountClient::class);
        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        // 测试自定义开始时间（使用近期日期避免超过30天）
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $result = $commandTester->execute(['startTime' => $yesterday]);
        $this->assertSame(Command::SUCCESS, $result);

        // 测试跨度超过30天的情况
        $result = $commandTester->execute([
            'startTime' => '2024-01-01',
            'endTime' => '2024-03-01',
        ]);
        $this->assertSame(Command::FAILURE, $result);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('开始时间和结束时间的跨度不能超过 30 天', $output);

        // 测试正常的日期范围
        $result = $commandTester->execute([
            'startTime' => '2024-01-01',
            'endTime' => '2024-01-15',
        ]);
        $this->assertSame(Command::SUCCESS, $result);
    }

    public function testArgumentEndTime(): void
    {
        $mockClient = $this->createMock(OfficialAccountClient::class);
        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        // 测试自定义结束时间（使用近期日期避免超过30天）
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $result = $commandTester->execute(['endTime' => $yesterday]);
        $this->assertSame(Command::SUCCESS, $result);

        // 测试跨度超过30天的情况（正向时间差）
        $result = $commandTester->execute([
            'startTime' => '2024-01-01',
            'endTime' => '2024-02-15',
        ]);
        $this->assertSame(Command::FAILURE, $result);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('开始时间和结束时间的跨度不能超过 30 天', $output);

        // 测试正常的结束时间
        $result = $commandTester->execute([
            'startTime' => '2024-01-01',
            'endTime' => '2024-01-31',
        ]);
        $this->assertSame(Command::SUCCESS, $result);
    }
}
