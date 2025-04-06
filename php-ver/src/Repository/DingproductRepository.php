<?php

namespace App\Repository;

use App\Entity\Dingproduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dingproduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dingproduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dingproduct[]    findAll()
 * @method Dingproduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DingproductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dingproduct::class);
    }

    // /**
    //  * @return Dingproduct[] Returns an array of Dingproduct objects
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
    public function findOneBySomeField($value): ?Dingproduct
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
