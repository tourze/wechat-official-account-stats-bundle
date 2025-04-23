<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareDataHour;

/**
 * @method ImageTextShareDataHour|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageTextShareDataHour|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageTextShareDataHour[]    findAll()
 * @method ImageTextShareDataHour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageTextShareDataHourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageTextShareDataHour::class);
    }
}
