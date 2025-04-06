<?php

namespace App\Service\Metier;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceMetierSaisie
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

    public function listReservation(){
        $_saisie = EntityName::IP_SAISIE;

        $_dql = "SELECT sai FROM $_saisie sai
                 WHERE sai.isDelete IS NULL
                 ORDER BY sai.dateEnvoiReserv DESC";

        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return $_result;
    }

    public function listReservationAgent($_id){
        $_saisie = EntityName::IP_SAISIE;

        $_dql = "SELECT sai FROM $_saisie sai
                 LEFT JOIN sai.agent ag
                 WHERE sai.isDelete IS NULL
                 AND ag.id = $_id
                 ORDER BY sai.dateEnvoiReserv DESC";

        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return $_result;
    }

    public function soldeAdmin(){
        $_saisie = EntityName::IP_SAISIE;

        $_dql = "SELECT SUM(sai.montRec) as solde FROM $_saisie sai
                 WHERE sai.isDelete IS NULL
                 ORDER BY sai.dateEnvoiReserv DESC";

        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return $_result ? $_result[0]['solde'] : 0;
    }

    public function nbreBilletJour(){
        $_saisie = EntityName::IP_SAISIE;
        $datetime   = new \DateTime();
        $aujourdhui = $datetime->format('Y-m-d');

        $_dql = "SELECT count(sai.id) as nbBillet FROM $_saisie sai
                 LEFT JOIN sai.agent ag
                 WHERE sai.isDelete IS NULL
                 AND DATE_FORMAT(sai.dateEnvoiReserv, '%Y-%m-%d') LIKE '%$aujourdhui%'";

        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return $_result[0]['nbBillet'];
    }

    public function nbreBilletMois(){
        $_saisie = EntityName::IP_SAISIE;
        $datetime   = new \DateTime();
        $aujourdhui = $datetime->format('Y-m');

        $_dql = "SELECT count(sai.id) as nbBillet FROM $_saisie sai
                 LEFT JOIN sai.agent ag
                 WHERE sai.isDelete IS NULL
                 AND DATE_FORMAT(sai.dateEnvoiReserv, '%Y-%m') LIKE '%$aujourdhui%'";

        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return $_result[0]['nbBillet'];
    }

}