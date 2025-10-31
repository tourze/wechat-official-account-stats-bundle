<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\MessageSendWeekData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSendWeekDataRepository;

/**
 * @internal
 */
#[CoversClass(MessageSendWeekDataRepository::class)]
#[RunTestsInSeparateProcesses]
final class MessageSendWeekDataRepositoryTest extends AbstractRepositoryTestCase
{
    private MessageSendWeekDataRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(MessageSendWeekDataRepository::class);
    }

    public function testRepositoryInstanceShouldReturnCorrectInstance(): void
    {
        $this->assertInstanceOf(MessageSendWeekDataRepository::class, $this->repository);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $account = $this->createAccount();
        $entity = new MessageSendWeekData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());
        $entity->setMsgType(MessageSendDataTypeEnum::TEXT);
        $entity->setMsgUser(100);
        $entity->setMsgCount(200);

        $this->repository->save($entity);

        $this->assertGreaterThan(0, $entity->getId());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $account = $this->createAccount();
        $entity = new MessageSendWeekData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());
        $entity->setMsgType(MessageSendDataTypeEnum::TEXT);
        $entity->setMsgUser(100);
        $entity->setMsgCount(200);

        $this->repository->save($entity);
        $id = $entity->getId();

        $this->repository->remove($entity);

        $deletedEntity = $this->repository->find($id);
        $this->assertNull($deletedEntity);
    }

    private function createAccount(): Account
    {
        $account = new Account();
        $account->setName('Test Account ' . uniqid());
        $account->setAppId('test_app_id_' . uniqid());
        $account->setAppSecret('test_secret');
        $account->setToken('test_token');
        $account->setEncodingAesKey('test_encoding_key');

        $persistedAccount = $this->persistAndFlush($account);
        $this->assertInstanceOf(Account::class, $persistedAccount);

        return $persistedAccount;
    }

    protected function createNewEntity(): object
    {
        $account = $this->createAccount();
        $entity = new MessageSendWeekData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());
        $entity->setMsgType(MessageSendDataTypeEnum::TEXT);
        $entity->setMsgUser(100);
        $entity->setMsgCount(200);

        return $entity;
    }

    protected function getRepository(): MessageSendWeekDataRepository
    {
        return $this->repository;
    }
}
