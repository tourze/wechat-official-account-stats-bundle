<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatOfficialAccountStatsBundle\Entity\AdvertisingSpaceData;

/**
 * @extends ServiceEntityRepository<AdvertisingSpaceData>
 */
#[AsRepository(entityClass: AdvertisingSpaceData::class)]
class AdvertisingSpaceDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdvertisingSpaceData::class);
    }

    public function save(AdvertisingSpaceData $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AdvertisingSpaceData $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
