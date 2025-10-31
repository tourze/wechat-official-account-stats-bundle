<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\MessageSendMonthData;
use WechatOfficialAccountStatsBundle\Repository\MessageSendMonthDataRepository;
use WechatOfficialAccountStatsBundle\Service\MessageSendMonthDataProcessor;

/**
 * @internal
 */
#[CoversClass(MessageSendMonthDataProcessor::class)]
final class MessageSendMonthDataProcessorTest extends TestCase
{
    private MessageSendMonthDataRepository&MockObject $repository;

    private LoggerInterface&MockObject $logger;

    private EntityManagerInterface&MockObject $entityManager;

    private MessageSendMonthDataProcessor $processor;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(MessageSendMonthDataRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->processor = new MessageSendMonthDataProcessor(
            $this->repository,
            $this->logger,
            $this->entityManager,
        );
    }

    public function testProcessorCanBeInstantiated(): void
    {
        $this->assertInstanceOf(MessageSendMonthDataProcessor::class, $this->processor);
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
