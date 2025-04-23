<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatOfficialAccountStatsBundle\Entity\RebateGoodsData;

/**
 * @method RebateGoodsData|null find($id, $lockMode = null, $lockVersion = null)
 * @method RebateGoodsData|null findOneBy(array $criteria, array $orderBy = null)
 * @method RebateGoodsData[]    findAll()
 * @method RebateGoodsData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RebateGoodsDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RebateGoodsData::class);
    }
}
