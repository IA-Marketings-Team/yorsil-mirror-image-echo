<?php

namespace App\Repository;

use App\Entity\GrilleTarifaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GrilleTarifaire>
 *
 * @method GrilleTarifaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method GrilleTarifaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method GrilleTarifaire[]    findAll()
 * @method GrilleTarifaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrilleTarifaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrilleTarifaire::class);
    }

    public function add(GrilleTarifaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GrilleTarifaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return GrilleTarifaire[] Returns an array of GrilleTarifaire objects
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

//    public function findOneBySomeField($value): ?GrilleTarifaire
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
