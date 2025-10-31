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
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Command\SyncImageTextShareDataHourCommand;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareDataHour;
use WechatOfficialAccountStatsBundle\Repository\ImageTextShareDataHourRepository;

/**
 * @internal
 */
#[CoversClass(SyncImageTextShareDataHourCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncImageTextShareDataHourCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getContainer()->get(SyncImageTextShareDataHourCommand::class);
        $this->assertInstanceOf(SyncImageTextShareDataHourCommand::class, $command);

        return new CommandTester($command);
    }

    private function createCommand(?OfficialAccountClient $mockClient = null): SyncImageTextShareDataHourCommand
    {
        if (null !== $mockClient) {
            self::getContainer()->set(OfficialAccountClient::class, $mockClient);
        }

        return self::getService(SyncImageTextShareDataHourCommand::class);
    }

    public function testConstruct(): void
    {
        $command = self::getService(SyncImageTextShareDataHourCommand::class);
        $this->assertInstanceOf(SyncImageTextShareDataHourCommand::class, $command);
        $this->assertSame('wechat:official-account:sync-image-text-share-data-hour', $command->getName());
        $this->assertSame('公众号-获取图文分享转发分时数据', $command->getDescription());
    }

    public function testExecuteWithNoValidAccounts(): void
    {
        $entityManager = self::getService(EntityManagerInterface::class);
        $accountRepository = self::getService(AccountRepository::class);
        foreach ($accountRepository->findBy(['valid' => true]) as $account) {
            $account->setValid(false);
            $entityManager->persist($account);
        }
        $entityManager->flush();

        $mockClient = $this->createMock(OfficialAccountClient::class);
        $mockClient->expects($this->never())->method('request');

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
            ->willReturn([
                'errcode' => 0,
                'errmsg' => 'ok',
                'list' => [],
            ])
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
                        'ref_hour' => 12,
                        'share_scene' => 1,
                        'share_count' => 150,
                        'share_user' => 75,
                    ],
                ],
            ])
        ;

        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $result);

        $repository = self::getService(ImageTextShareDataHourRepository::class);
        $savedData = $repository->findOneBy(['account' => $account]);
        $this->assertInstanceOf(ImageTextShareDataHour::class, $savedData);
        $this->assertSame(12, $savedData->getRefHour());
        $this->assertSame(1, $savedData->getShareScene());
        $this->assertSame(150, $savedData->getShareCount());
        $this->assertSame(75, $savedData->getShareUser());

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

        $repository = self::getService(ImageTextShareDataHourRepository::class);
        $savedData = $repository->findOneBy(['account' => $account]);
        $this->assertNull($savedData);

        $entityManager->remove($account);
        $entityManager->flush();
    }
}
