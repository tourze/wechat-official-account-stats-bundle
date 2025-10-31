<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatisticsHour;
use WechatOfficialAccountStatsBundle\Repository\ImageTextStatisticsHourRepository;
use WechatOfficialAccountStatsBundle\Service\ImageTextStatisticsHourProcessor;

/**
 * @internal
 */
#[CoversClass(ImageTextStatisticsHourProcessor::class)]
final class ImageTextStatisticsHourProcessorTest extends TestCase
{
    private ImageTextStatisticsHourRepository&MockObject $repository;

    private LoggerInterface&MockObject $logger;

    private EntityManagerInterface&MockObject $entityManager;

    private ImageTextStatisticsHourProcessor $processor;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ImageTextStatisticsHourRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->processor = new ImageTextStatisticsHourProcessor(
            $this->repository,
            $this->logger,
            $this->entityManager,
        );
    }

    public function testProcessorCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ImageTextStatisticsHourProcessor::class, $this->processor);
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
