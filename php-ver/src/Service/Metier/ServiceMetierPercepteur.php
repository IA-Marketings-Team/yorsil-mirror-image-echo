<?php

namespace App\Service\Metier;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceMetierPercepteur
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

    public function listePercepteur(){
        $_percepteur = EntityName::IP_PERCEPT;

        $_dql = "SELECT perc FROM $_percepteur perc ORDER BY perc.id DESC";

        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return $_result;
    }

    public function listePercepteurs($_page, $_nb_max_page, $_search, $_order_by){
        $_order_by = $_order_by ? $_order_by : "perc.id DESC";
        $_percepteur = EntityName::IP_PERCEPT;
        $_credit     = EntityName::IP_CREDIT;
        $_depot      = EntityName::IP_DEPOT;
        $_seuil      = EntityName::IP_SEUIL_PERCEPTEUR;

        $_dql = "SELECT perc.nom,
                        perc.prenom,
                        perc.tele,
                        perc.ville,
                        perc.pays,
                        (SELECT SUM(credit.montant) FROM $_credit credit LEFT JOIN credit.percept as percept WHERE percept.id = perc.id AND credit.isvalid = true) AS montant_rech,
                        (SELECT SUM(depot.montant) FROM $_depot depot LEFT JOIN depot.percepteur as percepts WHERE percepts.id = perc.id AND depot.isvalid = true) AS montant_dep,
                        perc.id as solde,
                        (SELECT SUM(seuil.montant) FROM $_seuil seuil LEFT JOIN seuil.percepteur as perceptss WHERE perceptss.id = perc.id ) AS seuils,
                        perc.id
                 FROM $_percepteur perc 
                 WHERE (perc.nom LIKE :search
                    OR  perc.prenom LIKE :search
                    OR  perc.tele LIKE :search
                    OR  perc.ville LIKE :search
                    OR  perc.pays LIKE :search)
                 ORDER BY $_order_by";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%")
            ->setFirstResult($_page)
            ->setMaxResults($_nb_max_page);
            
        return [$_query->getResult(), $this->countPercepteurs($_search)];
    }

    public function countPercepteurs($_search){
        $percepteur = EntityName::IP_PERCEPT;

        $_dql = "SELECT COUNT(perc.id) AS nbTotal
                 FROM $percepteur perc
                 WHERE (perc.nom LIKE :search
                    OR  perc.prenom LIKE :search
                    OR  perc.tele LIKE :search
                    OR  perc.ville LIKE :search
                    OR  perc.pays LIKE :search)
                 ";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%");

        return $_query->getOneOrNullResult()['nbTotal'];
    }

}