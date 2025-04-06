<?php

namespace App\Repository;

use App\Entity\TauxChange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TauxChange|null find($id, $lockMode = null, $lockVersion = null)
 * @method TauxChange|null findOneBy(array $criteria, array $orderBy = null)
 * @method TauxChange[]    findAll()
 * @method TauxChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TauxChangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TauxChange::class);
    }

    // /**
    //  * @return TauxChange[] Returns an array of TauxChange objects
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
    public function findOneBySomeField($value): ?TauxChange
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function countTauxChangeJours($_id,$_devise)
    {
        $datetime = new \DateTime();
        $aujourdhui = $datetime->format('Y-m-d');
        $compte_journalier = 0;

        $result = $this->createQueryBuilder('t')
                        ->leftJoin('t.pays_change','pays')
                        ->select('COUNT(t.id)')
                        ->where("pays.id = :id")
                        ->andWhere("t.devise = :devise")
                        ->andWhere("DATE_FORMAT(t.date_change, '%Y-%m-%d') = :aujourdhui")
                        ->setParameter('aujourdhui', $aujourdhui)
                        ->setParameter('id', $_id)
                        ->setParameter('devise', $_devise)
                        ->getQuery()
                        ->getSingleScalarResult();
        
        return $result;
    }
}
