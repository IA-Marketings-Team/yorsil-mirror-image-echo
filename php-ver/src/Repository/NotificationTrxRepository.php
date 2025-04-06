<?php

namespace App\Repository;

use App\Entity\NotificationTrx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotificationTrx|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationTrx|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationTrx[]    findAll()
 * @method NotificationTrx[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationTrxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationTrx::class);
    }

    // /**
    //  * @return NotificationTrx[] Returns an array of NotificationTrx objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NotificationTrx
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findAdminNotifTrxNotRead(): array
    {
        $qb = $this->createQueryBuilder('n')
        ->where('n.is_read = :is_read')
        ->andWhere('n.is_admin = :is_admin')
            ->setParameter('is_read', false)
            ->setParameter('is_admin', true)
            ->orderBy('n.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
        return $qb;
    }
    public function findBoutNotifTrxNotRead(): array
    {
        $qb = $this->createQueryBuilder('n')
        ->where('n.is_read = :is_read')
        ->andWhere('n.is_admin = :is_admin')
            ->setParameter('is_read', false)
            ->setParameter('is_admin', false)
            ->orderBy('n.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
        return $qb;
    }
}
