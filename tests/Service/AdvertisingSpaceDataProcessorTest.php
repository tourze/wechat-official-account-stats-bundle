<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\AdvertisingSpaceData;
use WechatOfficialAccountStatsBundle\Repository\AdvertisingSpaceDataRepository;
use WechatOfficialAccountStatsBundle\Service\AdvertisingSpaceDataProcessor;

/**
 * @internal
 */
#[CoversClass(AdvertisingSpaceDataProcessor::class)]
final class AdvertisingSpaceDataProcessorTest extends TestCase
{
    private AdvertisingSpaceDataRepository&MockObject $repository;

    private LoggerInterface&MockObject $logger;

    private EntityManagerInterface&MockObject $entityManager;

    private AdvertisingSpaceDataProcessor $processor;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(AdvertisingSpaceDataRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->processor = new AdvertisingSpaceDataProcessor(
            $this->repository,
            $this->logger,
            $this->entityManager,
        );
    }

    public function testProcessorCanBeInstantiated(): void
    {
        $this->assertInstanceOf(AdvertisingSpaceDataProcessor::class, $this->processor);
    }

    public function testProcessResponseWithInvalidData(): void
    {
        $account = new Account();
        $response = ['invalid' => 'data'];

        $this->logger->expects($this->once())
            ->method('error')
            ->with('获取公众号分广告位数据错误')
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
                    'date' => '2024-01-01',
                    'slot_id' => 123,
                    'ad_slot' => 'test_slot',
                    'req_succ_count' => 100,
                    'exposure_rate_count' => 80,
                    'click_count' => 10,
                    'click_rate' => '12.5',
                    'income' => 500,
                    'ecpm' => '6.25',
                ],
            ],
        ];

        $entity = new AdvertisingSpaceData();
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
