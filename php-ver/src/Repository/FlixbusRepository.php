<?php

namespace App\Repository;

use App\Entity\Flixbus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Flixbus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Flixbus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Flixbus[]    findAll()
 * @method Flixbus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlixbusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flixbus::class);
    }

    // /**
    //  * @return Flixbus[] Returns an array of Flixbus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Flixbus
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function countReservationByMonth()
    {
        $datetime = new \DateTime();
        $year = $datetime->format('Y');
        $month = $datetime->format('m');

        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('MONTH(r.date_resa) = :month')
            ->andWhere('YEAR(r.date_resa) = :year')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countReservationByMonthBefore()
    {
        $datetime = new \DateTime('now -1 month');
        $year = $datetime->format('Y');
        $month = $datetime->format('m');

        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('MONTH(r.date_resa) = :month')
            ->andWhere('YEAR(r.date_resa) = :year')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countReservationByYear()
    {
        $datetime = new \DateTime();
        $year = $datetime->format('Y');

        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('YEAR(r.date_resa) = :year')
            ->setParameter('year', $year)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function countReservationDays()
    {
        $datetime = new \DateTime();
        $aujourdhui = $datetime->format('Y-m-d');
        $compte_journalier = 0;

        $result = $this->createQueryBuilder('r')
                        ->select('COUNT(r.id)')
                        ->andWhere("DATE_FORMAT(r.date_resa, '%Y-%m-%d') = :aujourdhui")
                        ->setParameter('aujourdhui', $aujourdhui)
                        ->getQuery()
                        ->getSingleScalarResult();
        
        return $result;
    }

    public function findByDays($_idb)
    {
        $datetime = new \DateTime();
        $aujourdhui = $datetime->format('Y-m-d');

        $result = $this->createQueryBuilder('f')
                        ->leftjoin('f.boutique','boutique')
                        ->andWhere("DATE_FORMAT(f.date_resa, '%Y-%m-%d') = :aujourdhui")
                        ->andWhere('boutique.id = :id')
                        ->orderBy('f.id', 'DESC')
                        ->setParameter('id', $_idb)
                        ->setParameter('aujourdhui', $aujourdhui)
                        ->getQuery()
                        ->getResult();
        
        return $result;
    }

    public function findByMonths($_idb)
    {
        $datetime = new \DateTime();
        $year = $datetime->format('Y');
        $month = $datetime->format('m');

        $result = $this->createQueryBuilder('f')
                        ->leftjoin('f.boutique','boutique')
                        ->andWhere('MONTH(f.date_resa) = :month')
                        ->andWhere('YEAR(f.date_resa) = :year')
                        ->andWhere('boutique.id = :id')
                        ->orderBy('f.id', 'DESC')
                        ->setParameter('id', $_idb)
                        ->setParameter('month', $month)
                        ->setParameter('year', $year)
                        ->getQuery()
                        ->getResult();
        
        return $result;
    }

    public function findByMonthsBefore($_idb)
    {
        $datetime = new \DateTime();
        $datetime->modify('-1 month'); // Aller au mois précédent

        $year = $datetime->format('Y');
        $month = $datetime->format('m');

        return $this->createQueryBuilder('f')
                    ->leftJoin('f.boutique', 'boutique')
                    ->andWhere('MONTH(f.date_resa) = :month')
                    ->andWhere('YEAR(f.date_resa) = :year')
                    ->andWhere('boutique.id = :id')
                    ->orderBy('f.id', 'DESC')
                    ->setParameter('id', $_idb)
                    ->setParameter('month', $month)
                    ->setParameter('year', $year)
                    ->getQuery()
                    ->getResult();
    }

    public function findByYears($_idb)
    {
        $datetime = new \DateTime();
        $year = $datetime->format('Y');

        $result = $this->createQueryBuilder('f')
                        ->leftjoin('f.boutique','boutique')
                        ->andWhere('YEAR(f.date_resa) = :year')
                        ->andWhere('boutique.id = :id')
                        ->orderBy('f.id', 'DESC')
                        ->setParameter('id', $_idb)
                        ->setParameter('year', $year)
                        ->getQuery()
                        ->getResult();
        
        return $result;
    }

    public function findByMultipleValues($_date_debut,$_date_fin,$_bout_id)
    {
        $qb = $this->createQueryBuilder('c'); 
        return $qb
            ->leftjoin('c.boutique','boutique')
            ->andWhere('boutique.id = :id')
            ->andWhere("c.date_resa <= :datefin")
            ->andWhere(":datedebut <= c.date_resa") // Vérification de la plage de dates
            ->orderBy('c.date_resa', 'DESC')
            ->setParameter('id', $_bout_id)
            ->setParameter('datedebut', $_date_debut)
            ->setParameter('datefin', $_date_fin)
            ->getQuery()
            ->getResult();
    }

    public function findCAByMonth()
    {
        $datetime = new \DateTime();
        $year = $datetime->format('Y');
        $month = $datetime->format('m');

        $result = $this->createQueryBuilder('r')
            ->select('SUM(r.montant_total) AS total')
            ->andWhere('MONTH(r.date_resa) = :month')
            ->andWhere('YEAR(r.date_resa) = :year')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->getQuery()
            ->getOneOrNullResult();

        return ($result['total'] != null) ? (float)$result['total'] : 0;
    }

    public function findCAByDays()
    {
        $datetime = new \DateTime();
        $aujourdhui = $datetime->format('Y-m-d');

        $result = $this->createQueryBuilder('r')
                        ->select('SUM(r.montant_total) AS total')
                        ->andWhere("DATE_FORMAT(r.date_resa, '%Y-%m-%d') = :aujourdhui")
                        ->setParameter('aujourdhui', $aujourdhui)
                        ->getQuery()
                        ->getOneOrNullResult();
        return ($result['total'] != null) ? (float)$result['total'] : 0;
    }

    public function findCAAll()
    {
        $datetime = new \DateTime();
        $year = $datetime->format('Y');
        $month = $datetime->format('m');

        $result = $this->createQueryBuilder('r')
            ->select('SUM(r.montant_total) AS total')
            ->getQuery()
            ->getOneOrNullResult();

        return ($result['total'] != null) ? (float)$result['total'] : 0;
    }
}
