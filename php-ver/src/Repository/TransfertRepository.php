<?php

namespace App\Repository;

use App\Entity\Transfert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transfert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transfert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transfert[]    findAll()
 * @method Transfert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransfertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transfert::class);
    }

    // /**
    //  * @return Transfert[] Returns an array of Transfert objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transfert
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function countTransfertByMonth()
    {
        $datetime = new \DateTime();
        $year = $datetime->format('Y');
        $month = $datetime->format('m');

        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('MONTH(r.date) = :month')
            ->andWhere('YEAR(r.date) = :year')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countTransfertByMonthBefore()
    {
        $datetime = new \DateTime('now -1 month');
        $year = $datetime->format('Y');
        $month = $datetime->format('m');

        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('MONTH(r.date) = :month')
            ->andWhere('YEAR(r.date) = :year')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countTransfertByYear()
    {
        $datetime = new \DateTime();
        $year = $datetime->format('Y');

        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('YEAR(r.date) = :year')
            ->setParameter('year', $year)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function countTransfertDays()
    {
        $datetime = new \DateTime();
        $aujourdhui = $datetime->format('Y-m-d');
        $compte_journalier = 0;

        $result = $this->createQueryBuilder('r')
                        ->select('COUNT(r.id)')
                        ->andWhere("DATE_FORMAT(r.date, '%Y-%m-%d') = :aujourdhui")
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
                        ->leftjoin('f.bout','bout')
                        ->andWhere("DATE_FORMAT(f.date, '%Y-%m-%d') = :aujourdhui")
                        ->andWhere('bout.id = :id')
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
                        ->leftjoin('f.bout','bout')
                        ->andWhere('MONTH(f.date) = :month')
                        ->andWhere('YEAR(f.date) = :year')
                        ->andWhere('bout.id = :id')
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
                    ->leftJoin('f.bout', 'bout')
                    ->andWhere('MONTH(f.date) = :month')
                    ->andWhere('YEAR(f.date) = :year')
                    ->andWhere('bout.id = :id')
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
                        ->leftjoin('f.bout','bout')
                        ->andWhere('YEAR(f.date) = :year')
                        ->andWhere('bout.id = :id')
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
            ->leftjoin('c.bout','bout')
            ->andWhere('bout.id = :id')
            ->andWhere("c.date <= :datefin")
            ->andWhere(":datedebut <= c.date") // Vérification de la plage de dates
            ->orderBy('c.date', 'DESC')
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
                        ->andWhere("DATE_FORMAT(r.date, '%Y-%m-%d') = :aujourdhui")
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
            ->andWhere('MONTH(r.date) = :month')
            ->andWhere('YEAR(r.date) = :year')
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
