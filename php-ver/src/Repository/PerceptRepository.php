<?php

namespace App\Repository;

use App\Entity\Percept;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Percept|null find($id, $lockMode = null, $lockVersion = null)
 * @method Percept|null findOneBy(array $criteria, array $orderBy = null)
 * @method Percept[]    findAll()
 * @method Percept[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PerceptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Percept::class);
    }

    // /**
    //  * @return Percept[] Returns an array of Percept objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Percept
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
