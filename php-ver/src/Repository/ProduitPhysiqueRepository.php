<?php

namespace App\Repository;

use App\Entity\ProduitPhysique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProduitPhysique|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitPhysique|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitPhysique[]    findAll()
 * @method ProduitPhysique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitPhysiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitPhysique::class);
    }

    // /**
    //  * @return ProduitPhysique[] Returns an array of ProduitPhysique objects
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
    public function findOneBySomeField($value): ?ProduitPhysique
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

     public function soldeProduitPhysiqueById($id)
    {
        $result = $this->createQueryBuilder('p')
                    ->leftjoin('p.produitPhysiqueCodes','ppc')
                    ->select('p.prixAchat * COUNT(ppc.id) AS solde ')
                    ->andWhere('p.id = :id')
                    ->andWhere("ppc.status = 'pending' ")
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->getOneOrNullResult();
        return ($result != null) ? $result['solde'] : 0;
    }
}
