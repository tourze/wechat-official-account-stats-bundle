<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummaryHour;

/**
 * @method InterfaceSummaryHour|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterfaceSummaryHour|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterfaceSummaryHour[]    findAll()
 * @method InterfaceSummaryHour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterfaceSummaryHourRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterfaceSummaryHour::class);
    }
}
