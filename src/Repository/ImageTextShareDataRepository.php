<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareData;

/**
 * @method ImageTextShareData|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageTextShareData|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageTextShareData[]    findAll()
 * @method ImageTextShareData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageTextShareDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageTextShareData::class);
    }
}
