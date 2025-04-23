<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatOfficialAccountStatsBundle\Entity\MessageSendData;

/**
 * @method MessageSendData|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageSendData|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageSendData[]    findAll()
 * @method MessageSendData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageSendDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageSendData::class);
    }
}
