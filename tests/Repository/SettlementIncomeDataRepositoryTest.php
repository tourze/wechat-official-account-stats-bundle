<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\SettlementIncomeData;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderStatusEnum;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\SettlementIncomeDataRepository;

/**
 * @internal
 */
#[CoversClass(SettlementIncomeDataRepository::class)]
#[RunTestsInSeparateProcesses]
final class SettlementIncomeDataRepositoryTest extends AbstractRepositoryTestCase
{
    private SettlementIncomeDataRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(SettlementIncomeDataRepository::class);
    }

    public function testRepositoryInstanceShouldReturnCorrectInstance(): void
    {
        $this->assertInstanceOf(SettlementIncomeDataRepository::class, $this->repository);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $account = $this->createAccount();
        $entity = new SettlementIncomeData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());
        $entity->setSlotId('test-slot-123');
        $entity->setOrder(SettlementIncomeOrderTypeEnum::FIRST_HALF_OF_MONTH);
        $entity->setSettStatus(SettlementIncomeOrderStatusEnum::SETTLING);

        $this->repository->save($entity);

        $this->assertGreaterThan(0, $entity->getId());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $account = $this->createAccount();
        $entity = new SettlementIncomeData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());
        $entity->setSlotId('test-slot-123');
        $entity->setOrder(SettlementIncomeOrderTypeEnum::FIRST_HALF_OF_MONTH);
        $entity->setSettStatus(SettlementIncomeOrderStatusEnum::SETTLING);

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
        $entity = new SettlementIncomeData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());
        $entity->setSlotId('test-slot-123');
        $entity->setOrder(SettlementIncomeOrderTypeEnum::FIRST_HALF_OF_MONTH);
        $entity->setSettStatus(SettlementIncomeOrderStatusEnum::SETTLING);

        return $entity;
    }

    protected function getRepository(): SettlementIncomeDataRepository
    {
        return $this->repository;
    }
}
