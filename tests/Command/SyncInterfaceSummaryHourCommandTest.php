<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Command;

use Carbon\CarbonImmutable;
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
use WechatOfficialAccountStatsBundle\Command\SyncInterfaceSummaryHourCommand;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummaryHour;
use WechatOfficialAccountStatsBundle\Repository\InterfaceSummaryHourRepository;

/**
 * @internal
 */
#[CoversClass(SyncInterfaceSummaryHourCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncInterfaceSummaryHourCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getContainer()->get(SyncInterfaceSummaryHourCommand::class);
        $this->assertInstanceOf(SyncInterfaceSummaryHourCommand::class, $command);

        return new CommandTester($command);
    }

    private function createCommand(?OfficialAccountClient $mockClient = null): SyncInterfaceSummaryHourCommand
    {
        if (null !== $mockClient) {
            self::getContainer()->set(OfficialAccountClient::class, $mockClient);
        }

        return self::getService(SyncInterfaceSummaryHourCommand::class);
    }

    public function testConstruct(): void
    {
        $command = self::getService(SyncInterfaceSummaryHourCommand::class);
        $this->assertInstanceOf(SyncInterfaceSummaryHourCommand::class, $command);
        $this->assertSame('wechat:official-account:sync-interface-summary-hour', $command->getName());
        $this->assertSame('公众号-获取接口分析数据by hour', $command->getDescription());
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
                        'ref_hour' => '12',
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

        $interfaceSummaryHourRepository = self::getService(InterfaceSummaryHourRepository::class);
        $savedData = $interfaceSummaryHourRepository->findOneBy(['account' => $account]);
        $this->assertInstanceOf(InterfaceSummaryHour::class, $savedData);
        $this->assertSame(12, $savedData->getRefHour());
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

        $interfaceSummaryHourRepository = self::getService(InterfaceSummaryHourRepository::class);
        $savedData = $interfaceSummaryHourRepository->findOneBy(['account' => $account]);
        $this->assertNull($savedData);

        $entityManager->remove($account);
        $entityManager->flush();
    }

    public function testExecuteWithCustomStartTime(): void
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
            ->willReturn(['list' => []])
        ;

        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute(['startTime' => CarbonImmutable::now()->subDays(2)->format('Y-m-d')]);

        $this->assertSame(Command::SUCCESS, $result);
        $output = $commandTester->getDisplay();
        $this->assertIsString($output);

        $entityManager->remove($account);
        $entityManager->flush();
    }

    public function testExecuteWithInvalidDateRange(): void
    {
        $mockClient = $this->createMock(OfficialAccountClient::class);
        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([
            'startTime' => '2024-01-01',
            'endTime' => '2024-03-01',
        ]);

        $this->assertSame(Command::FAILURE, $result);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('开始时间和结束时间的跨度不能超过 30 天', $output);
    }

    public function testArgumentStartTime(): void
    {
        $command = self::getService(SyncInterfaceSummaryHourCommand::class);
        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasArgument('startTime'));
        $argument = $definition->getArgument('startTime');
        $this->assertFalse($argument->isRequired());
        $this->assertSame('order start time', $argument->getDescription());
    }

    public function testArgumentEndTime(): void
    {
        $command = self::getService(SyncInterfaceSummaryHourCommand::class);
        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasArgument('endTime'));
        $argument = $definition->getArgument('endTime');
        $this->assertFalse($argument->isRequired());
        $this->assertSame('order end time', $argument->getDescription());
    }
}
