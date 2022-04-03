<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function getDoctors()
    {
        return $this->createQueryBuilder('d')
            ->andWhere('CAST(d.roles as text) = \'["ROLE_DOCTOR"]\'')
            ->orderBy('d.lastName', 'ASC')
            ->orderBy('d.firstName', 'ASC')
            ->orderBy('d.patronymic', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findDoctorByLogin($login): ?User
    {
        return $this->createQueryBuilder('d')
            ->andWhere('CAST(d.roles as text) = \'["ROLE_DOCTOR"]\'')
            ->andWhere('d.login = :val')
            ->setParameter('val', $login)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
