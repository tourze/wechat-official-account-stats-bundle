<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\UserCumulate;
use WechatOfficialAccountStatsBundle\Repository\UserCumulateRepository;

/**
 * @internal
 */
#[CoversClass(UserCumulateRepository::class)]
#[RunTestsInSeparateProcesses]
final class UserCumulateRepositoryTest extends AbstractRepositoryTestCase
{
    private UserCumulateRepository $repository;

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
        $entity1 = $this->createUserCumulate($account, 300);
        $entity2 = $this->createUserCumulate($account, 100, new \DateTimeImmutable('+1 day'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $result = $this->repository->findOneBy(
            ['account' => $account],
            ['cumulateUser' => 'DESC']
        );

        $this->assertInstanceOf(UserCumulate::class, $result);
        $this->assertSame(300, $result->getCumulateUser());
    }

    public function testCountQueryShouldReturnCorrectCount(): void
    {
        $account = $this->createAccount();
        for ($i = 0; $i < 3; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createUserCumulate($account, 100 + $i, $date);
            $this->persistAndFlush($entity);
        }

        $qb = $this->repository->createQueryBuilder('uc');
        $count = $qb->select('COUNT(uc.id)')
            ->where('uc.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertSame(3, (int) $count);
    }

    public function testFindByWithAssociationCriteriaShouldReturnMatchingEntities(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createUserCumulate($account1, 100);
        $entity2 = $this->createUserCumulate($account2, 200);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['account' => $account1]);

        $this->assertCount(1, $results);
        $this->assertSame($account1->getId(), $results[0]->getAccount()->getId());
    }

    public function testFindOneByWithAssociationCriteriaShouldReturnEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createUserCumulate($account, 100);
        $entity = $this->persistAndFlush($entity);

        $result = $this->repository->findOneBy(['account' => $account]);

        $this->assertInstanceOf(UserCumulate::class, $result);
        $this->assertSame($account->getId(), $result->getAccount()->getId());
    }

    public function testCountWithAssociationCriteriaShouldReturnCorrectNumber(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createUserCumulate($account1, 100);
        $entity2 = $this->createUserCumulate($account1, 200, new \DateTimeImmutable('+1 day'));
        $entity3 = $this->createUserCumulate($account2, 300);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);
        $this->persistAndFlush($entity3);

        $count = $this->repository->count(['account' => $account1]);

        $this->assertSame(2, $count);
    }

    public function testFindByWithNullValueShouldReturnMatchingEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createUserCumulate($account, null);
        $entity2 = $this->createUserCumulate($account, 200, new \DateTimeImmutable('+1 day'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['cumulateUser' => null, 'account' => $account]);

        $this->assertCount(1, $results);
        $this->assertNull($results[0]->getCumulateUser());
    }

    public function testFindOneByWithNullValueShouldReturnEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createUserCumulate($account, null);
        $entity = $this->persistAndFlush($entity);

        $result = $this->repository->findOneBy(['cumulateUser' => null]);

        $this->assertInstanceOf(UserCumulate::class, $result);
        $this->assertNull($result->getCumulateUser());
    }

    public function testCountWithNullValueShouldReturnCorrectNumber(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createUserCumulate($account, null);
        $entity2 = $this->createUserCumulate($account, 100, new \DateTimeImmutable('+1 day'));

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $count = $this->repository->count(['cumulateUser' => null, 'account' => $account]);

        $this->assertSame(1, $count);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createUserCumulate($account, 100);

        $this->repository->save($entity, true);

        $this->assertEntityPersisted($entity);
        $this->assertNotNull($entity->getId());
    }

    public function testSaveWithoutFlushShouldNotFlushImmediately(): void
    {
        $account = $this->createAccount();
        $entity = $this->createUserCumulate($account, 200);

        $this->repository->save($entity, false);

        $this->assertTrue(self::getEntityManager()->contains($entity));

        self::getEntityManager()->flush();
        $this->assertEntityPersisted($entity);
    }

    public function testSaveWithCompleteDataShouldPersistAllFields(): void
    {
        $account = $this->createAccount();
        $date = new \DateTimeImmutable('2024-01-15');

        $entity = new UserCumulate();
        $entity->setAccount($account);
        $entity->setDate($date);
        $entity->setCumulateUser(1500);

        $this->repository->save($entity, true);

        self::getEntityManager()->clear();
        $saved = $this->repository->find($entity->getId());

        $this->assertInstanceOf(UserCumulate::class, $saved);
        $this->assertEquals($date, $saved->getDate());
        $this->assertSame(1500, $saved->getCumulateUser());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createUserCumulate($account, 300);
        $entity = $this->persistAndFlush($entity);
        $id = $entity->getId();

        $this->assertInstanceOf(UserCumulate::class, $entity);
        $this->repository->remove($entity, true);

        $this->assertEntityNotExists(UserCumulate::class, $id);
        $this->assertNull($this->repository->find($id));
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(UserCumulateRepository::class);
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

    private function createUserCumulate(Account $account, ?int $cumulateUser, ?\DateTimeImmutable $date = null): UserCumulate
    {
        $entity = new UserCumulate();
        $entity->setAccount($account);
        $entity->setDate($date ?? new \DateTimeImmutable());
        if (null !== $cumulateUser) {
            $entity->setCumulateUser($cumulateUser);
        }

        return $entity;
    }

    protected function createNewEntity(): object
    {
        $account = $this->createAccount();
        $entity = new UserCumulate();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());
        $entity->setCumulateUser(100);

        return $entity;
    }

    protected function getRepository(): UserCumulateRepository
    {
        return $this->repository;
    }
}
