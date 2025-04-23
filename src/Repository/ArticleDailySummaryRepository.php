<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatOfficialAccountStatsBundle\Entity\ArticleDailySummary;

/**
 * @method ArticleDailySummary|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleDailySummary|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleDailySummary[]    findAll()
 * @method ArticleDailySummary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleDailySummaryRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleDailySummary::class);
    }
}
