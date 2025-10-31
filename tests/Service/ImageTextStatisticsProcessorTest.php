<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatistics;
use WechatOfficialAccountStatsBundle\Repository\ImageTextStatisticsRepository;
use WechatOfficialAccountStatsBundle\Service\ImageTextStatisticsProcessor;

/**
 * @internal
 */
#[CoversClass(ImageTextStatisticsProcessor::class)]
final class ImageTextStatisticsProcessorTest extends TestCase
{
    private ImageTextStatisticsRepository&MockObject $repository;

    private LoggerInterface&MockObject $logger;

    private EntityManagerInterface&MockObject $entityManager;

    private ImageTextStatisticsProcessor $processor;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ImageTextStatisticsRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->processor = new ImageTextStatisticsProcessor(
            $this->repository,
            $this->logger,
            $this->entityManager,
        );
    }

    public function testProcessorCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ImageTextStatisticsProcessor::class, $this->processor);
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
