<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummaryHour;
use WechatOfficialAccountStatsBundle\Repository\InterfaceSummaryHourRepository;
use WechatOfficialAccountStatsBundle\Service\InterfaceSummaryHourProcessor;

/**
 * @internal
 */
#[CoversClass(InterfaceSummaryHourProcessor::class)]
final class InterfaceSummaryHourProcessorTest extends TestCase
{
    private InterfaceSummaryHourRepository&MockObject $repository;

    private LoggerInterface&MockObject $logger;

    private EntityManagerInterface&MockObject $entityManager;

    private InterfaceSummaryHourProcessor $processor;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(InterfaceSummaryHourRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->processor = new InterfaceSummaryHourProcessor(
            $this->repository,
            $this->logger,
            $this->entityManager,
        );
    }

    public function testProcessorCanBeInstantiated(): void
    {
        $this->assertInstanceOf(InterfaceSummaryHourProcessor::class, $this->processor);
    }

    public function testProcessResponseWithInvalidData(): void
    {
        $account = new Account();
        $response = ['invalid' => 'data'];

        $this->logger->expects($this->once())
            ->method('error')
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
}
