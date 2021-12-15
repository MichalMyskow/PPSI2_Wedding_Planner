<?php

namespace App\Repository;

use App\Entity\GuestConflict;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GuestConflict|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuestConflict|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuestConflict[]    findAll()
 * @method GuestConflict[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuestConflictRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GuestConflict::class);
    }

    // /**
    //  * @return GuestConflict[] Returns an array of GuestConflict objects
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
    public function findOneBySomeField($value): ?GuestConflict
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
