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
            ->getQuery();
    }
}
