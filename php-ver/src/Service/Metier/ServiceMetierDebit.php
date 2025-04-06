<?php

namespace App\Service\Metier;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceMetierDebit
{
    private $_entity_manager;
    private $_container;

    /**
     * UserManager constructor.
     * @param EntityManagerInterface $_entity_manager
     * @param ContainerInterface $_container
     */
    public function __construct(
        EntityManagerInterface $_entity_manager,
        ContainerInterface $_container
    )
    {
        $this->_entity_manager = $_entity_manager;
        $this->_container      = $_container;
    }
    
    public function listeDebits($_page, $_nb_max_page, $_search, $_order_by){
        $_order_by = $_order_by ? $_order_by : "debit.id DESC";
        $_debit = EntityName::IP_DEBIT;

        $_dql = "SELECT user.nom as userNom,
                        bout.nom as boutNom,
                        debit.montant,
                        DATE_FORMAT(debit.date, '%d-%m-%Y') as debitDate,
                        debit.note,
                        debit.id
                 FROM $_debit debit 
                 LEFT JOIN debit.bout as bout
                 LEFT JOIN debit.admin as user
                 WHERE (user.nom LIKE :search
                    OR  bout.nom LIKE :search
                    OR  DATE_FORMAT(debit.date, '%d-%m-%Y') LIKE :search
                    OR  debit.note LIKE :search)
                 ORDER BY $_order_by";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%")
            ->setFirstResult($_page)
            ->setMaxResults($_nb_max_page);
            
        return [$_query->getResult(), $this->countDebit($_search)];
    }

    public function countDebit($_search){
        $debit = EntityName::IP_DEBIT;

        $_dql = "SELECT COUNT(debit.id) AS nbTotal
                 FROM $debit debit
                 LEFT JOIN debit.bout as bout
                 LEFT JOIN debit.admin as user
                 WHERE (user.nom LIKE :search
                    OR  bout.nom LIKE :search
                    OR  DATE_FORMAT(debit.date, '%d-%m-%Y') LIKE :search
                    OR  debit.note LIKE :search)
                 ";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%");

        return $_query->getOneOrNullResult()['nbTotal'];
    }

}