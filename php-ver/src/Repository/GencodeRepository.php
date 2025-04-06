<?php

namespace App\Repository;

use App\Entity\Gencode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Gencode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gencode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gencode[]    findAll()
 * @method Gencode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GencodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gencode::class);
    }

    // /**
    //  * @return Gencode[] Returns an array of Gencode objects
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
    public function findOneBySomeField($value): ?Gencode
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
