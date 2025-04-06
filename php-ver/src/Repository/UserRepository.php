<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAgent()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_AGENT%')
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    } 

    public function findAdmin()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    } 

    public function findSuperAdmin()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_SUPER_ADMIN%')
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    } 

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function suivieAgent()
    {
        $result = $this->createQueryBuilder('u')
                    ->leftjoin('u.saiReservs','saiReservs')
                    ->select('u.id,u.nom,u.numCom,SUM(saiReservs.montRec) as solde')
                    ->andWhere('u.roles LIKE :role')
                    ->setParameter('role', '%ROLE_AGENT%')
                    ->groupby('u.id',)
                    ->getQuery()
                    ->getResult();
        return $result;
    }

    public function debitAgent()
    {
        $result = $this->createQueryBuilder('u')
                    ->leftjoin('u.debits','debits')
                    ->select('u.id,u.nom,u.numCom,SUM(debits.montDebit) as debit')
                    ->andWhere('u.roles LIKE :role')
                    ->setParameter('role', '%ROLE_AGENT%')
                    ->groupby('u.id',)
                    ->getQuery()
                    ->getResult();
        return $result;
    }

    public function findUser()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_USER%')
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    } 
}
