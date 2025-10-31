<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatisticsHour;
use WechatOfficialAccountStatsBundle\Repository\ImageTextStatisticsHourRepository;

/**
 * @internal
 */
#[CoversClass(ImageTextStatisticsHourRepository::class)]
#[RunTestsInSeparateProcesses]
final class ImageTextStatisticsHourRepositoryTest extends AbstractRepositoryTestCase
{
    private ImageTextStatisticsHourRepository $repository;

    public function testFindByWithValidCriteriaShouldReturnMatchingEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createImageTextStatisticsHour($account, 10, 100);
        $entity2 = $this->createImageTextStatisticsHour($account, 15, 150, new \DateTimeImmutable('+1 day'));

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
        $entity1 = $this->createImageTextStatisticsHour($account, 20, 200);
        $entity2 = $this->createImageTextStatisticsHour($account, 5, 50, new \DateTimeImmutable('+1 day'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['account' => $account], ['refHour' => 'ASC']);

        $this->assertCount(2, $results);
        $this->assertSame(5, $results[0]->getRefHour());
        $this->assertSame(20, $results[1]->getRefHour());
    }

    public function testFindByWithLimitShouldReturnLimitedResults(): void
    {
        $account = $this->createAccount();
        for ($i = 1; $i <= 5; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createImageTextStatisticsHour($account, $i, $i * 10, $date);
            $this->persistAndFlush($entity);
        }

        $results = $this->repository->findBy(['account' => $account], null, 3);

        $this->assertCount(3, $results);
    }

    public function testFindByWithOffsetShouldReturnOffsetResults(): void
    {
        $account = $this->createAccount();
        for ($i = 1; $i <= 5; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createImageTextStatisticsHour($account, $i, $i * 10, $date);
            $this->persistAndFlush($entity);
        }

        $results = $this->repository->findBy(['account' => $account], null, null, 2);

        $this->assertCount(3, $results);
    }

    public function testFindOneByWithValidCriteriaShouldReturnSingleEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createImageTextStatisticsHour($account, 12, 120);
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(ImageTextStatisticsHour::class, $persistedEntity);

        $result = $this->repository->findOneBy(['id' => $persistedEntity->getId()]);

        $this->assertInstanceOf(ImageTextStatisticsHour::class, $result);
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
        $entity1 = $this->createImageTextStatisticsHour($account, 18, 180);
        $entity2 = $this->createImageTextStatisticsHour($account, 8, 80, new \DateTimeImmutable('+1 day'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $result = $this->repository->findOneBy(
            ['account' => $account],
            ['refHour' => 'ASC']
        );

        $this->assertInstanceOf(ImageTextStatisticsHour::class, $result);
        $this->assertSame(8, $result->getRefHour());
    }

    public function testCountQueryShouldReturnCorrectCount(): void
    {
        $account = $this->createAccount();
        for ($i = 1; $i <= 3; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createImageTextStatisticsHour($account, $i, $i * 10, $date);
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
        $entity = $this->createImageTextStatisticsHour($account, 14, 140);

        $this->repository->save($entity, true);

        $this->assertEntityPersisted($entity);
        $this->assertNotNull($entity->getId());
    }

    public function testSaveWithoutFlushShouldNotFlushImmediately(): void
    {
        $account = $this->createAccount();
        $entity = $this->createImageTextStatisticsHour($account, 16, 160);

        $this->repository->save($entity, false);

        $this->assertTrue(self::getEntityManager()->contains($entity));

        self::getEntityManager()->flush();
        $this->assertEntityPersisted($entity);
    }

    public function testSaveWithCompleteDataShouldPersistAllFields(): void
    {
        $account = $this->createAccount();
        $date = new \DateTimeImmutable('2024-01-15');

        $entity = new ImageTextStatisticsHour();
        $entity->setAccount($account);
        $entity->setDate($date);
        $entity->setRefHour(12);
        $entity->setIntPageReadUser(200);
        $entity->setIntPageReadCount(300);
        $entity->setOriPageReadUser(50);
        $entity->setOriPageReadCount(75);
        $entity->setShareUser(25);
        $entity->setShareCount(30);
        $entity->setAddToFavUser(10);
        $entity->setAddToFavCount(12);

        $this->repository->save($entity, true);

        self::getEntityManager()->clear();
        $saved = $this->repository->find($entity->getId());

        $this->assertInstanceOf(ImageTextStatisticsHour::class, $saved);
        $this->assertEquals($date, $saved->getDate());
        $this->assertSame(12, $saved->getRefHour());
        $this->assertSame(200, $saved->getIntPageReadUser());
        $this->assertSame(300, $saved->getIntPageReadCount());
        $this->assertSame(50, $saved->getOriPageReadUser());
        $this->assertSame(75, $saved->getOriPageReadCount());
        $this->assertSame(25, $saved->getShareUser());
        $this->assertSame(30, $saved->getShareCount());
        $this->assertSame(10, $saved->getAddToFavUser());
        $this->assertSame(12, $saved->getAddToFavCount());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createImageTextStatisticsHour($account, 20, 200);
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(ImageTextStatisticsHour::class, $persistedEntity);
        $id = $persistedEntity->getId();

        $this->repository->remove($persistedEntity, true);

        $this->assertEntityNotExists(ImageTextStatisticsHour::class, $id);
        $this->assertNull($this->repository->find($id));
    }

    public function testFindByAccountAssociationShouldReturnAccountRelatedEntities(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createImageTextStatisticsHour($account1, 10, 100);
        $entity2 = $this->createImageTextStatisticsHour($account2, 15, 150, new \DateTimeImmutable('+1 day'));

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

        $entity1 = $this->createImageTextStatisticsHour($account1, 10, 100);
        $entity2 = $this->createImageTextStatisticsHour($account1, 15, 150, new \DateTimeImmutable('+1 day'));
        $entity3 = $this->createImageTextStatisticsHour($account2, 20, 200, new \DateTimeImmutable('+2 days'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);
        $this->persistAndFlush($entity3);

        $count = $this->repository->count(['account' => $account1]);

        $this->assertSame(2, $count);
    }

    public function testFindByNullFieldShouldReturnMatchingEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createImageTextStatisticsHour($account, 10, 100);
        $entity2 = $this->createImageTextStatisticsHour($account, 15, 150, new \DateTimeImmutable('+1 day'));
        $entity2->setShareUser(50);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['shareUser' => null, 'account' => $account]);

        $this->assertCount(1, $results);
        $this->assertNull($results[0]->getShareUser());
        $this->assertSame(10, $results[0]->getRefHour());
    }

    public function testCountWithNullFieldShouldReturnCorrectNumber(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createImageTextStatisticsHour($account, 10, 100);
        $entity2 = $this->createImageTextStatisticsHour($account, 15, 150, new \DateTimeImmutable('+1 day'));
        $entity2->setOriPageReadCount(75);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $count = $this->repository->count(['oriPageReadCount' => null, 'account' => $account]);

        $this->assertSame(1, $count);
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ImageTextStatisticsHourRepository::class);
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

    private function createImageTextStatisticsHour(Account $account, int $refHour, int $intPageReadUser, ?\DateTimeImmutable $date = null): ImageTextStatisticsHour
    {
        static $counter = 0;
        ++$counter;

        $baseDate = $date ?? new \DateTimeImmutable();
        $uniqueDate = $baseDate->modify("+{$counter} hours");

        $entity = new ImageTextStatisticsHour();
        $entity->setAccount($account);
        $entity->setDate($uniqueDate);
        $entity->setRefHour($refHour);
        $entity->setIntPageReadUser($intPageReadUser);

        return $entity;
    }

    protected function createNewEntity(): object
    {
        $account = $this->createAccount();

        return $this->createImageTextStatisticsHour($account, 10, 100);
    }

    /**
     * @return ServiceEntityRepository<ImageTextStatisticsHour>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
