<?php

namespace App\Repository;

use App\Entity\Credit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Credit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Credit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Credit[]    findAll()
 * @method Credit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreditRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Credit::class);
    }

    // /**
    //  * @return Credit[] Returns an array of Credit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Credit
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findOneByType($value): ?Credit
    {

        return $this->createQueryBuilder('b')
            ->where('b.type = :value')
            ->orderBy('b.id', 'DESC')
            ->setParameter('value', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByMultipleValues($_date_debut,$_date_fin,$_bout_id)
    {
        $qb = $this->createQueryBuilder('c'); 
        return $qb
            ->leftjoin('c.bout','bout')
            ->where('c.type IS NOT NULL')
            ->andWhere('bout.id = :id')
            ->andWhere("c.isvalid = 1")
            ->andWhere("c.date <= :datefin")
            ->andWhere(":datedebut <= c.date") // Vérification de la plage de dates
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('c.isdelete', ':isdelete'),
                    $qb->expr()->isNull('c.isdelete')
                )
            )
            ->orderBy('c.date', 'DESC')
            ->setParameter('id', $_bout_id)
            ->setParameter('datedebut', $_date_debut)
            ->setParameter('datefin', $_date_fin)
            ->setParameter('isdelete', false)
            ->getQuery()
            ->getResult();
    }

    public function sumRechargement($id)
    {
        $result = $this->createQueryBuilder('d')
                    ->leftjoin('d.percept','perc')
                    ->select('SUM(d.montant) AS total')
                    ->andWhere('perc.id = :id')
                    ->andWhere("d.isvalid = 1")
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->getOneOrNullResult();
        return ($result != null) ? $result['total'] : 0;
    }

}
