<?php

namespace App\Repository;

use App\Entity\Guest;
use App\Entity\Wedding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Guest|null find($id, $lockMode = null, $lockVersion = null)
 * @method Guest|null findOneBy(array $criteria, array $orderBy = null)
 * @method Guest[]    findAll()
 * @method Guest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Guest::class);
    }

    public function findAllWithoutInvite(Wedding $wedding)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.acceptation = :acceptation')
            ->setParameter('acceptation', false)
            ->andWhere('g.invitationSent = :invitationSent')
            ->setParameter('invitationSent', false)
            ->andWhere('g.wedding = :wedding')
            ->setParameter('wedding', $wedding)
            ->andWhere('g.email IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    public function findByConflictedGuest(Guest $guest)
    {
        return $this->createQueryBuilder('g')
            ->andWhere(':conflictedGuest MEMBER OF g.conflictedGuests')
            ->setParameter('conflictedGuest', $guest)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Guest[] Returns an array of Guest objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Guest
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
