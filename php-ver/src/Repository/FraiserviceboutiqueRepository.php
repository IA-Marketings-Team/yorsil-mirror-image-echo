<?php

namespace App\Repository;

use App\Entity\Fraiserviceboutique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fraiserviceboutique|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fraiserviceboutique|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fraiserviceboutique[]    findAll()
 * @method Fraiserviceboutique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FraiserviceboutiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fraiserviceboutique::class);
    }

    // /**
    //  * @return Fraiserviceboutique[] Returns an array of Fraiserviceboutique objects
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
    public function findOneBySomeField($value): ?Fraiserviceboutique
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
