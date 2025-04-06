<?php

namespace App\Repository;

use App\Entity\Notificationrechargement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notificationrechargement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notificationrechargement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notificationrechargement[]    findAll()
 * @method Notificationrechargement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationrechargementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notificationrechargement::class);
    }

    // /**
    //  * @return Notificationrechargement[] Returns an array of Notificationrechargement objects
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
    public function findOneBySomeField($value): ?Notificationrechargement
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findUnreadAdminNotificationsRechargementForToday(): array
    {
        $datetime = new \DateTime();
        $aujourdhui = $datetime->format('Y-m-d');
        $qb = $this->createQueryBuilder('n')
            ->where('n.isRead IS NULL')
            ->andWhere('n.isAdmin = :is_admin')
            ->andWhere("DATE_FORMAT(n.date, '%Y-%m-%d') = :aujourdhui") // Remplace 'date' par le champ correspondant dans ton entité
            ->setParameter('is_admin', true)
            ->setParameter('aujourdhui', $aujourdhui)
            ->orderBy('n.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
        return $qb;
    }

    public function countUnreadAdminNotificationsRechargementForToday(): int
    {
        $datetime = new \DateTime();
        $aujourdhui = $datetime->format('Y-m-d');
        $qb = $this->createQueryBuilder('n')
            ->select('COUNT(n)')
            ->where('n.isRead IS NULL')
            ->andWhere('n.isAdmin = :is_admin')
            ->andWhere("DATE_FORMAT(n.date, '%Y-%m-%d') = :aujourdhui")
            ->setParameter('aujourdhui', $aujourdhui)
            ->setParameter('is_admin', true)
            ;

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

}
