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
use WechatOfficialAccountStatsBundle\Command\SyncArticleSummaryCommand;
use WechatOfficialAccountStatsBundle\Entity\ArticleDailySummary;
use WechatOfficialAccountStatsBundle\Repository\ArticleDailySummaryRepository;

/**
 * @internal
 */
#[CoversClass(SyncArticleSummaryCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncArticleSummaryCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getContainer()->get(SyncArticleSummaryCommand::class);
        $this->assertInstanceOf(SyncArticleSummaryCommand::class, $command);

        return new CommandTester($command);
    }

    private function createCommand(?OfficialAccountClient $mockClient = null): SyncArticleSummaryCommand
    {
        if (null !== $mockClient) {
            self::getContainer()->set(OfficialAccountClient::class, $mockClient);
        }

        return self::getService(SyncArticleSummaryCommand::class);
    }

    public function testConstruct(): void
    {
        $command = self::getService(SyncArticleSummaryCommand::class);
        $this->assertInstanceOf(SyncArticleSummaryCommand::class, $command);
        $this->assertSame('wechat:official-account:sync-article-summary', $command->getName());
        $this->assertSame('公众号-获取图文群发每日数据', $command->getDescription());
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
                        'msgId' => 'test_msg_id_123',
                        'title' => '测试文章标题',
                        'int_page_read_user' => 100,
                        'int_page_read_count' => 150,
                        'ori_page_read_user' => 80,
                        'ori_page_read_count' => 120,
                        'share_user' => 20,
                        'share_count' => 25,
                        'add_to_fav_user' => 15,
                        'add_to_fav_count' => 18,
                    ],
                ],
            ])
        ;

        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $result);

        $articleSummaryRepository = self::getService(ArticleDailySummaryRepository::class);
        $savedData = $articleSummaryRepository->findOneBy(['account' => $account]);
        $this->assertInstanceOf(ArticleDailySummary::class, $savedData);
        $this->assertSame('test_msg_id_123', $savedData->getMsgId());
        $this->assertSame('测试文章标题', $savedData->getTitle());
        $this->assertSame(100, $savedData->getIntPageReadUser());
        $this->assertSame(150, $savedData->getIntPageReadCount());
        $this->assertSame(80, $savedData->getOriPageReadUser());
        $this->assertSame(120, $savedData->getOriPageReadCount());
        $this->assertSame(20, $savedData->getShareUser());
        $this->assertSame(25, $savedData->getShareCount());
        $this->assertSame(15, $savedData->getAddToFavUser());
        $this->assertSame(18, $savedData->getAddToFavCount());

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

        $articleSummaryRepository = self::getService(ArticleDailySummaryRepository::class);
        $savedData = $articleSummaryRepository->findOneBy(['account' => $account]);
        $this->assertNull($savedData);

        $entityManager->remove($account);
        $entityManager->flush();
    }

    public function testExecuteWithExistingArticleSummaryUpdate(): void
    {
        $account = new Account();
        $account->setAppId('test_app_id');
        $account->setAppSecret('test_app_secret');
        $account->setValid(true);
        $account->setName('测试账号');

        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->persist($account);
        $entityManager->flush();

        $existingArticle = new ArticleDailySummary();
        $existingArticle->setAccount($account);
        $existingArticle->setDate(new \DateTimeImmutable('2024-01-01'));
        $existingArticle->setMsgId('test_msg_id_123');
        $existingArticle->setTitle('旧标题');
        $existingArticle->setIntPageReadUser(50);

        $entityManager->persist($existingArticle);
        $entityManager->flush();

        $mockClient = $this->createMock(OfficialAccountClient::class);
        $mockClient->method('request')
            ->willReturn([
                'list' => [
                    [
                        'ref_date' => '2024-01-01',
                        'msgId' => 'test_msg_id_123',
                        'title' => '更新后的标题',
                        'int_page_read_user' => 200,
                        'int_page_read_count' => 250,
                        'ori_page_read_user' => 180,
                        'ori_page_read_count' => 220,
                        'share_user' => 30,
                        'share_count' => 35,
                        'add_to_fav_user' => 25,
                        'add_to_fav_count' => 28,
                    ],
                ],
            ])
        ;

        $command = $this->createCommand($mockClient);
        $commandTester = new CommandTester($command);

        $result = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $result);

        $articleSummaryRepository = self::getService(ArticleDailySummaryRepository::class);
        $updatedData = $articleSummaryRepository->findOneBy(['account' => $account, 'msgId' => 'test_msg_id_123']);
        $this->assertInstanceOf(ArticleDailySummary::class, $updatedData);
        $this->assertSame('更新后的标题', $updatedData->getTitle());
        $this->assertSame(200, $updatedData->getIntPageReadUser());
        $this->assertSame(250, $updatedData->getIntPageReadCount());

        $entityManager->remove($account);
        $entityManager->remove($updatedData);
        $entityManager->flush();
    }
}
