<?php

namespace App\Repository;

use App\Entity\Checkup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    // /**
    //  * @return Checkup[] Returns an array of Checkup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Checkup
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
