<?php

namespace App\Repository;

use App\Entity\Pet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pet[]    findAll()
 * @method Pet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pet::class);
    }

    public function getPetsPaginationQuery($owner): Query
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('p.name', 'ASC')
            ->getQuery();
    }
}
