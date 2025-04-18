<?php

namespace App\Repository;

use App\Entity\FatouratiCreances;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FatouratiCreances|null find($id, $lockMode = null, $lockVersion = null)
 * @method FatouratiCreances|null findOneBy(array $criteria, array $orderBy = null)
 * @method FatouratiCreances[]    findAll()
 * @method FatouratiCreances[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FatouratiCreancesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FatouratiCreances::class);
    }

    // /**
    //  * @return FatouratiCreances[] Returns an array of FatouratiCreances objects
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
    public function findOneBySomeField($value): ?FatouratiCreances
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
