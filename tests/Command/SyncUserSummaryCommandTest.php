<?php

namespace WechatOfficialAccountStatsBundle\Tests\Command;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Command\SyncUserSummaryCommand;
use WechatOfficialAccountStatsBundle\Entity\UserSummary;
use WechatOfficialAccountStatsBundle\Enum\UserSummarySource;
use WechatOfficialAccountStatsBundle\Repository\UserSummaryRepository;
use WechatOfficialAccountStatsBundle\Request\GetUserSummaryRequest;

class SyncUserSummaryCommandTest extends TestCase
{
    private AccountRepository $accountRepository;
    private OfficialAccountClient $client;
    private UserSummaryRepository $summaryRepository;
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private SyncUserSummaryCommand $command;
    private InputInterface $input;
    private OutputInterface $output;

    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->client = $this->createMock(OfficialAccountClient::class);
        $this->summaryRepository = $this->createMock(UserSummaryRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);

        $this->command = new SyncUserSummaryCommand(
            $this->accountRepository,
            $this->client,
            $this->summaryRepository,
            $this->logger,
            $this->entityManager
        );
    }

    /**
     * 测试命令在成功场景下的执行
     */
    public function testExecute_withSuccessfulResponse_returnsSuccess(): void
    {
        // 固定当前日期，以确保测试的可预测性
        CarbonImmutable::setTestNow(Carbon::create(2023, 1, 10));

        $account = $this->createMock(Account::class);

        // 设置账户仓库模拟
        $this->accountRepository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true])
            ->willReturn([$account]);

        // 设置请求/响应模拟
        $this->client->expects($this->once())
            ->method('request')
            ->willReturnCallback(function (GetUserSummaryRequest $request) use ($account) {
                // 验证请求参数
                $this->assertSame($account, $request->getAccount());
                $this->assertInstanceOf(DateTimeInterface::class, $request->getBeginDate());
                $this->assertInstanceOf(DateTimeInterface::class, $request->getEndDate());

                // 返回模拟响应
                return [
                    'list' => [
                        [
                            'ref_date' => '2023-01-03',
                            'user_source' => 0,
                            'new_user' => 100,
                            'cancel_user' => 20
                        ],
                        [
                            'ref_date' => '2023-01-03',
                            'user_source' => 1,
                            'new_user' => 50,
                            'cancel_user' => 10
                        ]
                    ]
                ];
            });

        // 获取 execute 方法的反射
        $executeMethod = new \ReflectionMethod(SyncUserSummaryCommand::class, 'execute');
        $executeMethod->setAccessible(true);

        // 设置摘要仓库模拟 - 第一个条目不存在，需要创建
        $this->summaryRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturnMap([
                [
                    [
                        'account' => $account,
                        'date' => $this->callback(function ($date) {
                            return $date instanceof DateTimeInterface
                                && $date->format('Y-m-d') === '2023-01-03';
                        }),
                        'source' => UserSummarySource::OTHER
                    ],
                    null
                ],
                [
                    [
                        'account' => $account,
                        'date' => $this->callback(function ($date) {
                            return $date instanceof DateTimeInterface
                                && $date->format('Y-m-d') === '2023-01-03';
                        }),
                        'source' => UserSummarySource::SEARCH
                    ],
                    $this->createMock(UserSummary::class)
                ]
            ]);

        // 实体管理器模拟 - 预期会持久化并刷新两次（每个数据条目一次）
        $this->entityManager->expects($this->exactly(2))
            ->method('persist')
            ->with($this->isInstanceOf(UserSummary::class));

        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        // 执行命令
        $result = $executeMethod->invoke($this->command, $this->input, $this->output);

        // 验证返回值
        $this->assertSame(Command::SUCCESS, $result);

        // 重置测试日期
        CarbonImmutable::setTestNow();
    }

    /**
     * 测试命令在API返回错误时的行为
     */
    public function testExecute_withErrorResponse_logsErrorAndContinues(): void
    {
        // 固定当前日期
        CarbonImmutable::setTestNow(Carbon::create(2023, 1, 10));

        $account = $this->createMock(Account::class);

        // 设置账户仓库模拟
        $this->accountRepository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true])
            ->willReturn([$account]);

        // 设置请求/响应模拟 - 返回错误响应
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn(['errcode' => 40001, 'errmsg' => '无效的凭证']);

        // 日志记录调用
        $this->logger->expects($this->once())
            ->method('error')
            ->with('获取用户增减数据发生错误', $this->anything());

        // 不调用数据库操作
        $this->summaryRepository->expects($this->never())
            ->method('findOneBy');

        $this->entityManager->expects($this->never())
            ->method('persist');

        $this->entityManager->expects($this->never())
            ->method('flush');

        // 获取 execute 方法的反射
        $executeMethod = new \ReflectionMethod(SyncUserSummaryCommand::class, 'execute');
        $executeMethod->setAccessible(true);

        // 执行命令
        $result = $executeMethod->invoke($this->command, $this->input, $this->output);

        // 验证返回值
        $this->assertSame(Command::SUCCESS, $result);

        // 重置测试日期
        CarbonImmutable::setTestNow();
    }

    /**
     * 测试命令处理未知来源值的情况
     */
    public function testExecute_withUnknownSource_logsErrorAndContinues(): void
    {
        // 固定当前日期
        CarbonImmutable::setTestNow(Carbon::create(2023, 1, 10));

        $account = $this->createMock(Account::class);

        // 设置账户仓库模拟
        $this->accountRepository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true])
            ->willReturn([$account]);

        // 设置请求/响应模拟 - 返回包含未知来源的响应
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn([
                'list' => [
                    [
                        'ref_date' => '2023-01-03',
                        'user_source' => 999, // 未知来源
                        'new_user' => 100,
                        'cancel_user' => 20
                    ]
                ]
            ]);

        // 日志记录调用
        $this->logger->expects($this->once())
            ->method('error')
            ->with('发生未知的数据来源', $this->anything());

        // 不调用数据库操作
        $this->summaryRepository->expects($this->never())
            ->method('findOneBy');

        $this->entityManager->expects($this->never())
            ->method('persist');

        $this->entityManager->expects($this->never())
            ->method('flush');

        // 获取 execute 方法的反射
        $executeMethod = new \ReflectionMethod(SyncUserSummaryCommand::class, 'execute');
        $executeMethod->setAccessible(true);

        // 执行命令
        $result = $executeMethod->invoke($this->command, $this->input, $this->output);

        // 验证返回值
        $this->assertSame(Command::SUCCESS, $result);

        // 重置测试日期
        CarbonImmutable::setTestNow();
    }

    /**
     * 测试命令在没有有效账户时的行为
     */
    public function testExecute_withNoValidAccounts_returnsSuccess(): void
    {
        // 设置账户仓库模拟 - 没有有效账户
        $this->accountRepository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true])
            ->willReturn([]);

        // 不调用其他操作
        $this->client->expects($this->never())
            ->method('request');

        $this->logger->expects($this->never())
            ->method('error');

        $this->summaryRepository->expects($this->never())
            ->method('findOneBy');

        $this->entityManager->expects($this->never())
            ->method('persist');

        $this->entityManager->expects($this->never())
            ->method('flush');

        // 获取 execute 方法的反射
        $executeMethod = new \ReflectionMethod(SyncUserSummaryCommand::class, 'execute');
        $executeMethod->setAccessible(true);

        // 执行命令
        $result = $executeMethod->invoke($this->command, $this->input, $this->output);

        // 验证返回值
        $this->assertSame(Command::SUCCESS, $result);
    }
}
