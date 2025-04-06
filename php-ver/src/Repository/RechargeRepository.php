<?php

namespace App\Repository;

use App\Entity\Recharge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recharge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recharge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recharge[]    findAll()
 * @method Recharge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RechargeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recharge::class);
    }

    // /**
    //  * @return Recharge[] Returns an array of Recharge objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Recharge
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function countSoldRechargeByMonth()
    {
        $datetime = new \DateTime();
        $year = $datetime->format('Y');
        $month = $datetime->format('m');

        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.processState = :state')
            ->andWhere('MONTH(r.saleDate) = :month')
            ->andWhere('YEAR(r.saleDate) = :year')
            ->setParameter('state', 'SOLD')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countSoldRechargeByMonthBefore()
    {
        $datetime = new \DateTime('now -1 month');
        $year = $datetime->format('Y');
        $month = $datetime->format('m');

        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.processState = :state')
            ->andWhere('MONTH(r.saleDate) = :month')
            ->andWhere('YEAR(r.saleDate) = :year')
            ->setParameter('state', 'SOLD')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countSoldRechargeByYear()
    {
        $datetime = new \DateTime();
        $year = $datetime->format('Y');

        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.processState = :state')
            ->andWhere('YEAR(r.saleDate) = :year')
            ->setParameter('state', 'SOLD')
            ->setParameter('year', $year)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function countSoldRechargeDays()
    {
        $datetime = new \DateTime();
        $aujourdhui = $datetime->format('Y-m-d');
        $compte_journalier = 0;

        $result = $this->createQueryBuilder('r')
                        ->select('COUNT(r.id)')
                        ->where('r.processState = :state')
                        ->andWhere("DATE_FORMAT(r.saleDate, '%Y-%m-%d') = :aujourdhui")
                        ->setParameter('state', 'SOLD')
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
                        ->andWhere("DATE_FORMAT(f.saleDate, '%Y-%m-%d') = :aujourdhui")
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
                        ->andWhere('MONTH(f.saleDate) = :month')
                        ->andWhere('YEAR(f.saleDate) = :year')
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
                    ->andWhere('MONTH(f.saleDate) = :month')
                    ->andWhere('YEAR(f.saleDate) = :year')
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
                        ->andWhere('YEAR(f.saleDate) = :year')
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
            ->andWhere("c.saleDate <= :datefin")
            ->andWhere(":datedebut <= c.saleDate") // Vérification de la plage de dates
            ->orderBy('c.saleDate', 'DESC')
            ->setParameter('id', $_bout_id)
            ->setParameter('datedebut', $_date_debut)
            ->setParameter('datefin', $_date_fin)
            ->getQuery()
            ->getResult();
    }

    public function findCAByDays()
    {
        $datetime = new \DateTime();
        $aujourdhui = $datetime->format('Y-m-d');
        $compte_journalier = 0;

        $result = $this->createQueryBuilder('r')
                        ->select('SUM(r.montant) AS total')
                        ->andWhere("DATE_FORMAT(r.saleDate, '%Y-%m-%d') = :aujourdhui")
                        ->setParameter('aujourdhui', $aujourdhui)
                        ->getQuery()
                        ->getOneOrNullResult();
        
         return ($result['total'] != null) ? (float)$result['total'] : 0;
    }

    public function findCAByMonth()
    {
        $datetime = new \DateTime();
        $year = $datetime->format('Y');
        $month = $datetime->format('m');

        $result = $this->createQueryBuilder('r')
            ->select('SUM(r.montant) AS total')
            ->andWhere('MONTH(r.saleDate) = :month')
            ->andWhere('YEAR(r.saleDate) = :year')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
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
            ->select('SUM(r.montant) AS total')
            ->getQuery()
            ->getOneOrNullResult();

         return ($result['total'] != null) ? (float)$result['total'] : 0;
    }
}
