<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\UserSummary;
use WechatOfficialAccountStatsBundle\Repository\UserSummaryRepository;
use WechatOfficialAccountStatsBundle\Service\UserSummaryDataProcessor;

/**
 * @internal
 */
#[CoversClass(UserSummaryDataProcessor::class)]
final class UserSummaryDataProcessorTest extends TestCase
{
    private UserSummaryRepository&MockObject $repository;

    private LoggerInterface&MockObject $logger;

    private EntityManagerInterface&MockObject $entityManager;

    private UserSummaryDataProcessor $processor;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserSummaryRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->processor = new UserSummaryDataProcessor(
            $this->repository,
            $this->logger,
            $this->entityManager,
        );
    }

    public function testProcessorCanBeInstantiated(): void
    {
        $this->assertInstanceOf(UserSummaryDataProcessor::class, $this->processor);
    }

    public function testProcessResponseWithInvalidData(): void
    {
        $account = new Account();
        $response = ['invalid' => 'data'];

        $this->logger->expects($this->once())
            ->method('error')
        ;

        $this->processor->processResponse($account, $response);
    }

    public function testProcessResponseWithEmptyList(): void
    {
        $account = new Account();
        $response = ['list' => []];

        $this->processor->processResponse($account, $response);

        $this->expectNotToPerformAssertions();
    }

    public function testProcessResponseWithValidData(): void
    {
        $account = new Account();
        $response = [
            'list' => [
                [
                    'ref_date' => '2024-01-01',
                    'user_source' => 1,
                    'new_user' => 50,
                    'cancel_user' => 10,
                ],
            ],
        ];

        $entity = new UserSummary();
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($entity)
        ;

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($entity)
        ;

        $this->processor->processResponse($account, $response);
    }
}
