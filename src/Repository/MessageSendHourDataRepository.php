<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatOfficialAccountStatsBundle\Entity\MessageSendHourData;

/**
 * @method MessageSendHourData|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageSendHourData|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageSendHourData[]    findAll()
 * @method MessageSendHourData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageSendHourDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageSendHourData::class);
    }
}
