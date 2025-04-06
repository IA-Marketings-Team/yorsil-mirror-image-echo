<?php

namespace App\Repository;

use App\Entity\SoldeService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SoldeService|null find($id, $lockMode = null, $lockVersion = null)
 * @method SoldeService|null findOneBy(array $criteria, array $orderBy = null)
 * @method SoldeService[]    findAll()
 * @method SoldeService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SoldeServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SoldeService::class);
    }

    // /**
    //  * @return SoldeService[] Returns an array of SoldeService objects
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
    public function findOneBySomeField($value): ?SoldeService
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
