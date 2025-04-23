<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummary;

/**
 * @method InterfaceSummary|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterfaceSummary|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterfaceSummary[]    findAll()
 * @method InterfaceSummary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterfaceSummaryRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterfaceSummary::class);
    }
}
