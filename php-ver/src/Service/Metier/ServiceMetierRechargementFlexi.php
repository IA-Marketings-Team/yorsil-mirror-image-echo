<?php

namespace App\Service\Metier;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceMetierRechargementFlexi{
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

    public function notifRechargement($idRechargement){
        $_rechargement = EntityName::IP_RECHARGEMENT_FLEXI;

        $filtre = ($idRechargement > 0) ? " WHERE rechargeflexi.id > $idRechargement" : '';
        
        $_dql = "SELECT rechargeflexi.id,
                        user.nom
                        FROM $_rechargement rechargeflexi
                        LEFT JOIN rechargeflexi.user as user
                        $filtre
                        ORDER BY rechargeflexi.id DESC";
                        
        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setMaxResults(5);

        return $_query->getResult();
    }

}