<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\UserSummary;
use WechatOfficialAccountStatsBundle\Enum\UserSummarySource;
use WechatOfficialAccountStatsBundle\Repository\UserSummaryRepository;

/**
 * @internal
 */
#[CoversClass(UserSummaryRepository::class)]
#[RunTestsInSeparateProcesses]
final class UserSummaryRepositoryTest extends AbstractRepositoryTestCase
{
    private UserSummaryRepository $repository;

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

    public function testCountQueryShouldReturnCorrectCount(): void
    {
        $account = $this->createAccount();
        for ($i = 0; $i < 3; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createUserSummary($account, UserSummarySource::SEARCH, 100 + $i, $date);
            $this->persistAndFlush($entity);
        }

        $qb = $this->repository->createQueryBuilder('us');
        $count = $qb->select('COUNT(us.id)')
            ->where('us.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertSame(3, (int) $count);
    }

    public function testFindOneByShouldRespectOrderByClause(): void
    {
        $account = $this->createAccount();
        $date1 = new \DateTimeImmutable('+1 day');
        $date2 = new \DateTimeImmutable('+2 days');
        $entity1 = $this->createUserSummary($account, UserSummarySource::SEARCH, 300, $date1);
        $entity2 = $this->createUserSummary($account, UserSummarySource::SEARCH, 100, $date2);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $result = $this->repository->findOneBy(
            ['source' => UserSummarySource::SEARCH],
            ['newUser' => 'DESC']
        );

        $this->assertInstanceOf(UserSummary::class, $result);
        $this->assertSame(300, $result->getNewUser());
    }

    public function testFindByWithAssociationCriteriaShouldReturnMatchingEntities(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createUserSummary($account1, UserSummarySource::SEARCH, 100);
        $entity2 = $this->createUserSummary($account2, UserSummarySource::SEARCH, 200);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['account' => $account1]);

        $this->assertCount(1, $results);
        $this->assertSame($account1, $results[0]->getAccount());
        $this->assertSame(100, $results[0]->getNewUser());
    }

    public function testFindOneByWithAssociationCriteriaShouldReturnEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createUserSummary($account, UserSummarySource::SEARCH, 100);
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(UserSummary::class, $persistedEntity);

        $result = $this->repository->findOneBy(['account' => $account]);

        $this->assertInstanceOf(UserSummary::class, $result);
        $this->assertSame($account->getId(), $result->getAccount()->getId());
    }

    public function testCountByAccountAssociationShouldReturnCorrectCount(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createUserSummary($account1, UserSummarySource::SEARCH, 100);
        $entity2 = $this->createUserSummary($account1, UserSummarySource::CARD, 200);
        $entity3 = $this->createUserSummary($account2, UserSummarySource::SEARCH, 300);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);
        $this->persistAndFlush($entity3);

        $count = $this->repository->count(['account' => $account1]);

        $this->assertSame(2, $count);
    }

    public function testCountWithNullValueShouldReturnCorrectNumber(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createUserSummary($account, UserSummarySource::SEARCH, 100);
        $entity2 = $this->createUserSummary($account, UserSummarySource::CARD, 200);
        $entity2->setCancelUser(50);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $count = $this->repository->count(['cancelUser' => null, 'account' => $account]);

        $this->assertSame(1, $count);
    }

    public function testFindByWithNullValueShouldReturnMatchingEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createUserSummary($account, UserSummarySource::SEARCH, 100);
        $entity2 = $this->createUserSummary($account, UserSummarySource::CARD, 200);
        $entity2->setCancelUser(50);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['cancelUser' => null, 'account' => $account]);

        $this->assertCount(1, $results);
        $this->assertNull($results[0]->getCancelUser());
        $this->assertSame(100, $results[0]->getNewUser());
    }

    public function testFindOneByWithNullValueShouldReturnEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createUserSummary($account, UserSummarySource::SEARCH, 100);
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(UserSummary::class, $persistedEntity);

        $result = $this->repository->findOneBy(['cancelUser' => null]);

        $this->assertInstanceOf(UserSummary::class, $result);
        $this->assertNull($result->getCancelUser());
    }

    public function testSaveShouldPersistEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createUserSummary($account, UserSummarySource::SEARCH, 50);

        $this->repository->save($entity, true);

        $this->assertEntityPersisted($entity);
        $this->assertNotNull($entity->getId());
    }

    public function testSaveWithoutFlushShouldNotFlushImmediately(): void
    {
        $account = $this->createAccount();
        $entity = $this->createUserSummary($account, UserSummarySource::CARD, 100);

        $this->repository->save($entity, false);

        $this->assertTrue(self::getEntityManager()->contains($entity));

        self::getEntityManager()->flush();
        $this->assertEntityPersisted($entity);
    }

    public function testSaveWithCompleteDataShouldPersistAllFields(): void
    {
        $account = $this->createAccount();
        $date = new \DateTimeImmutable('2024-01-15');

        $entity = new UserSummary();
        $entity->setAccount($account);
        $entity->setDate($date);
        $entity->setSource(UserSummarySource::SCAN_QRCODE);
        $entity->setNewUser(75);
        $entity->setCancelUser(25);

        $this->repository->save($entity, true);

        self::getEntityManager()->clear();
        $saved = $this->repository->find($entity->getId());

        $this->assertInstanceOf(UserSummary::class, $saved);
        $this->assertEquals($date, $saved->getDate());
        $this->assertSame(UserSummarySource::SCAN_QRCODE, $saved->getSource());
        $this->assertSame(75, $saved->getNewUser());
        $this->assertSame(25, $saved->getCancelUser());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createUserSummary($account, UserSummarySource::ARTICLE_ACCOUNT, 200);
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(UserSummary::class, $persistedEntity);
        $id = $persistedEntity->getId();

        $this->repository->remove($persistedEntity, true);

        $this->assertEntityNotExists(UserSummary::class, $id);
        $this->assertNull($this->repository->find($id));
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(UserSummaryRepository::class);
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

    private function createUserSummary(Account $account, UserSummarySource $source, int $newUser, ?\DateTimeImmutable $date = null): UserSummary
    {
        $entity = new UserSummary();
        $entity->setAccount($account);
        $entity->setDate($date ?? new \DateTimeImmutable());
        $entity->setSource($source);
        $entity->setNewUser($newUser);

        return $entity;
    }

    protected function createNewEntity(): object
    {
        $account = $this->createAccount();
        $entity = new UserSummary();
        $entity->setAccount($account);
        $entity->setDate(new \DateTimeImmutable());
        $entity->setSource(UserSummarySource::SEARCH);
        $entity->setNewUser(100);

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<UserSummary>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
