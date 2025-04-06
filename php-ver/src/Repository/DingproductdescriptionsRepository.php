<?php

namespace App\Repository;

use App\Entity\Dingproductdescriptions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dingproductdescriptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dingproductdescriptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dingproductdescriptions[]    findAll()
 * @method Dingproductdescriptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DingproductdescriptionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dingproductdescriptions::class);
    }

    // /**
    //  * @return Dingproductdescriptions[] Returns an array of Dingproductdescriptions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Dingproductdescriptions
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
