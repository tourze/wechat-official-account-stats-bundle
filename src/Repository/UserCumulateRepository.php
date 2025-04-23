<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatOfficialAccountStatsBundle\Entity\UserCumulate;

/**
 * @method UserCumulate|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCumulate|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCumulate[]    findAll()
 * @method UserCumulate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCumulateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCumulate::class);
    }
}
