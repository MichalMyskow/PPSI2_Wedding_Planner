<?php

namespace App\Repository;

use App\Entity\Wedding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Wedding|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wedding|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wedding[]    findAll()
 * @method Wedding[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeddingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wedding::class);
    }

    public function findOneByDateAndRoom(Wedding $wedding): ?Wedding
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.date BETWEEN :date1 AND :date2')
            ->setParameter('date1', $wedding->getDate()->format('Y-m-d 00:00:00'))
            ->setParameter('date2', $wedding->getDate()->format('Y-m-d 23:59:59'))
            ->andWhere('w.room = :room')
            ->setParameter('room', $wedding->getRoom())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
