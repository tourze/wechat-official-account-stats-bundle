<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\AdvertisingSpaceData;
use WechatOfficialAccountStatsBundle\Repository\AdvertisingSpaceDataRepository;

/**
 * @internal
 */
#[CoversClass(AdvertisingSpaceDataRepository::class)]
#[RunTestsInSeparateProcesses]
final class AdvertisingSpaceDataRepositoryTest extends AbstractRepositoryTestCase
{
    private AdvertisingSpaceDataRepository $repository;

    public function testFindByWithInvalidFieldShouldThrowException(): void
    {
        $this->expectException(\Exception::class);

        $this->repository->findBy(['nonexistent_field' => 'value']);
    }

    public function testFindOneByWithNonExistentCriteriaShouldReturnNull(): void
    {
        $result = $this->repository->findOneBy(['id' => 999999]);

        $this->assertNull($result);
    }

    public function testFindOneByShouldRespectOrderByClause(): void
    {
        $account = $this->createAccount();
        $date1 = new \DateTimeImmutable();
        $date2 = new \DateTimeImmutable('+1 day');
        $entity1 = $this->createAdvertisingSpaceData($account, 300, 'Test Ad Slot 1', $date1);
        $entity2 = $this->createAdvertisingSpaceData($account, 100, 'Test Ad Slot 2', $date2);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $result = $this->repository->findOneBy(
            ['account' => $account],
            ['slotId' => 'DESC']
        );

        $this->assertInstanceOf(AdvertisingSpaceData::class, $result);
        $this->assertSame(300, $result->getSlotId());
    }

    public function testFindByAccountShouldReturnAccountRelatedEntities(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createAdvertisingSpaceData($account1, 123, 'Test Ad Slot 1');
        $entity2 = $this->createAdvertisingSpaceData($account2, 456, 'Test Ad Slot 2');

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['account' => $account1]);

        $this->assertCount(1, $results);
        $this->assertSame($account1, $results[0]->getAccount());
        $this->assertSame(123, $results[0]->getSlotId());
    }

    public function testFindByNullableFieldShouldReturnMatchingEntities(): void
    {
        $account = $this->createAccount();
        $date1 = new \DateTimeImmutable();
        $date2 = new \DateTimeImmutable('+1 day');
        $entity1 = $this->createAdvertisingSpaceData($account, 123, 'Test Ad Slot 1', $date1);
        $entity2 = $this->createAdvertisingSpaceData($account, 456, 'Test Ad Slot 2', $date2);
        $entity2->setReqSuccCount(100);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['reqSuccCount' => null, 'account' => $account]);

        $this->assertCount(1, $results);
        $this->assertNull($results[0]->getReqSuccCount());
        $this->assertSame(123, $results[0]->getSlotId());
    }

    public function testCountQueryShouldReturnCorrectCount(): void
    {
        $account = $this->createAccount();
        for ($i = 0; $i < 3; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createAdvertisingSpaceData($account, 100 + $i, "Test Ad Slot {$i}", $date);
            $this->persistAndFlush($entity);
        }

        $qb = $this->repository->createQueryBuilder('asd');
        $count = $qb->select('COUNT(asd.id)')
            ->where('asd.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertSame(3, (int) $count);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createAdvertisingSpaceData($account, 123, 'Test Ad Slot');

        $this->repository->save($entity, true);

        $this->assertEntityPersisted($entity);
        $this->assertNotNull($entity->getId());
    }

    public function testSaveWithoutFlushShouldNotFlushImmediately(): void
    {
        $account = $this->createAccount();
        $entity = $this->createAdvertisingSpaceData($account, 456, 'Test Ad Slot No Flush');

        $this->repository->save($entity, false);

        $this->assertTrue(self::getEntityManager()->contains($entity));

        self::getEntityManager()->flush();
        $this->assertEntityPersisted($entity);
    }

    public function testSaveWithCompleteDataShouldPersistAllFields(): void
    {
        $account = $this->createAccount();
        $date = new \DateTimeImmutable('2024-01-15');

        $entity = new AdvertisingSpaceData();
        $entity->setAccount($account);
        $entity->setDate($date);
        $entity->setSlotId(789);
        $entity->setAdSlot('Complete Test Ad Slot');
        $entity->setReqSuccCount(1000);
        $entity->setExposureCount(800);
        $entity->setExposureRate('80%');
        $entity->setClickCount(50);
        $entity->setClickRate('6.25%');
        $entity->setIncome(2500);
        $entity->setEcpm('31.25');

        $this->repository->save($entity, true);

        self::getEntityManager()->clear();
        $saved = $this->repository->find($entity->getId());

        $this->assertInstanceOf(AdvertisingSpaceData::class, $saved);
        $this->assertEquals($date, $saved->getDate());
        $this->assertSame(789, $saved->getSlotId());
        $this->assertSame('Complete Test Ad Slot', $saved->getAdSlot());
        $this->assertSame(1000, $saved->getReqSuccCount());
        $this->assertSame(800, $saved->getExposureCount());
        $this->assertSame('80%', $saved->getExposureRate());
        $this->assertSame(50, $saved->getClickCount());
        $this->assertSame('6.25%', $saved->getClickRate());
        $this->assertSame(2500, $saved->getIncome());
        $this->assertSame('31.25', $saved->getEcpm());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createAdvertisingSpaceData($account, 999, 'Test Ad Slot for Remove');
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(AdvertisingSpaceData::class, $persistedEntity);
        $id = $persistedEntity->getId();

        $this->repository->remove($persistedEntity, true);

        $this->assertEntityNotExists(AdvertisingSpaceData::class, $id);
        $this->assertNull($this->repository->find($id));
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(AdvertisingSpaceDataRepository::class);
        self::getEntityManager()->clear();
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

    public function testFindByWithEmptyCriteriaShouldReturnAllEntities(): void
    {
        $account = $this->createAccount();
        $entity = $this->createAdvertisingSpaceData($account, 123, 'Test Ad Slot');
        $this->persistAndFlush($entity);

        $results = $this->repository->findBy(['account' => $account]);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertContainsOnlyInstancesOf(AdvertisingSpaceData::class, $results);
    }

    public function testFindByNullFieldShouldReturnMatchingEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createAdvertisingSpaceData($account, 123, 'Test Ad Slot 1');
        $entity2 = $this->createAdvertisingSpaceData($account, 456, 'Test Ad Slot 2', new \DateTimeImmutable('+1 day'));
        $entity2->setExposureCount(100);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['exposureCount' => null, 'account' => $account]);

        $this->assertCount(1, $results);
        $this->assertNull($results[0]->getExposureCount());
        $this->assertSame(123, $results[0]->getSlotId());
    }

    public function testCountWithNullFieldShouldReturnCorrectNumber(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createAdvertisingSpaceData($account, 123, 'Test Ad Slot 1');
        $entity2 = $this->createAdvertisingSpaceData($account, 456, 'Test Ad Slot 2', new \DateTimeImmutable('+1 day'));
        $entity2->setClickCount(50);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $count = $this->repository->count(['clickCount' => null, 'account' => $account]);

        $this->assertSame(1, $count);
    }

    public function testFindOneByWithAssociationCriteriaShouldReturnEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createAdvertisingSpaceData($account, 123, 'Test Ad Slot');
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(AdvertisingSpaceData::class, $persistedEntity);

        $result = $this->repository->findOneBy(['account' => $account]);

        $this->assertInstanceOf(AdvertisingSpaceData::class, $result);
        $this->assertSame($account->getId(), $result->getAccount()->getId());
    }

    public function testFindOneByWithNullValueShouldReturnEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createAdvertisingSpaceData($account, 123, 'Test Ad Slot');
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(AdvertisingSpaceData::class, $persistedEntity);

        $result = $this->repository->findOneBy(['income' => null, 'account' => $account]);

        $this->assertInstanceOf(AdvertisingSpaceData::class, $result);
        $this->assertNull($result->getIncome());
    }

    public function testCountByAccountRelationShouldReturnCorrectNumber(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createAdvertisingSpaceData($account1, 123, 'Test Ad Slot 1');
        $entity2 = $this->createAdvertisingSpaceData($account1, 456, 'Test Ad Slot 2', new \DateTimeImmutable('+1 day'));
        $entity3 = $this->createAdvertisingSpaceData($account2, 789, 'Test Ad Slot 3');

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);
        $this->persistAndFlush($entity3);

        $count = $this->repository->count(['account' => $account1]);

        $this->assertSame(2, $count);
    }

    private function createAdvertisingSpaceData(Account $account, int $slotId, string $adSlot, ?\DateTimeImmutable $date = null): AdvertisingSpaceData
    {
        $entity = new AdvertisingSpaceData();
        $entity->setAccount($account);
        $entity->setDate($date ?? new \DateTimeImmutable());
        $entity->setSlotId($slotId);
        $entity->setAdSlot($adSlot);

        return $entity;
    }

    protected function createNewEntity(): object
    {
        $account = $this->createAccount();

        return $this->createAdvertisingSpaceData($account, 123, 'Test Ad Slot ' . uniqid());
    }

    /**
     * @return ServiceEntityRepository<AdvertisingSpaceData>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
