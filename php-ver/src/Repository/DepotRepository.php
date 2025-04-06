<?php

namespace App\Repository;

use App\Entity\Depot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Depot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Depot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Depot[]    findAll()
 * @method Depot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depot::class);
    }

    // /**
    //  * @return Depot[] Returns an array of Depot objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Depot
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function sumDepot($id)
    {
        $result = $this->createQueryBuilder('d')
                    ->leftjoin('d.percepteur','perc')
                    ->select('SUM(d.montant) AS total ')
                    ->andWhere('perc.id = :id')
                    ->andWhere("d.isvalid = 1")
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->getOneOrNullResult();
        return ($result != null) ? $result['total'] : 0;
    }
}
