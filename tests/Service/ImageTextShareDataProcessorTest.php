<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareData;
use WechatOfficialAccountStatsBundle\Repository\ImageTextShareDataRepository;
use WechatOfficialAccountStatsBundle\Service\ImageTextShareDataProcessor;

/**
 * @internal
 */
#[CoversClass(ImageTextShareDataProcessor::class)]
final class ImageTextShareDataProcessorTest extends TestCase
{
    private ImageTextShareDataRepository&MockObject $repository;

    private LoggerInterface&MockObject $logger;

    private EntityManagerInterface&MockObject $entityManager;

    private ImageTextShareDataProcessor $processor;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ImageTextShareDataRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->processor = new ImageTextShareDataProcessor(
            $this->repository,
            $this->logger,
            $this->entityManager,
        );
    }

    public function testProcessorCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ImageTextShareDataProcessor::class, $this->processor);
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
}
