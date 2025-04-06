<?php

namespace App\Service\Metier;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceMetierGeste
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
    
    public function listeGestes($_page, $_nb_max_page, $_search, $_order_by){
        $_order_by = $_order_by ? $_order_by : "geste.id DESC";
        $_geste = EntityName::IP_GESTE;

        $_dql = "SELECT DATE_FORMAT(geste.date, '%d-%m-%Y') as gesteDate,
                        user.nom as un,
                        bout.nom as bn,
                        geste.montant,
                        geste.motif
                 FROM $_geste geste 
                 LEFT JOIN geste.bout as bout
                 LEFT JOIN geste.admin as user

                 WHERE (user.nom LIKE :search
                    OR  bout.nom LIKE :search
                    OR  geste.montant LIKE :search
                    OR  DATE_FORMAT(geste.date, '%d-%m-%Y') LIKE :search
                    OR  geste.motif LIKE :search)
                 ORDER BY $_order_by";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%")
            ->setFirstResult($_page)
            ->setMaxResults($_nb_max_page);
            
        return [$_query->getResult(), $this->countGeste($_search)];
    }

    public function countGeste($_search){
        $geste = EntityName::IP_GESTE;

        $_dql = "SELECT COUNT(geste.id) AS nbTotal
                 FROM $geste geste
                 LEFT JOIN geste.bout as bout
                 LEFT JOIN geste.admin as user
                 WHERE (user.nom LIKE :search
                    OR  bout.nom LIKE :search
                    OR  geste.montant LIKE :search
                    OR  DATE_FORMAT(geste.date, '%d-%m-%Y') LIKE :search
                    OR  geste.motif LIKE :search)";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%");

        return $_query->getOneOrNullResult()['nbTotal'];
    }

}