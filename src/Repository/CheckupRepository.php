<?php

namespace App\Repository;

use App\Entity\Checkup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Checkup|null find($id, $lockMode = null, $lockVersion = null)
 * @method Checkup|null findOneBy(array $criteria, array $orderBy = null)
 * @method Checkup[]    findAll()
 * @method Checkup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CheckupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Checkup::class);
    }

    public function getCheckupsHistoryPaginationQuery($pets, $statuses): Query
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.pet IN (:pets)')
            ->andWhere('c.status IN (:statuses)')
            ->setParameter('statuses', $statuses)
            ->setParameter('pets', $pets)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ;
    }

    public function getAllCheckupsByDate($date)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = \'Назначен\'')
            ->andWhere('c.date between :dateStart and :dateEnd')
            ->setParameter('dateStart', $date->format('Y-m-d') . ' 00:00:00')
            ->setParameter('dateEnd', $date->format('Y-m-d') . ' 23:59:59')
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
}
