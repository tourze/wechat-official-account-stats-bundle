<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ArticleDailySummary;
use WechatOfficialAccountStatsBundle\Repository\ArticleDailySummaryRepository;

/**
 * @internal
 */
#[CoversClass(ArticleDailySummaryRepository::class)]
#[RunTestsInSeparateProcesses]
final class ArticleDailySummaryRepositoryTest extends AbstractRepositoryTestCase
{
    private ArticleDailySummaryRepository $repository;

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
        $entity1 = $this->createArticleDailySummary($account, 'test_msg_id_1', 'ZZZ Article Title');
        $entity2 = $this->createArticleDailySummary($account, 'test_msg_id_2', 'AAA Article Title');

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $result = $this->repository->findOneBy(
            [],
            ['title' => 'ASC']
        );

        $this->assertInstanceOf(ArticleDailySummary::class, $result);
        $this->assertSame('AAA Article Title', $result->getTitle());
    }

    public function testCountQueryShouldReturnCorrectCount(): void
    {
        $account = $this->createAccount();
        for ($i = 0; $i < 3; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createArticleDailySummary($account, "test_msg_id_{$i}", "Test Article Title {$i}", $date);
            $this->persistAndFlush($entity);
        }

        $qb = $this->repository->createQueryBuilder('ads');
        $count = $qb->select('COUNT(ads.id)')
            ->where('ads.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertSame(3, (int) $count);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createArticleDailySummary($account, 'test_msg_id', 'Test Article Title');

        $this->repository->save($entity, true);

        $this->assertEntityPersisted($entity);
        $this->assertNotNull($entity->getId());
    }

    public function testSaveWithoutFlushShouldNotFlushImmediately(): void
    {
        $account = $this->createAccount();
        $entity = $this->createArticleDailySummary($account, 'test_msg_id_no_flush', 'Test Article Title No Flush');

        $this->repository->save($entity, false);

        $this->assertTrue(self::getEntityManager()->contains($entity));

        self::getEntityManager()->flush();
        $this->assertEntityPersisted($entity);
    }

    public function testSaveWithCompleteDataShouldPersistAllFields(): void
    {
        $account = $this->createAccount();
        $date = new \DateTimeImmutable('2024-01-15');

        $entity = new ArticleDailySummary();
        $entity->setAccount($account);
        $entity->setDate($date);
        $entity->setMsgId('complete_test_msg_id');
        $entity->setTitle('Complete Test Article Title');
        $entity->setIntPageReadUser(500);
        $entity->setIntPageReadCount(600);
        $entity->setOriPageReadUser(100);
        $entity->setOriPageReadCount(120);
        $entity->setShareUser(25);
        $entity->setShareCount(30);
        $entity->setAddToFavUser(15);
        $entity->setAddToFavCount(18);

        $this->repository->save($entity, true);

        self::getEntityManager()->clear();
        $saved = $this->repository->find($entity->getId());

        $this->assertInstanceOf(ArticleDailySummary::class, $saved);
        $this->assertEquals($date, $saved->getDate());
        $this->assertSame('complete_test_msg_id', $saved->getMsgId());
        $this->assertSame('Complete Test Article Title', $saved->getTitle());
        $this->assertSame(500, $saved->getIntPageReadUser());
        $this->assertSame(600, $saved->getIntPageReadCount());
        $this->assertSame(100, $saved->getOriPageReadUser());
        $this->assertSame(120, $saved->getOriPageReadCount());
        $this->assertSame(25, $saved->getShareUser());
        $this->assertSame(30, $saved->getShareCount());
        $this->assertSame(15, $saved->getAddToFavUser());
        $this->assertSame(18, $saved->getAddToFavCount());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createArticleDailySummary($account, 'test_msg_id_remove', 'Test Article Title for Remove');
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(ArticleDailySummary::class, $persistedEntity);
        $id = $persistedEntity->getId();

        $this->repository->remove($persistedEntity, true);

        $this->assertEntityNotExists(ArticleDailySummary::class, $id);
        $this->assertNull($this->repository->find($id));
    }

    public function testFindByWithAssociationCriteriaShouldReturnMatchingEntities(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createArticleDailySummary($account1, 'test_msg_id_1', 'Test Article Title 1');
        $entity2 = $this->createArticleDailySummary($account2, 'test_msg_id_2', 'Test Article Title 2');

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['account' => $account1]);

        $this->assertCount(1, $results);
        $this->assertSame($account1->getId(), $results[0]->getAccount()->getId());
    }

    public function testFindOneByWithAssociationCriteriaShouldReturnEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createArticleDailySummary($account, 'test_msg_id', 'Test Article Title');
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(ArticleDailySummary::class, $persistedEntity);

        $result = $this->repository->findOneBy(['account' => $account]);

        $this->assertInstanceOf(ArticleDailySummary::class, $result);
        $this->assertSame($account->getId(), $result->getAccount()->getId());
    }

    public function testCountWithAssociationCriteriaShouldReturnCorrectNumber(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createArticleDailySummary($account1, 'test_msg_id_1', 'Test Article Title 1');
        $entity2 = $this->createArticleDailySummary($account1, 'test_msg_id_2', 'Test Article Title 2');
        $entity3 = $this->createArticleDailySummary($account2, 'test_msg_id_3', 'Test Article Title 3');

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);
        $this->persistAndFlush($entity3);

        $count = $this->repository->count(['account' => $account1]);

        $this->assertSame(2, $count);
    }

    public function testFindByWithNullValueShouldReturnMatchingEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createArticleDailySummary($account, 'test_msg_id_1', 'Test Article Title 1');
        $entity2 = $this->createArticleDailySummary($account, 'test_msg_id_2', 'Test Article Title 2');
        $entity2->setIntPageReadUser(100);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['intPageReadUser' => null, 'account' => $account]);

        $this->assertCount(1, $results);
        $this->assertNull($results[0]->getIntPageReadUser());
    }

    public function testFindOneByWithNullValueShouldReturnEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createArticleDailySummary($account, 'test_msg_id', 'Test Article Title');
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(ArticleDailySummary::class, $persistedEntity);

        $result = $this->repository->findOneBy(['shareUser' => null]);

        $this->assertInstanceOf(ArticleDailySummary::class, $result);
        $this->assertNull($result->getShareUser());
    }

    public function testCountWithNullValueShouldReturnCorrectNumber(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createArticleDailySummary($account, 'test_msg_id_1', 'Test Article Title 1');
        $entity2 = $this->createArticleDailySummary($account, 'test_msg_id_2', 'Test Article Title 2');
        $entity2->setShareCount(10);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $count = $this->repository->count(['shareCount' => null, 'account' => $account]);

        $this->assertSame(1, $count);
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ArticleDailySummaryRepository::class);
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

    private function createArticleDailySummary(Account $account, string $msgId, string $title, ?\DateTimeImmutable $date = null): ArticleDailySummary
    {
        $entity = new ArticleDailySummary();
        $entity->setAccount($account);
        $entity->setDate($date ?? new \DateTimeImmutable());
        $entity->setMsgId($msgId);
        $entity->setTitle($title);

        return $entity;
    }

    protected function createNewEntity(): object
    {
        $account = $this->createAccount();

        return $this->createArticleDailySummary($account, 'test_msg_id_' . uniqid(), 'Test Article Title ' . uniqid());
    }

    /**
     * @return ServiceEntityRepository<ArticleDailySummary>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
