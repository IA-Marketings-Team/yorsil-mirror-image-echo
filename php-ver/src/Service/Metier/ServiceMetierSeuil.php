<?php

namespace App\Service\Metier;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceMetierSeuil
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

    public function listeSeuil(){
        $_creit = EntityName::IP_SEUIL;

        $_dql = "SELECT seuil FROM $_creit seuil ORDER BY seuil.id DESC";

        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return $_result;
    }

    public function listeSeuils($_page, $_nb_max_page, $_search, $_order_by){
        $_order_by = $_order_by ? $_order_by : "seuil.id DESC";
        $_seuil = EntityName::IP_SEUIL;

        $_dql = "SELECT user.nom as un,
                        bout.nom as bn,
                        seuil.montant,
                        DATE_FORMAT(seuil.date, '%d-%m-%Y') as suilDate,
                        seuil.id
                 FROM $_seuil seuil
                 LEFT JOIN seuil.admin as user
                 LEFT JOIN seuil.bout as bout 
                 
                 WHERE (user.nom LIKE :search
                    OR  bout.nom LIKE :search
                    OR  seuil.montant LIKE :search
                    OR DATE_FORMAT(seuil.date, '%d-%m-%Y') LIKE :search
                 )
                 ORDER BY $_order_by";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%")
            ->setFirstResult($_page)
            ->setMaxResults($_nb_max_page);
            
        return [$_query->getResult(), $this->countCredits($_search)];
    }

    public function countCredits($_search){
        $_seuil = EntityName::IP_SEUIL;

        $_dql = "SELECT COUNT(seuil.id) AS nbTotal
                 FROM $_seuil seuil
                 LEFT JOIN seuil.bout as user
                 LEFT JOIN seuil.bout as bout

                 WHERE (user.nom LIKE :search
                    OR  bout.nom LIKE :search
                    OR  seuil.montant LIKE :search
                    OR  DATE_FORMAT(seuil.date, '%d-%m-%Y') LIKE :search)";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%");

        return $_query->getOneOrNullResult()['nbTotal'];
    }

}