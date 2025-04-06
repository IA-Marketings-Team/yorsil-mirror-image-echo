<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    // /**
    //  * @return Notification[] Returns an array of Notification objects
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
    public function findOneBySomeField($value): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findUnreadAdminNotificationsForToday(): array
    {
        $datetime = new \DateTime();
        $aujourdhui = $datetime->format('Y-m-d');
        $qb = $this->createQueryBuilder('n')
            ->where('n.isRead IS NULL')
            ->andWhere('n.is_admin = :is_admin')
            ->andWhere("DATE_FORMAT(n.date, '%Y-%m-%d') = :aujourdhui") // Remplace 'date' par le champ correspondant dans ton entité
            ->setParameter('is_admin', true)
            ->setParameter('aujourdhui', $aujourdhui)
            ->orderBy('n.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
        return $qb;
    }

    public function countUnreadAdminNotificationsForToday(): int
    {
        $datetime = new \DateTime();
        $aujourdhui = $datetime->format('Y-m-d');
        $qb = $this->createQueryBuilder('n')
            ->select('COUNT(n)')
            ->where('n.isRead IS NULL')
            ->andWhere('n.is_admin = :is_admin')
            ->andWhere("DATE_FORMAT(n.date, '%Y-%m-%d') = :aujourdhui")
            ->setParameter('aujourdhui', $aujourdhui)
            ->setParameter('is_admin', true)
            ;

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
