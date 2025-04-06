<?php

namespace App\Repository;

use App\Entity\SeuilBilleterie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SeuilBilleterie|null find($id, $lockMode = null, $lockVersion = null)
 * @method SeuilBilleterie|null findOneBy(array $criteria, array $orderBy = null)
 * @method SeuilBilleterie[]    findAll()
 * @method SeuilBilleterie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeuilBilleterieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SeuilBilleterie::class);
    }

    // /**
    //  * @return SeuilBilleterie[] Returns an array of SeuilBilleterie objects
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
    public function findOneBySomeField($value): ?SeuilBilleterie
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
