<?php

namespace App\Repository;

use App\Entity\FatouratiPaiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FatouratiPaiement|null find($id, $lockMode = null, $lockVersion = null)
 * @method FatouratiPaiement|null findOneBy(array $criteria, array $orderBy = null)
 * @method FatouratiPaiement[]    findAll()
 * @method FatouratiPaiement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FatouratiPaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FatouratiPaiement::class);
    }

    // /**
    //  * @return FatouratiPaiement[] Returns an array of FatouratiPaiement objects
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
    public function findOneBySomeField($value): ?FatouratiPaiement
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
