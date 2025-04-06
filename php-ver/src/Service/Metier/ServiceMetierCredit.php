<?php

namespace App\Service\Metier;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceMetierCredit
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

    public function listeCredit(){
        $_creit = EntityName::IP_CREDIT;

        $_dql = "SELECT credit FROM $_creit credit ORDER BY credit.id DESC";

        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return $_result;
    }

    public function listeCredits($_page, $_nb_max_page, $_search, $_order_by){
        $_order_by = $_order_by ? $_order_by : "credit.id DESC";
        $_credit = EntityName::IP_CREDIT;

        $_dql = "SELECT DATE_FORMAT(credit.date, '%d/%m/%Y') as creditDate,
                        user.nom as un,
                        bout.nom as bn,
                        credit.ref,
                        credit.montant,
                        credit.type,
                        perc.nom as pn,
                        CASE 
                            WHEN credit.isvalid IS NULL THEN 'En attente'
                            WHEN credit.isvalid = true THEN 'Validé'
                            WHEN credit.isvalid = false THEN 'Rejeté'
                            ELSE credit.isvalid
                        END AS statut,
                        file.url_fichier,
                        credit.id
                 FROM $_credit credit 
                 LEFT JOIN credit.bout as bout
                 LEFT JOIN credit.percept as perc
                 LEFT JOIN credit.admin as user
                 LEFT JOIN credit.file as file
                 WHERE (user.nom LIKE :search
                    OR  bout.nom LIKE :search
                    OR  credit.montant LIKE :search
                    OR  credit.ref LIKE :search
                    OR  credit.type LIKE :search
                    OR  perc.nom LIKE :search
                    OR  DATE_FORMAT(credit.date, '%d-%m-%Y') LIKE :search)
                 AND (credit.isdelete = false OR credit.isdelete IS NULL)
                 ORDER BY $_order_by";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%")
            ->setFirstResult($_page)
            ->setMaxResults($_nb_max_page);
            
        return [$_query->getResult(), $this->countCredits($_search)];
    }

    public function countCredits($_search){
        $_credit = EntityName::IP_CREDIT;

        $_dql = "SELECT COUNT(credit.id) AS nbTotal
                 FROM $_credit credit
                 LEFT JOIN credit.bout as bout
                 LEFT JOIN credit.percept as perc
                 LEFT JOIN credit.admin as user
                 WHERE (user.nom LIKE :search
                    OR  bout.nom LIKE :search
                    OR  credit.montant LIKE :search
                    OR  perc.nom LIKE :search
                    OR  DATE_FORMAT(credit.date, '%d-%m-%Y') LIKE :search)
                 AND (credit.isdelete = false OR credit.isdelete IS NULL)";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%");

        return $_query->getOneOrNullResult()['nbTotal'];
    }

}