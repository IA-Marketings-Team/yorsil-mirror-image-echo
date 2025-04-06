<?php

namespace App\Service\Metier;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceMetierDepot
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
    
    public function listeDepots($_page, $_nb_max_page, $_search, $_order_by){
        $_order_by = $_order_by ? $_order_by : "depot.id DESC";
        $_depot = EntityName::IP_DEPOT;

        $_dql = "SELECT DATE_FORMAT(depot.date, '%d-%m-%Y') as depotDate,
                        user.nom as userNom,
                        depot.montant,
                        depot.note,
                        CASE 
                            WHEN depot.isvalid IS NULL THEN 'En attente'
                            WHEN depot.isvalid = true THEN 'Validé'
                            WHEN depot.isvalid = false THEN 'Rejeté'
                            ELSE depot.isvalid
                        END AS statut,
                        file.url_fichier,
                        depot.id
                 FROM $_depot depot 
                 LEFT JOIN depot.percepteur as user
                 LEFT JOIN depot.file as file
                 WHERE (user.nom LIKE :search
                    OR  DATE_FORMAT(depot.date, '%d-%m-%Y') LIKE :search
                    OR  depot.note LIKE :search)
                 ORDER BY $_order_by";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%")
            ->setFirstResult($_page)
            ->setMaxResults($_nb_max_page);
            
        return [$_query->getResult(), $this->countDepot($_search)];
    }

    public function countDepot($_search){
        $depot = EntityName::IP_DEPOT;

        $_dql = "SELECT COUNT(depot.id) AS nbTotal
                 FROM $depot depot
                 LEFT JOIN depot.percepteur as user
                 WHERE (user.nom LIKE :search
                    OR  DATE_FORMAT(depot.date, '%d-%m-%Y') LIKE :search
                    OR  depot.note LIKE :search)
                 ";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%");

        return $_query->getOneOrNullResult()['nbTotal'];
    }

}