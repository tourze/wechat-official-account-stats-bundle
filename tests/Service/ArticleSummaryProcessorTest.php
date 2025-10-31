<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ArticleDailySummary;
use WechatOfficialAccountStatsBundle\Repository\ArticleDailySummaryRepository;
use WechatOfficialAccountStatsBundle\Service\ArticleSummaryProcessor;

/**
 * @internal
 */
#[CoversClass(ArticleSummaryProcessor::class)]
final class ArticleSummaryProcessorTest extends TestCase
{
    private ArticleDailySummaryRepository&MockObject $repository;

    private LoggerInterface&MockObject $logger;

    private EntityManagerInterface&MockObject $entityManager;

    private ArticleSummaryProcessor $processor;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ArticleDailySummaryRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->processor = new ArticleSummaryProcessor(
            $this->repository,
            $this->logger,
            $this->entityManager,
        );
    }

    public function testProcessorCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ArticleSummaryProcessor::class, $this->processor);
    }

    public function testProcessResponseWithInvalidData(): void
    {
        $account = new Account();
        $response = ['invalid' => 'data'];

        $this->logger->expects($this->once())
            ->method('error')
            ->with('获取累计用户数据发生错误')
        ;

        $this->entityManager->expects($this->never())
            ->method('flush')
        ;

        $this->processor->processResponse($response, $account);
    }

    public function testProcessResponseWithEmptyList(): void
    {
        $account = new Account();
        $response = ['list' => []];

        $this->entityManager->expects($this->once())
            ->method('flush')
        ;

        $this->processor->processResponse($response, $account);
    }

    public function testProcessResponseWithValidData(): void
    {
        $account = new Account();
        $response = [
            'list' => [
                [
                    'ref_date' => '2024-01-01',
                    'msgId' => 'MSG123',
                    'title' => 'Test Article',
                    'int_page_read_user' => 100,
                    'int_page_read_count' => 150,
                    'ori_page_read_user' => 80,
                    'ori_page_read_count' => 120,
                    'share_user' => 20,
                    'share_count' => 25,
                    'add_to_fav_user' => 10,
                    'add_to_fav_count' => 12,
                ],
            ],
        ];

        $entity = new ArticleDailySummary();
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($entity)
        ;

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($entity)
        ;

        $this->entityManager->expects($this->once())
            ->method('flush')
        ;

        $this->processor->processResponse($response, $account);
    }
}
