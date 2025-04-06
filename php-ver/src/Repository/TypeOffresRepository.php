<?php

namespace App\Repository;

use App\Entity\TypeOffres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeOffres|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeOffres|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeOffres[]    findAll()
 * @method TypeOffres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeOffresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeOffres::class);
    }

    // /**
    //  * @return TypeOffres[] Returns an array of TypeOffres objects
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
    public function findOneBySomeField($value): ?TypeOffres
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
