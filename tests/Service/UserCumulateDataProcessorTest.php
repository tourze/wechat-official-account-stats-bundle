<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\UserCumulate;
use WechatOfficialAccountStatsBundle\Repository\UserCumulateRepository;
use WechatOfficialAccountStatsBundle\Service\UserCumulateDataProcessor;

/**
 * @internal
 */
#[CoversClass(UserCumulateDataProcessor::class)]
final class UserCumulateDataProcessorTest extends TestCase
{
    private UserCumulateRepository&MockObject $repository;

    private LoggerInterface&MockObject $logger;

    private EntityManagerInterface&MockObject $entityManager;

    private UserCumulateDataProcessor $processor;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserCumulateRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->processor = new UserCumulateDataProcessor(
            $this->repository,
            $this->logger,
            $this->entityManager,
        );
    }

    public function testProcessorCanBeInstantiated(): void
    {
        $this->assertInstanceOf(UserCumulateDataProcessor::class, $this->processor);
    }

    public function testProcessResponseWithInvalidData(): void
    {
        $account = new Account();
        $response = ['invalid' => 'data'];

        $this->logger->expects($this->once())
            ->method('error')
            ->with('获取累计用户数据发生错误')
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
                    'cumulate_user' => 1000,
                ],
            ],
        ];

        $entity = new UserCumulate();
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
