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
use WechatOfficialAccountStatsBundle\Command\SyncImageTextStatisticsCommand;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatistics;
use WechatOfficialAccountStatsBundle\Repository\ImageTextStatisticsRepository;

/**
 * @internal
 */
#[CoversClass(SyncImageTextStatisticsCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncImageTextStatisticsCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getContainer()->get(SyncImageTextStatisticsCommand::class);
        $this->assertInstanceOf(SyncImageTextStatisticsCommand::class, $command);

        return new CommandTester($command);
    }

    private function createCommand(?OfficialAccountClient $mockClient = null): SyncImageTextStatisticsCommand
    {
        if (null !== $mockClient) {
            self::getContainer()->set(OfficialAccountClient::class, $mockClient);
        }

        return self::getService(SyncImageTextStatisticsCommand::class);
    }

    public function testConstruct(): void
    {
        $command = self::getService(SyncImageTextStatisticsCommand::class);
        $this->assertInstanceOf(SyncImageTextStatisticsCommand::class, $command);
        $this->assertSame('wechat:official-account:sync-image-text-statistics', $command->getName());
        $this->assertSame('公众号-获取图文统计数据', $command->getDescription());
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
                        'user_source' => 1,
                        'int_page_read_user' => 100,
                        'int_page_read_count' => 200,
                        'ori_page_read_user' => 80,
                        'ori_page_read_count' => 120,
                        'share_user' => 50,
                        'share_count' => 60,
                        'add_to_fav_user' => 20,
                        'add_to_fav_count' => 25,
                    ],
                ],
            ])
        ;

        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $result);

        $imageTextStatisticsRepository = self::getService(ImageTextStatisticsRepository::class);
        $savedData = $imageTextStatisticsRepository->findOneBy(['account' => $account]);
        $this->assertInstanceOf(ImageTextStatistics::class, $savedData);
        $this->assertSame(100, $savedData->getIntPageReadUser());
        $this->assertSame(200, $savedData->getIntPageReadCount());
        $this->assertSame(80, $savedData->getOriPageReadUser());
        $this->assertSame(120, $savedData->getOriPageReadCount());
        $this->assertSame(50, $savedData->getShareUser());
        $this->assertSame(60, $savedData->getShareCount());
        $this->assertSame(20, $savedData->getAddToFavUser());
        $this->assertSame(25, $savedData->getAddToFavCount());

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

        $imageTextStatisticsRepository = self::getService(ImageTextStatisticsRepository::class);
        $savedData = $imageTextStatisticsRepository->findOneBy(['account' => $account]);
        $this->assertNull($savedData);

        $entityManager->remove($account);
        $entityManager->flush();
    }
}
