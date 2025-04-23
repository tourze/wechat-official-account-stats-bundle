<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatOfficialAccountStatsBundle\Entity\ArticleTotal;

/**
 * @method ArticleTotal|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleTotal|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleTotal[]    findAll()
 * @method ArticleTotal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleTotalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleTotal::class);
    }
}
