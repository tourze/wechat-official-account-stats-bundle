<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\MessageSendHourData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSendHourDataRepository;

/**
 * @internal
 */
#[CoversClass(MessageSendHourDataRepository::class)]
#[RunTestsInSeparateProcesses]
final class MessageSendHourDataRepositoryTest extends AbstractRepositoryTestCase
{
    private MessageSendHourDataRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(MessageSendHourDataRepository::class);
    }

    public function testConstruct(): void
    {
        $this->assertNotNull($this->repository);
    }

    public function testSaveMethodShouldPersistEntity(): void
    {
        $account = $this->createTestAccount();
        $entity = $this->createMessageSendHourData($account);

        $this->repository->save($entity, true);

        $found = $this->repository->find($entity->getId());
        $this->assertInstanceOf(MessageSendHourData::class, $found);
        $this->assertEquals($entity->getDate(), $found->getDate());
    }

    public function testRemoveMethodShouldDeleteEntity(): void
    {
        $account = $this->createTestAccount();
        $entity = $this->createMessageSendHourData($account);

        $this->persistEntities([$account, $entity]);

        $id = $entity->getId();

        $this->repository->remove($entity, true);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    private function createTestAccount(): Account
    {
        $account = new Account();
        $account->setAppId('test-app-id-' . uniqid());
        $account->setAppSecret('test-app-secret');
        $account->setName('Test Account');

        return $account;
    }

    private function createMessageSendHourData(Account $account, ?\DateTimeImmutable $date = null): MessageSendHourData
    {
        $entity = new MessageSendHourData();
        $entity->setAccount($account);
        $entity->setDate($date ?? new \DateTimeImmutable());
        $entity->setRefHour(10);
        $entity->setMsgType(MessageSendDataTypeEnum::TEXT);
        $entity->setMsgUser(100);
        $entity->setMsgCount(50);

        return $entity;
    }

    protected function createNewEntity(): object
    {
        $account = $this->createTestAccount();
        $entity = new MessageSendHourData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());
        $entity->setRefHour(10);
        $entity->setMsgType(MessageSendDataTypeEnum::TEXT);

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<MessageSendHourData>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
