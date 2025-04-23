<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatistics;

/**
 * @method ImageTextStatistics|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageTextStatistics|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageTextStatistics[]    findAll()
 * @method ImageTextStatistics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageTextStatisticsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageTextStatistics::class);
    }
}
