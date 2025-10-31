<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\MessageSendData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSendDataRepository;

/**
 * @internal
 */
#[CoversClass(MessageSendDataRepository::class)]
#[RunTestsInSeparateProcesses]
final class MessageSendDataRepositoryTest extends AbstractRepositoryTestCase
{
    private MessageSendDataRepository $repository;

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(MessageSendDataRepository::class, $this->repository);
    }

    public function testSave(): void
    {
        $account = $this->createAccount();
        $entity = new MessageSendData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());
        $entity->setMsgType(MessageSendDataTypeEnum::TEXT);

        $this->repository->save($entity, true);

        $this->assertNotNull($entity->getId());
        $found = $this->repository->find($entity->getId());
        $this->assertInstanceOf(MessageSendData::class, $found);
        $this->assertEquals($entity->getDate(), $found->getDate());
        $this->assertEquals($entity->getMsgType(), $found->getMsgType());
    }

    public function testSaveWithoutFlush(): void
    {
        $account = $this->createAccount();
        $entity = new MessageSendData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());
        $entity->setMsgType(MessageSendDataTypeEnum::IMAGE);

        $this->repository->save($entity, false);
        self::getEntityManager()->flush();

        $this->assertNotNull($entity->getId());
        $found = $this->repository->find($entity->getId());
        $this->assertInstanceOf(MessageSendData::class, $found);
    }

    public function testRemove(): void
    {
        $account = $this->createAccount();
        $entity = new MessageSendData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());
        $entity->setMsgType(MessageSendDataTypeEnum::AUDIO);
        $this->repository->save($entity, true);
        $id = $entity->getId();

        $this->repository->remove($entity, true);

        $this->assertNull($this->repository->find($id));
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(MessageSendDataRepository::class);
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

    protected function createNewEntity(): object
    {
        $account = $this->createAccount();
        $entity = new MessageSendData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());
        $entity->setMsgType(MessageSendDataTypeEnum::TEXT);

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<MessageSendData>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
