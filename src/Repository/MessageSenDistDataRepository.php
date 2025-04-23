<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatOfficialAccountStatsBundle\Entity\MessageSenDistData;

/**
 * @method MessageSenDistData|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageSenDistData|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageSenDistData[]    findAll()
 * @method MessageSenDistData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageSenDistDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageSenDistData::class);
    }
}
