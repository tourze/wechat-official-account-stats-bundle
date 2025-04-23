<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatisticsHour;

/**
 * @method ImageTextStatisticsHour|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageTextStatisticsHour|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageTextStatisticsHour[]    findAll()
 * @method ImageTextStatisticsHour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageTextStatisticsHourRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageTextStatisticsHour::class);
    }
}
