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
use WechatOfficialAccountStatsBundle\Command\SyncAdvertisingSpaceDataCommand;
use WechatOfficialAccountStatsBundle\Entity\AdvertisingSpaceData;
use WechatOfficialAccountStatsBundle\Repository\AdvertisingSpaceDataRepository;

/**
 * @internal
 */
#[CoversClass(SyncAdvertisingSpaceDataCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncAdvertisingSpaceDataCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getContainer()->get(SyncAdvertisingSpaceDataCommand::class);
        $this->assertInstanceOf(SyncAdvertisingSpaceDataCommand::class, $command);

        return new CommandTester($command);
    }

    private function createCommand(?OfficialAccountClient $mockClient = null): SyncAdvertisingSpaceDataCommand
    {
        if (null !== $mockClient) {
            self::getContainer()->set(OfficialAccountClient::class, $mockClient);
        }

        return self::getService(SyncAdvertisingSpaceDataCommand::class);
    }

    public function testConstruct(): void
    {
        $command = self::getService(SyncAdvertisingSpaceDataCommand::class);
        $this->assertInstanceOf(SyncAdvertisingSpaceDataCommand::class, $command);
        $this->assertSame('wechat:official-account:sync-advertising-space-data', $command->getName());
        $this->assertSame('公众号-获取公众号分广告位数据', $command->getDescription());
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
                        'date' => '2024-01-01',
                        'slot_id' => '123',
                        'ad_slot' => 'test_slot',
                        'req_succ_count' => '100',
                        'exposure_rate_count' => '80',
                        'click_count' => '10',
                        'click_rate' => '0.1',
                        'income' => '1000',
                        'ecpm' => '10.5',
                    ],
                ],
            ])
        ;

        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $result);

        $advertisingSpaceDataRepository = self::getService(AdvertisingSpaceDataRepository::class);
        $savedData = $advertisingSpaceDataRepository->findOneBy(['account' => $account]);
        $this->assertInstanceOf(AdvertisingSpaceData::class, $savedData);
        $this->assertSame(123, $savedData->getSlotId());
        $this->assertSame('test_slot', $savedData->getAdSlot());
        $this->assertSame(100, $savedData->getReqSuccCount());
        $this->assertSame(80, $savedData->getExposureCount());
        $this->assertSame(10, $savedData->getClickCount());
        $this->assertSame('0.1', $savedData->getClickRate());
        $this->assertSame(1000, $savedData->getIncome());
        $this->assertSame('10.5', $savedData->getEcpm());

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

        $advertisingSpaceDataRepository = self::getService(AdvertisingSpaceDataRepository::class);
        $savedData = $advertisingSpaceDataRepository->findOneBy(['account' => $account]);
        $this->assertNull($savedData);

        $entityManager->remove($account);
        $entityManager->flush();
    }
}
