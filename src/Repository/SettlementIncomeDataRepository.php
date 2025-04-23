<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatOfficialAccountStatsBundle\Entity\SettlementIncomeData;

/**
 * @method SettlementIncomeData|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettlementIncomeData|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettlementIncomeData[]    findAll()
 * @method SettlementIncomeData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettlementIncomeDataRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SettlementIncomeData::class);
    }
}
