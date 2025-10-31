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
use WechatOfficialAccountStatsBundle\Command\SyncUserSummaryCommand;
use WechatOfficialAccountStatsBundle\Entity\UserSummary;
use WechatOfficialAccountStatsBundle\Enum\UserSummarySource;
use WechatOfficialAccountStatsBundle\Repository\UserSummaryRepository;

/**
 * @internal
 */
#[CoversClass(SyncUserSummaryCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncUserSummaryCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getContainer()->get(SyncUserSummaryCommand::class);
        $this->assertInstanceOf(SyncUserSummaryCommand::class, $command);

        return new CommandTester($command);
    }

    private function createCommand(?OfficialAccountClient $mockClient = null): SyncUserSummaryCommand
    {
        if (null !== $mockClient) {
            self::getContainer()->set(OfficialAccountClient::class, $mockClient);
        }

        return self::getService(SyncUserSummaryCommand::class);
    }

    public function testConstruct(): void
    {
        $command = self::getService(SyncUserSummaryCommand::class);
        $this->assertInstanceOf(SyncUserSummaryCommand::class, $command);
        $this->assertSame('wechat:official-account:sync-user-summary', $command->getName());
        $this->assertSame('公众号-获取用户增减数据', $command->getDescription());
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
                        'user_source' => 0,
                        'new_user' => 10,
                        'cancel_user' => 2,
                    ],
                ],
            ])
        ;

        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $result);

        $userSummaryRepository = self::getService(UserSummaryRepository::class);
        $savedData = $userSummaryRepository->findOneBy(['account' => $account]);
        $this->assertInstanceOf(UserSummary::class, $savedData);
        $this->assertSame(UserSummarySource::OTHER, $savedData->getSource());
        $this->assertSame(10, $savedData->getNewUser());
        $this->assertSame(2, $savedData->getCancelUser());

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

        $userSummaryRepository = self::getService(UserSummaryRepository::class);
        $savedData = $userSummaryRepository->findOneBy(['account' => $account]);
        $this->assertNull($savedData);

        $entityManager->remove($account);
        $entityManager->flush();
    }
}
