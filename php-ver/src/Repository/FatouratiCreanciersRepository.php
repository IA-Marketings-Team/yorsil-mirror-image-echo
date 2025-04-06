<?php

namespace App\Repository;

use App\Entity\FatouratiCreanciers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FatouratiCreanciers|null find($id, $lockMode = null, $lockVersion = null)
 * @method FatouratiCreanciers|null findOneBy(array $criteria, array $orderBy = null)
 * @method FatouratiCreanciers[]    findAll()
 * @method FatouratiCreanciers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FatouratiCreanciersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FatouratiCreanciers::class);
    }

    // /**
    //  * @return FatouratiCreanciers[] Returns an array of FatouratiCreanciers objects
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
    public function findOneBySomeField($value): ?FatouratiCreanciers
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
