<?php

namespace App\Repository;

use App\Entity\Seuilpercepteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Seuilpercepteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seuilpercepteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seuilpercepteur[]    findAll()
 * @method Seuilpercepteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeuilpercepteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seuilpercepteur::class);
    }

    // /**
    //  * @return Seuilpercepteur[] Returns an array of Seuilpercepteur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Seuilpercepteur
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
