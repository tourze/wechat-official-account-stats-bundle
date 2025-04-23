<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatOfficialAccountStatsBundle\Entity\MessageSendMonthData;

/**
 * @method MessageSendMonthData|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageSendMonthData|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageSendMonthData[]    findAll()
 * @method MessageSendMonthData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageSendMonthDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageSendMonthData::class);
    }
}
