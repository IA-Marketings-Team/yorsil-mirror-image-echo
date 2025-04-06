<?php

namespace App\Repository;

use App\Entity\Seuil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Seuil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seuil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seuil[]    findAll()
 * @method Seuil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeuilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seuil::class);
    }

    // /**
    //  * @return Seuil[] Returns an array of Seuil objects
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
    public function findOneBySomeField($value): ?Seuil
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
