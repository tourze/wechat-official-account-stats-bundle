<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\RebateGoodsData;
use WechatOfficialAccountStatsBundle\Repository\RebateGoodsDataRepository;

/**
 * @internal
 */
#[CoversClass(RebateGoodsDataRepository::class)]
#[RunTestsInSeparateProcesses]
final class RebateGoodsDataRepositoryTest extends AbstractRepositoryTestCase
{
    private RebateGoodsDataRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(RebateGoodsDataRepository::class);
    }

    public function testRepositoryInstanceShouldReturnCorrectInstance(): void
    {
        $this->assertInstanceOf(RebateGoodsDataRepository::class, $this->repository);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $account = $this->createAccount();
        $entity = new RebateGoodsData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());

        $this->repository->save($entity);

        $this->assertGreaterThan(0, $entity->getId());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $account = $this->createAccount();
        $entity = new RebateGoodsData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());

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
        $entity = new RebateGoodsData();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());

        return $entity;
    }

    protected function getRepository(): RebateGoodsDataRepository
    {
        return $this->repository;
    }
}
