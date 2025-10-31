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
use WechatOfficialAccountStatsBundle\Command\SyncArticleTotalCommand;
use WechatOfficialAccountStatsBundle\Entity\ArticleTotal;
use WechatOfficialAccountStatsBundle\Repository\ArticleTotalRepository;

/**
 * @internal
 */
#[CoversClass(SyncArticleTotalCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncArticleTotalCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getContainer()->get(SyncArticleTotalCommand::class);
        $this->assertInstanceOf(SyncArticleTotalCommand::class, $command);

        return new CommandTester($command);
    }

    private function createCommand(?OfficialAccountClient $mockClient = null): SyncArticleTotalCommand
    {
        if (null !== $mockClient) {
            self::getContainer()->set(OfficialAccountClient::class, $mockClient);
        }

        return self::getService(SyncArticleTotalCommand::class);
    }

    public function testConstruct(): void
    {
        $command = self::getService(SyncArticleTotalCommand::class);
        $this->assertInstanceOf(SyncArticleTotalCommand::class, $command);
        $this->assertSame('wechat:official-account:sync-article-total', $command->getName());
        $this->assertSame('公众号-获取图文群发总数据', $command->getDescription());
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
                        'msgId' => 'test_msg_id_456',
                        'title' => '测试文章总数据',
                        'details' => [
                            [
                                'stat_date' => '2024-01-01',
                                'target_user' => 1000,
                                'int_page_read_user' => 800,
                                'int_page_read_count' => 1200,
                                'ori_page_read_user' => 600,
                                'ori_page_read_count' => 900,
                                'share_user' => 100,
                                'share_count' => 120,
                                'add_to_fav_user' => 50,
                                'add_to_fav_count' => 60,
                                'int_page_from_session_read_user' => 400,
                                'int_page_from_session_read_count' => 500,
                                'int_page_from_hist_msg_read_user' => 200,
                                'int_page_from_hist_msg_read_count' => 250,
                                'int_page_from_feed_read_user' => 100,
                                'int_page_from_feed_read_count' => 150,
                                'int_page_from_friends_read_user' => 80,
                                'int_page_from_friends_read_count' => 100,
                                'int_page_from_other_read_user' => 20,
                                'int_page_from_other_read_count' => 30,
                                'feed_share_from_session_user' => 40,
                                'feed_share_from_session_cnt' => 50,
                                'feed_share_from_feed_user' => 30,
                                'feed_share_from_feed_cnt' => 35,
                                'feed_share_from_other_user' => 20,
                                'feed_share_from_other_cnt' => 25,
                            ],
                        ],
                    ],
                ],
            ])
        ;

        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $result);

        $articleTotalRepository = self::getService(ArticleTotalRepository::class);
        $savedData = $articleTotalRepository->findOneBy(['account' => $account]);
        $this->assertInstanceOf(ArticleTotal::class, $savedData);
        $this->assertSame('test_msg_id_456', $savedData->getMsgId());
        $this->assertSame('测试文章总数据', $savedData->getTitle());
        $this->assertSame(1000, $savedData->getTargetUser());
        $this->assertSame(800, $savedData->getIntPageReadUser());
        $this->assertSame(1200, $savedData->getIntPageReadCount());
        $this->assertSame(600, $savedData->getOriPageReadUser());
        $this->assertSame(900, $savedData->getOriPageReadCount());
        $this->assertSame(100, $savedData->getShareUser());
        $this->assertSame(120, $savedData->getShareCount());

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

        $articleTotalRepository = self::getService(ArticleTotalRepository::class);
        $savedData = $articleTotalRepository->findOneBy(['account' => $account]);
        $this->assertNull($savedData);

        $entityManager->remove($account);
        $entityManager->flush();
    }
}
