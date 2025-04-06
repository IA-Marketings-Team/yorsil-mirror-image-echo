<?php

namespace App\Repository;

use App\Entity\Fraiservice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fraiservice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fraiservice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fraiservice[]    findAll()
 * @method Fraiservice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FraiserviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fraiservice::class);
    }

    // /**
    //  * @return Fraiservice[] Returns an array of Fraiservice objects
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
    public function findOneBySomeField($value): ?Fraiservice
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
