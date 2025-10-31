<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ArticleTotal;
use WechatOfficialAccountStatsBundle\Repository\ArticleTotalRepository;

/**
 * @internal
 */
#[CoversClass(ArticleTotalRepository::class)]
#[RunTestsInSeparateProcesses]
final class ArticleTotalRepositoryTest extends AbstractRepositoryTestCase
{
    private ArticleTotalRepository $repository;

    public function testFindByWithValidCriteriaShouldReturnMatchingEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createArticleTotal($account, 'test_msg_id_1', 'Test Article Title 1');
        $entity2 = $this->createArticleTotal($account, 'test_msg_id_2', 'Test Article Title 2');

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['msgId' => 'test_msg_id_1', 'account' => $account]);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertSame('test_msg_id_1', $results[0]->getMsgId());
    }

    public function testFindByWithInvalidFieldShouldThrowException(): void
    {
        $this->expectException(\Exception::class);

        $this->repository->findBy(['nonexistent_field' => 'value']);
    }

    public function testFindByWithOrderByShouldReturnOrderedEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createArticleTotal($account, 'test_msg_id_1', 'ZZZ Article Title');
        $entity2 = $this->createArticleTotal($account, 'test_msg_id_2', 'AAA Article Title');

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['account' => $account], ['title' => 'ASC']);

        $this->assertCount(2, $results);
        $this->assertSame('AAA Article Title', $results[0]->getTitle());
        $this->assertSame('ZZZ Article Title', $results[1]->getTitle());
    }

    public function testFindByWithLimitShouldReturnLimitedResults(): void
    {
        $account = $this->createAccount();
        for ($i = 0; $i < 5; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createArticleTotal($account, "test_msg_id_{$i}", "Test Article Title {$i}", $date);
            $this->persistAndFlush($entity);
        }

        $results = $this->repository->findBy(['account' => $account], null, 3);

        $this->assertCount(3, $results);
    }

    public function testFindByWithOffsetShouldReturnOffsetResults(): void
    {
        $account = $this->createAccount();
        for ($i = 0; $i < 5; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createArticleTotal($account, "test_msg_id_offset_{$i}", "Test Article Title {$i}", $date);
            $this->persistAndFlush($entity);
        }

        $results = $this->repository->findBy(['account' => $account], ['id' => 'ASC'], 2, 2);

        $this->assertCount(2, $results);
        $this->assertSame('test_msg_id_offset_2', $results[0]->getMsgId());
        $this->assertSame('test_msg_id_offset_3', $results[1]->getMsgId());
    }

    public function testFindOneByWithValidCriteriaShouldReturnSingleEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createArticleTotal($account, 'test_msg_id', 'Test Article Title');
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(ArticleTotal::class, $persistedEntity);

        $result = $this->repository->findOneBy(['id' => $persistedEntity->getId()]);

        $this->assertInstanceOf(ArticleTotal::class, $result);
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
        $entity1 = $this->createArticleTotal($account, 'test_msg_id_1', 'ZZZ Article Title');
        $entity2 = $this->createArticleTotal($account, 'test_msg_id_2', 'AAA Article Title');

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $result = $this->repository->findOneBy(
            [],
            ['title' => 'ASC']
        );

        $this->assertInstanceOf(ArticleTotal::class, $result);
        $this->assertSame('AAA Article Title', $result->getTitle());
    }

    public function testCountQueryShouldReturnCorrectCount(): void
    {
        $account = $this->createAccount();
        for ($i = 0; $i < 3; ++$i) {
            $date = new \DateTimeImmutable("+{$i} days");
            $entity = $this->createArticleTotal($account, "test_count_msg_id_{$i}", "Test Count Article Title {$i}", $date);
            $this->persistAndFlush($entity);
        }

        $qb = $this->repository->createQueryBuilder('at');
        $count = $qb->select('COUNT(at.id)')
            ->where('at.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertSame(3, (int) $count);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createArticleTotal($account, 'test_msg_id', 'Test Article Title');

        $this->repository->save($entity, true);

        $this->assertEntityPersisted($entity);
        $this->assertNotNull($entity->getId());
    }

    public function testSaveWithoutFlushShouldNotFlushImmediately(): void
    {
        $account = $this->createAccount();
        $entity = $this->createArticleTotal($account, 'test_msg_id_no_flush', 'Test Article Title No Flush');

        $this->repository->save($entity, false);

        $this->assertTrue(self::getEntityManager()->contains($entity));

        self::getEntityManager()->flush();
        $this->assertEntityPersisted($entity);
    }

    public function testSaveWithCompleteDataShouldPersistAllFields(): void
    {
        $account = $this->createAccount();
        $date = new \DateTimeImmutable('2024-01-15');
        $statDate = new \DateTimeImmutable('2024-01-20');

        $entity = new ArticleTotal();
        $entity->setAccount($account);
        $entity->setDate($date);
        $entity->setStatDate($statDate);
        $entity->setMsgId('complete_test_msg_id');
        $entity->setTitle('Complete Test Article Title');
        $entity->setTargetUser(1000);
        $entity->setIntPageReadUser(800);
        $entity->setIntPageReadCount(900);
        $entity->setOriPageReadUser(200);
        $entity->setOriPageReadCount(250);
        $entity->setShareUser(50);
        $entity->setShareCount(75);
        $entity->setAddToFavUser(30);
        $entity->setAddToFavCount(35);

        $this->repository->save($entity, true);

        self::getEntityManager()->clear();
        $saved = $this->repository->find($entity->getId());

        $this->assertInstanceOf(ArticleTotal::class, $saved);
        $this->assertEquals($date, $saved->getDate());
        $this->assertEquals($statDate, $saved->getStatDate());
        $this->assertSame('complete_test_msg_id', $saved->getMsgId());
        $this->assertSame('Complete Test Article Title', $saved->getTitle());
        $this->assertSame(1000, $saved->getTargetUser());
        $this->assertSame(800, $saved->getIntPageReadUser());
        $this->assertSame(900, $saved->getIntPageReadCount());
        $this->assertSame(200, $saved->getOriPageReadUser());
        $this->assertSame(250, $saved->getOriPageReadCount());
        $this->assertSame(50, $saved->getShareUser());
        $this->assertSame(75, $saved->getShareCount());
        $this->assertSame(30, $saved->getAddToFavUser());
        $this->assertSame(35, $saved->getAddToFavCount());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $account = $this->createAccount();
        $entity = $this->createArticleTotal($account, 'test_msg_id_remove', 'Test Article Title for Remove');
        $persistedEntity = $this->persistAndFlush($entity);
        $this->assertInstanceOf(ArticleTotal::class, $persistedEntity);
        $id = $persistedEntity->getId();

        $this->repository->remove($persistedEntity, true);

        $this->assertEntityNotExists(ArticleTotal::class, $id);
        $this->assertNull($this->repository->find($id));
    }

    public function testFindByAccountAssociationShouldReturnAccountRelatedEntities(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createArticleTotal($account1, 'test_msg_id_1', 'Test Article Title 1');
        $entity2 = $this->createArticleTotal($account2, 'test_msg_id_2', 'Test Article Title 2');

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['account' => $account1]);

        $this->assertCount(1, $results);
        $this->assertSame($account1, $results[0]->getAccount());
        $this->assertSame('test_msg_id_1', $results[0]->getMsgId());
    }

    public function testCountByAccountAssociationShouldReturnCorrectNumber(): void
    {
        $account1 = $this->createAccount();
        $account2 = $this->createAccount();

        $entity1 = $this->createArticleTotal($account1, 'test_msg_id_1', 'Test Article Title 1');
        $entity2 = $this->createArticleTotal($account1, 'test_msg_id_2', 'Test Article Title 2', new \DateTimeImmutable('+1 day'));
        $entity3 = $this->createArticleTotal($account2, 'test_msg_id_3', 'Test Article Title 3');

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);
        $this->persistAndFlush($entity3);

        $count = $this->repository->count(['account' => $account1]);

        $this->assertSame(2, $count);
    }

    public function testFindByNullFieldShouldReturnMatchingEntities(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createArticleTotal($account, 'test_msg_id_1', 'Test Article Title 1');
        $entity2 = $this->createArticleTotal($account, 'test_msg_id_2', 'Test Article Title 2', new \DateTimeImmutable('+1 day'));
        $entity2->setTargetUser(100);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $results = $this->repository->findBy(['targetUser' => null, 'account' => $account]);

        $this->assertCount(1, $results);
        $this->assertNull($results[0]->getTargetUser());
        $this->assertSame('test_msg_id_1', $results[0]->getMsgId());
    }

    public function testCountWithNullFieldShouldReturnCorrectNumber(): void
    {
        $account = $this->createAccount();
        $entity1 = $this->createArticleTotal($account, 'test_msg_id_1', 'Test Article Title 1');
        $entity2 = $this->createArticleTotal($account, 'test_msg_id_2', 'Test Article Title 2', new \DateTimeImmutable('+1 day'));
        $entity2->setIntPageReadUser(50);

        $this->persistAndFlush($entity1);
        $this->persistAndFlush($entity2);

        $count = $this->repository->count(['intPageReadUser' => null, 'account' => $account]);

        $this->assertSame(1, $count);
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ArticleTotalRepository::class);
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

    private function createArticleTotal(Account $account, string $msgId, string $title, ?\DateTimeImmutable $date = null): ArticleTotal
    {
        static $counter = 0;
        ++$counter;

        $baseDate = $date ?? new \DateTimeImmutable();
        // Ensure unique date and statDate combination for each call
        $uniqueDate = $baseDate->modify("+{$counter} hours");
        $uniqueStatDate = $baseDate->modify("+{$counter} days");

        $entity = new ArticleTotal();
        $entity->setAccount($account);
        $entity->setDate($uniqueDate);
        $entity->setStatDate($uniqueStatDate);
        $entity->setMsgId($msgId);
        $entity->setTitle($title);

        return $entity;
    }

    protected function createNewEntity(): object
    {
        $account = $this->createAccount();

        return $this->createArticleTotal($account, 'test_msg_id', 'Test Article Title');
    }

    /**
     * @return ServiceEntityRepository<ArticleTotal>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
