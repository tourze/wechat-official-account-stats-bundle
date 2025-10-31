<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareDataHour;
use WechatOfficialAccountStatsBundle\Repository\ImageTextShareDataHourRepository;

/**
 * @internal
 */
#[CoversClass(ImageTextShareDataHourRepository::class)]
#[RunTestsInSeparateProcesses]
final class ImageTextShareDataHourRepositoryTest extends AbstractRepositoryTestCase
{
    private ImageTextShareDataHourRepository $repository;

    public function testFindByWithValidCriteriaShouldReturnMatchingEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createImageTextShareDataHour($account, 10, 1);
        $entity2 = $this->createImageTextShareDataHour($account, 15, 2, new \DateTimeImmutable('+1 day'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['refHour' => 10, 'account' => $account]);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertSame(10, $results[0]->getRefHour());
    }

    public function testFindByWithInvalidFieldShouldThrowException(): void
    {
        $this->expectException(\Exception::class);

        $this->repository->findBy(['nonexistent_field' => 'value']);
    }

    public function testFindByWithOrderByShouldReturnOrderedEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createImageTextShareDataHour($account, 23, 1);
        $entity2 = $this->createImageTextShareDataHour($account, 5, 2, new \DateTimeImmutable('+1 day'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['account' => $account], ['refHour' => 'ASC']);

        $this->assertCount(2, $results);
        $this->assertSame(5, $results[0]->getRefHour());
        $this->assertSame(23, $results[1]->getRefHour());
    }

    public function testFindByWithLimitShouldReturnLimitedResults(): void
    {
        $account = $this->createAccount();
        for ($i = 0; $i < 5; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createImageTextShareDataHour($account, $i, 1, $date);
            $this->persistAndFlush($entity);
        }

        $results = $this->repository->findBy(['account' => $account], null, 3);

        $this->assertCount(3, $results);
    }

    public function testFindByWithOffsetShouldReturnOffsetResults(): void
    {
        $account = $this->createAccount();
        $createdEntities = [];
        for ($i = 0; $i < 5; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createImageTextShareDataHour($account, $i, 1, $date);
            $persistedEntity = $this->persistAndFlush($entity);
            $this->assertInstanceOf(ImageTextShareDataHour::class, $persistedEntity);
            $createdEntities[] = $persistedEntity;
        }

        $results = $this->repository->findBy(['account' => $account], null, null, 2);

        $this->assertCount(3, $results);
        $resultIds = array_map(fn ($entity) => $entity->getId(), $results);
        $expectedIds = array_map(fn ($entity) => $entity->getId(), array_slice($createdEntities, 2));
        $this->assertEquals($expectedIds, $resultIds);
    }

    public function testFindOneByWithValidCriteriaShouldReturnSingleEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createImageTextShareDataHour($account, 12, 1);
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(ImageTextShareDataHour::class, $persistedEntity);

        $result = $this->repository->findOneBy(['id' => $persistedEntity->getId()]);

        $this->assertInstanceOf(ImageTextShareDataHour::class, $result);
        $this->assertSame($entity->getId(), $result->getId());
    }

    public function testFindOneByWithNonExistentCriteriaShouldReturnNull(): void
    {
        $result = $this->repository->findOneBy(['id' => 999999]);

        $this->assertNull($result);
    }

    public function testFindOneByWithOrderByShouldReturnFirstOrderedEntity(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createImageTextShareDataHour($account, 20, 1);
        $entity2 = $this->createImageTextShareDataHour($account, 5, 2, new \DateTimeImmutable('+1 day'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $result = $this->repository->findOneBy(
            ['account' => $account],
            ['refHour' => 'ASC']
        );

        $this->assertInstanceOf(ImageTextShareDataHour::class, $result);
        $this->assertSame(5, $result->getRefHour());
    }

    public function testCountQueryShouldReturnCorrectCount(): void
    {
        $account = $this->createAccount();
        for ($i = 0; $i < 3; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createImageTextShareDataHour($account, $i, 1, $date);
            $this->persistAndFlush($entity);
        }

        $qb = $this->repository->createQueryBuilder('itsh');
        $count = $qb->select('COUNT(itsh.id)')
            ->where('itsh.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertSame(3, (int) $count);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createImageTextShareDataHour($account, 12, 1);

        $this->repository->save($entity, true);

        $this->assertEntityPersisted($entity);
        $this->assertNotNull($entity->getId());
    }

    public function testSaveWithoutFlushShouldNotFlushImmediately(): void
    {
        $account = $this->createAccount();
        $entity = $this->createImageTextShareDataHour($account, 15, 2);

        $this->repository->save($entity, false);

        $this->assertTrue(self::getEntityManager()->contains($entity));

        self::getEntityManager()->flush();
        $this->assertEntityPersisted($entity);
    }

    public function testSaveWithCompleteDataShouldPersistAllFields(): void
    {
        $account = $this->createAccount();
        $date = new \DateTimeImmutable('2024-01-15');

        $entity = new ImageTextShareDataHour();
        $entity->setAccount($account);
        $entity->setDate($date);
        $entity->setRefHour(14);
        $entity->setShareScene(2);
        $entity->setShareCount(150);
        $entity->setShareUser(75);

        $this->repository->save($entity, true);

        self::getEntityManager()->clear();
        $saved = $this->repository->find($entity->getId());

        $this->assertInstanceOf(ImageTextShareDataHour::class, $saved);
        $this->assertEquals($date, $saved->getDate());
        $this->assertSame(14, $saved->getRefHour());
        $this->assertSame(2, $saved->getShareScene());
        $this->assertSame(150, $saved->getShareCount());
        $this->assertSame(75, $saved->getShareUser());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createImageTextShareDataHour($account, 18, 3);
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(ImageTextShareDataHour::class, $persistedEntity);
        $id = $persistedEntity->getId();

        $this->repository->remove($persistedEntity, true);

        $this->assertEntityNotExists(ImageTextShareDataHour::class, $id);
        $this->assertNull($this->repository->find($id));
    }

    public function testFindByAccountAssociationShouldReturnAccountRelatedEntities(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createImageTextShareDataHour($account1, 10, 1);
        $entity2 = $this->createImageTextShareDataHour($account2, 15, 2, new \DateTimeImmutable('+1 day'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['account' => $account1]);

        $this->assertCount(1, $results);
        $this->assertSame($account1, $results[0]->getAccount());
        $this->assertSame(10, $results[0]->getRefHour());
    }

    public function testCountByAccountAssociationShouldReturnCorrectNumber(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createImageTextShareDataHour($account1, 10, 1);
        $entity2 = $this->createImageTextShareDataHour($account1, 15, 2, new \DateTimeImmutable('+1 day'));
        $entity3 = $this->createImageTextShareDataHour($account2, 20, 3, new \DateTimeImmutable('+2 days'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);
        $this->persistAndFlush($entity3);

        $count = $this->repository->count(['account' => $account1]);

        $this->assertSame(2, $count);
    }

    public function testFindByNullFieldShouldReturnMatchingEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createImageTextShareDataHour($account, 10, 1);
        $entity2 = $this->createImageTextShareDataHour($account, 15, 2, new \DateTimeImmutable('+1 day'));
        $entity2->setShareCount(100);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['shareCount' => null, 'account' => $account]);

        $this->assertCount(1, $results);
        $this->assertNull($results[0]->getShareCount());
        $this->assertSame(10, $results[0]->getRefHour());
    }

    public function testCountWithNullFieldShouldReturnCorrectNumber(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createImageTextShareDataHour($account, 10, 1);
        $entity2 = $this->createImageTextShareDataHour($account, 15, 2, new \DateTimeImmutable('+1 day'));
        $entity2->setShareUser(50);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $count = $this->repository->count(['shareUser' => null, 'account' => $account]);

        $this->assertSame(1, $count);
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ImageTextShareDataHourRepository::class);
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

    private function createImageTextShareDataHour(Account $account, int $refHour, int $shareScene, ?\DateTimeImmutable $date = null): ImageTextShareDataHour
    {
        static $counter = 0;
        ++$counter;

        $baseDate = $date ?? new \DateTimeImmutable();
        $uniqueDate = $baseDate->modify("+{$counter} hours");

        $entity = new ImageTextShareDataHour();
        $entity->setAccount($account);
        $entity->setDate($uniqueDate);
        $entity->setRefHour($refHour);
        $entity->setShareScene($shareScene);

        return $entity;
    }

    protected function createNewEntity(): object
    {
        $account = $this->createAccount();

        return $this->createImageTextShareDataHour($account, 10, 1);
    }

    /**
     * @return ServiceEntityRepository<ImageTextShareDataHour>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
