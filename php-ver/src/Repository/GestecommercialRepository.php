<?php

namespace App\Repository;

use App\Entity\Gestecommercial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Gestecommercial|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gestecommercial|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gestecommercial[]    findAll()
 * @method Gestecommercial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GestecommercialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gestecommercial::class);
    }

    // /**
    //  * @return Gestecommercial[] Returns an array of Gestecommercial objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Gestecommercial
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
