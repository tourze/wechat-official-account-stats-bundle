<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\MessageSendMonthData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSendMonthDataRepository;

/**
 * @internal
 */
#[CoversClass(MessageSendMonthDataRepository::class)]
#[RunTestsInSeparateProcesses]
final class MessageSendMonthDataRepositoryTest extends AbstractRepositoryTestCase
{
    private MessageSendMonthDataRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(MessageSendMonthDataRepository::class);
    }

    public function testRepositoryInstanceShouldReturnCorrectInstance(): void
    {
        $this->assertInstanceOf(MessageSendMonthDataRepository::class, $this->repository);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $account = $this->createAccount();
        $entity = new MessageSendMonthData();
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
        $entity = new MessageSendMonthData();
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

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setName('Test Account ' . uniqid());
        $account->setAppId('test_app_id_' . uniqid());
        $account->setAppSecret('test_secret');
        $account->setToken('test_token');
        $account->setEncodingAesKey('test_encoding_key');
        $account = $this->persistAndFlush($account);
        $this->assertInstanceOf(Account::class, $account);

        $entity = new MessageSendMonthData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());
        $entity->setMsgType(MessageSendDataTypeEnum::TEXT);
        $entity->setMsgUser(100);
        $entity->setMsgCount(200);

        return $entity;
    }

    protected function getRepository(): MessageSendMonthDataRepository
    {
        return $this->repository;
    }

    private function createAccount(): Account
    {
        $account = new Account();
        $account->setName('Test Account ' . uniqid());
        $account->setAppId('test_app_id_' . uniqid());
        $account->setAppSecret('test_secret');
        $account->setToken('test_token');
        $account->setEncodingAesKey('test_encoding_key');

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        return $account;
    }
}
