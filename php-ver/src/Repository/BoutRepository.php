<?php

namespace App\Repository;

use App\Entity\Bout;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Bout|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bout|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bout[]    findAll()
 * @method Bout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bout::class);
    }

    // /**
    //  * @return Bout[] Returns an array of Bout objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    
    public function findOneByYear($value): ?Bout
    {
        return $this->createQueryBuilder('b')
            ->andWhere('YEAR(b.date_creation) = :year')
            ->setParameter('year', $value)
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
}
