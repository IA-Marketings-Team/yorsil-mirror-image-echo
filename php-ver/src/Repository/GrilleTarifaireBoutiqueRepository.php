<?php

namespace App\Repository;

use App\Entity\GrilleTarifaireBoutique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GrilleTarifaireBoutique>
 *
 * @method GrilleTarifaireBoutique|null find($id, $lockMode = null, $lockVersion = null)
 * @method GrilleTarifaireBoutique|null findOneBy(array $criteria, array $orderBy = null)
 * @method GrilleTarifaireBoutique[]    findAll()
 * @method GrilleTarifaireBoutique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrilleTarifaireBoutiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrilleTarifaireBoutique::class);
    }

    public function add(GrilleTarifaireBoutique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GrilleTarifaireBoutique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return GrilleTarifaireBoutique[] Returns an array of GrilleTarifaireBoutique objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GrilleTarifaireBoutique
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
