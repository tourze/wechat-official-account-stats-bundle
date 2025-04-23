<?php

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatOfficialAccountStatsBundle\Entity\MessageSendWeekData;

/**
 * @method MessageSendWeekData|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageSendWeekData|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageSendWeekData[]    findAll()
 * @method MessageSendWeekData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageSendWeekDataRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageSendWeekData::class);
    }
}
