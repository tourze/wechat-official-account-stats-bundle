<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatOfficialAccountStatsBundle\Entity\AdvertisingSpaceData;

/**
 * @method AdvertisingSpaceData|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdvertisingSpaceData|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdvertisingSpaceData[]    findAll()
 * @method AdvertisingSpaceData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertisingSpaceDataRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdvertisingSpaceData::class);
    }
}
