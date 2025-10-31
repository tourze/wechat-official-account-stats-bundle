<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareData;
use WechatOfficialAccountStatsBundle\Repository\ImageTextShareDataRepository;

/**
 * @internal
 */
#[CoversClass(ImageTextShareDataRepository::class)]
#[RunTestsInSeparateProcesses]
final class ImageTextShareDataRepositoryTest extends AbstractRepositoryTestCase
{
    private ImageTextShareDataRepository $repository;

    public function testFindByWithValidCriteriaShouldReturnMatchingEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createImageTextShareData($account, 1, 100);
        $entity2 = $this->createImageTextShareData($account, 2, 200, new \DateTimeImmutable('+1 day'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['shareScene' => 1, 'account' => $account]);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertSame(1, $results[0]->getShareScene());
    }

    public function testFindByWithInvalidFieldShouldThrowException(): void
    {
        $this->expectException(\Exception::class);

        $this->repository->findBy(['nonexistent_field' => 'value']);
    }

    public function testFindByWithOrderByShouldReturnOrderedEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createImageTextShareData($account, 3, 300);
        $entity2 = $this->createImageTextShareData($account, 1, 100, new \DateTimeImmutable('+1 day'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['account' => $account], ['shareScene' => 'ASC']);

        $this->assertCount(2, $results);
        $this->assertSame(1, $results[0]->getShareScene());
        $this->assertSame(3, $results[1]->getShareScene());
    }

    public function testFindByWithLimitShouldReturnLimitedResults(): void
    {
        $account = $this->createAccount();
        for ($i = 1; $i <= 5; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createImageTextShareData($account, $i, $i * 100, $date);
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
            $entity = $this->createImageTextShareData($account, $i, $i * 100, $date);
            $this->persistAndFlush($entity);
        }

        $results = $this->repository->findBy(['account' => $account], ['id' => 'ASC'], 2, 2);

        $this->assertCount(2, $results);
        $this->assertSame(3, $results[0]->getShareScene());
        $this->assertSame(4, $results[1]->getShareScene());
    }

    public function testFindOneByWithValidCriteriaShouldReturnSingleEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createImageTextShareData($account, 5, 500);
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(ImageTextShareData::class, $persistedEntity);

        $result = $this->repository->findOneBy(['id' => $persistedEntity->getId()]);

        $this->assertInstanceOf(ImageTextShareData::class, $result);
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
        $entity1 = $this->createImageTextShareData($account, 5, 500);
        $entity2 = $this->createImageTextShareData($account, 2, 200, new \DateTimeImmutable('+1 day'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $result = $this->repository->findOneBy(
            ['account' => $account],
            ['shareScene' => 'ASC']
        );

        $this->assertInstanceOf(ImageTextShareData::class, $result);
        $this->assertSame(2, $result->getShareScene());
    }

    public function testCountQueryShouldReturnCorrectCount(): void
    {
        $account = $this->createAccount();
        for ($i = 1; $i <= 3; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createImageTextShareData($account, $i, $i * 100, $date);
            $this->persistAndFlush($entity);
        }

        $qb = $this->repository->createQueryBuilder('itsd');
        $count = $qb->select('COUNT(itsd.id)')
            ->where('itsd.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertSame(3, (int) $count);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createImageTextShareData($account, 3, 300);

        $this->repository->save($entity, true);

        $this->assertEntityPersisted($entity);
        $this->assertNotNull($entity->getId());
    }

    public function testSaveWithoutFlushShouldNotFlushImmediately(): void
    {
        $account = $this->createAccount();
        $entity = $this->createImageTextShareData($account, 4, 400);

        $this->repository->save($entity, false);

        $this->assertTrue(self::getEntityManager()->contains($entity));

        self::getEntityManager()->flush();
        $this->assertEntityPersisted($entity);
    }

    public function testSaveWithCompleteDataShouldPersistAllFields(): void
    {
        $account = $this->createAccount();
        $date = new \DateTimeImmutable('2024-01-15');

        $entity = new ImageTextShareData();
        $entity->setAccount($account);
        $entity->setDate($date);
        $entity->setShareScene(2);
        $entity->setShareCount(250);
        $entity->setShareUser(125);

        $this->repository->save($entity, true);

        self::getEntityManager()->clear();
        $saved = $this->repository->find($entity->getId());

        $this->assertInstanceOf(ImageTextShareData::class, $saved);
        $this->assertEquals($date, $saved->getDate());
        $this->assertSame(2, $saved->getShareScene());
        $this->assertSame(250, $saved->getShareCount());
        $this->assertSame(125, $saved->getShareUser());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createImageTextShareData($account, 6, 600);
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(ImageTextShareData::class, $persistedEntity);
        $id = $persistedEntity->getId();

        $this->repository->remove($persistedEntity, true);

        $this->assertEntityNotExists(ImageTextShareData::class, $id);
        $this->assertNull($this->repository->find($id));
    }

    public function testFindByAccountAssociationShouldReturnAccountRelatedEntities(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createImageTextShareData($account1, 1, 100);
        $entity2 = $this->createImageTextShareData($account2, 2, 200, new \DateTimeImmutable('+1 day'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['account' => $account1]);

        $this->assertCount(1, $results);
        $this->assertSame($account1, $results[0]->getAccount());
        $this->assertSame(1, $results[0]->getShareScene());
    }

    public function testCountByAccountAssociationShouldReturnCorrectNumber(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createImageTextShareData($account1, 1, 100);
        $entity2 = $this->createImageTextShareData($account1, 2, 200, new \DateTimeImmutable('+1 day'));
        $entity3 = $this->createImageTextShareData($account2, 3, 300, new \DateTimeImmutable('+2 days'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);
        $this->persistAndFlush($entity3);

        $count = $this->repository->count(['account' => $account1]);

        $this->assertSame(2, $count);
    }

    public function testFindByNullFieldShouldReturnMatchingEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createImageTextShareData($account, 1, 100);
        $entity2 = $this->createImageTextShareData($account, 2, 200, new \DateTimeImmutable('+1 day'));
        $entity2->setShareUser(150);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['shareUser' => null, 'account' => $account]);

        $this->assertCount(1, $results);
        $this->assertNull($results[0]->getShareUser());
        $this->assertSame(1, $results[0]->getShareScene());
    }

    public function testCountWithNullFieldShouldReturnCorrectNumber(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createImageTextShareData($account, 1, 100);
        $entity1->setShareCount(null);
        $entity2 = $this->createImageTextShareData($account, 2, 200, new \DateTimeImmutable('+1 day'));
        $entity2->setShareCount(250);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $count = $this->repository->count(['shareCount' => null, 'account' => $account]);

        $this->assertSame(1, $count);
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ImageTextShareDataRepository::class);
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

    private function createImageTextShareData(Account $account, int $shareScene, int $shareCount, ?\DateTimeImmutable $date = null): ImageTextShareData
    {
        static $counter = 0;
        ++$counter;

        $baseDate = $date ?? new \DateTimeImmutable();
        $uniqueDate = $baseDate->modify("+{$counter} hours");

        $entity = new ImageTextShareData();
        $entity->setAccount($account);
        $entity->setDate($uniqueDate);
        $entity->setShareScene($shareScene);
        $entity->setShareCount($shareCount);

        return $entity;
    }

    protected function createNewEntity(): object
    {
        $account = $this->createAccount();

        return $this->createImageTextShareData($account, 1, 100);
    }

    /**
     * @return ServiceEntityRepository<ImageTextShareData>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
