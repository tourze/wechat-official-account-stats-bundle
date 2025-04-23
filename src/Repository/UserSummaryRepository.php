<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatOfficialAccountStatsBundle\Entity\UserSummary;

/**
 * @method UserSummary|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSummary|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSummary[]    findAll()
 * @method UserSummary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSummaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSummary::class);
    }
}
