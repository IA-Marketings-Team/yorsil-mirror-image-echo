<?php

namespace App\Repository;

use App\Entity\Pays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pays|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pays|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pays[]    findAll()
 * @method Pays[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pays::class);
    }

    // /**
    //  * @return Pays[] Returns an array of Pays objects
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
    public function findOneBySomeField($value): ?Pays
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByNameLike(string $name): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where($qb->expr()->like('p.nom', ':name'))
            ->setParameter('name', '%' . $name . '%');

        return $qb->getQuery()->getResult();
    }

    public function findByApi(string $api,string $name): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.type_api = :val')
            ->orWhere('p.type_api IS NULL')
            ->andWhere('p.nom LIKE :name')
            ->setParameter('val', $api)
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('p.type_api', 'DESC')  // Met les valeurs non null en haut
            ->addOrderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
        ;
    }
}
