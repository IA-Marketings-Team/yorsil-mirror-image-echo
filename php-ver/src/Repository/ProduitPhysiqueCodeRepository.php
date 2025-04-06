<?php

namespace App\Repository;

use App\Entity\ProduitPhysiqueCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProduitPhysiqueCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitPhysiqueCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitPhysiqueCode[]    findAll()
 * @method ProduitPhysiqueCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitPhysiqueCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitPhysiqueCode::class);
    }

    // /**
    //  * @return ProduitPhysiqueCode[] Returns an array of ProduitPhysiqueCode objects
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
    public function findOneBySomeField($value): ?ProduitPhysiqueCode
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
