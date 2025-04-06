<?php

namespace App\Service\Metier;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use App\Repository\BoutRepository;
use App\Entity\User;

class ServiceMetierBoutique
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
        ContainerInterface $_container,
        BoutRepository $_bout_repository
    )
    {
        $this->_entity_manager  = $_entity_manager;
        $this->_container       = $_container;
        $this->_bout_repository = $_bout_repository;
    }

    public function listeBoutiques($_page, $_nb_max_page, $_search, $_order_by){
        $_order_by = $_order_by ? $_order_by : "bout.id DESC";
        $_boutique = EntityName::IP_BOUTIQUE;
        $_credit   = EntityName::IP_CREDIT;
        $_debit    = EntityName::IP_DEBIT;
        $_geste    = EntityName::IP_GESTE;
        $_seuil    = EntityName::IP_SEUIL;
        $_seuil_b  = EntityName::IP_SEUIL_BILLETERIE;
        //-- (SELECT SUM(seuil.montant) FROM $_seuil seuil LEFT JOIN seuil.bout as boutique WHERE boutique.id = bout.id) AS montant_seuil,
                        
        $_dql = "SELECT bout.code,
                        bout.nom,
                        bout.nRcs,
                        bout.numMobile,
                        bout.email,
                        bout.adresse,
                        bout.codePost,
                        (SELECT SUM(credit.montant) FROM $_credit credit LEFT JOIN credit.bout as bouti WHERE bouti.id = bout.id) AS montant,
                        (SELECT SUM(debit.montant) FROM $_debit debit LEFT JOIN debit.bout as bouts WHERE bouts.id = bout.id) AS montant_debit,
                        (SELECT SUM(geste.montant) FROM $_geste geste LEFT JOIN geste.bout as boutiq WHERE boutiq.id = bout.id) AS montant_geste,
                        bout.id as solde,
                        (SELECT seuilb.montant FROM $_seuil_b seuilb WHERE seuilb.bout = bout AND seuilb.id = (SELECT MAX(s2.id) FROM $_seuil_b s2 WHERE s2.bout = bout)) AS montant_seuil,
                        user.createur,
                        bout.is_active,
                        bout.id
                 FROM $_boutique bout 
                 LEFT JOIN bout.user as user
                 WHERE ( bout.code LIKE :search
                     OR bout.nom LIKE :search
                     OR bout.nRcs LIKE :search
                     OR bout.numMobile LIKE :search
                     OR user.createur LIKE :search
                     OR bout.email LIKE :search
                     OR bout.adresse LIKE :search
                     OR bout.codePost LIKE :search)
                 GROUP BY bout.id
                 ORDER BY bout.is_active DESC, $_order_by";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%")
            ->setFirstResult($_page)
            ->setMaxResults($_nb_max_page);
            
        return [$_query->getResult(), $this->countBoutiques($_search)];
    }

    public function countBoutiques($_search){
        $_boutique = EntityName::IP_BOUTIQUE;

        $_dql = "SELECT COUNT(bout.id) AS nbTotal
                 FROM $_boutique bout
                 LEFT JOIN bout.user as user
                 WHERE ( bout.code LIKE :search
                     OR bout.nom LIKE :search
                     OR bout.nRcs LIKE :search
                     OR bout.numMobile LIKE :search
                     OR user.createur LIKE :search
                     OR bout.email LIKE :search
                     OR bout.adresse LIKE :search
                     OR bout.codePost LIKE :search)
                 ";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%");

        return $_query->getOneOrNullResult()['nbTotal'];
    }

    public function creditBoutique(User $user){
        $_boutique_user = $this->_bout_repository->findOneBy(['user' => $user]);
        $id = $_boutique_user->getId();
        $_boutique = EntityName::IP_BOUTIQUE;

        $_dql = "SELECT
                    SUM(cred.montant) as montant
                 FROM $_boutique bout 
                 LEFT JOIN bout.user as user
                 LEFT JOIN bout.credits as cred
                 WHERE bout.id = $id
                 AND cred.isvalid = 1
                 AND (cred.isdelete = false OR cred.isdelete IS NULL)
                 GROUP BY bout.id";

        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return ($_result) ? $_result[0]['montant'] : 0;
    }

    public function debitBoutique(User $user){
        $_boutique_user = $this->_bout_repository->findOneBy(['user' => $user]);
        $id = $_boutique_user->getId();
        $_boutique = EntityName::IP_BOUTIQUE;

        $_dql = "SELECT
                    SUM(deb.montant) as montant
                 FROM $_boutique bout 
                 LEFT JOIN bout.user as user
                 LEFT JOIN bout.debits as deb
                 WHERE bout.id = $id
                 GROUP BY bout.id";

        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return ($_result) ? $_result[0]['montant'] : 0;
    }

    public function gesteBoutique(User $user){
        $_boutique_user = $this->_bout_repository->findOneBy(['user' => $user]);
        $id = $_boutique_user->getId();
        $_boutique = EntityName::IP_BOUTIQUE;

        $_dql = "SELECT
                    SUM(geste.montant) as montant
                 FROM $_boutique bout 
                 LEFT JOIN bout.user as user
                 LEFT JOIN bout.gestecommercials as geste
                 WHERE bout.id = $id
                 GROUP BY bout.id";

        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return ($_result) ? $_result[0]['montant'] : 0;
    }

    public function seuilBoutique(User $user){
        $_boutique_user = $this->_bout_repository->findOneBy(['user' => $user]);
        $id = $_boutique_user->getId();
        $_boutique = EntityName::IP_BOUTIQUE;

        $_dql = "SELECT
                    SUM(seuil.montant) as montant
                 FROM $_boutique bout 
                 LEFT JOIN bout.user as user
                 LEFT JOIN bout.seuils as seuil
                 WHERE bout.id = $id
                 GROUP BY bout.id";

        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return ($_result[0]['montant']) ? $_result[0]['montant'] : 0;
    }

    public function creditBoutiqueDater(User $user,String $_date){
        $_boutique_user = $this->_bout_repository->findOneBy(['user' => $user]);
        $id = $_boutique_user->getId();
        $_boutique = EntityName::IP_BOUTIQUE;

        $_dql = "SELECT
                    SUM(cred.montant) as montant
                 FROM $_boutique bout 
                 LEFT JOIN bout.user as user
                 LEFT JOIN bout.credits as cred
                 WHERE bout.id = $id
                 AND cred.date <= '$_date'
                 AND cred.isvalid = 1
                 AND (cred.isdelete = false OR cred.isdelete IS NULL)
                 GROUP BY bout.id";
 
        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return ($_result) ? $_result[0]['montant'] : 0;
    }

    public function debitBoutiqueDater(User $user,String $_date){
        $_boutique_user = $this->_bout_repository->findOneBy(['user' => $user]);
        $id = $_boutique_user->getId();
        $_boutique = EntityName::IP_BOUTIQUE;

        $_dql = "SELECT
                    SUM(deb.montant) as montant
                 FROM $_boutique bout 
                 LEFT JOIN bout.user as user
                 LEFT JOIN bout.debits as deb
                 WHERE bout.id = $id
                 AND deb.date <= '$_date'
                 GROUP BY bout.id";

        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return ($_result) ? $_result[0]['montant'] : 0;
    }

    public function gesteBoutiqueDater(User $user,String $_date){
        $_boutique_user = $this->_bout_repository->findOneBy(['user' => $user]);
        $id = $_boutique_user->getId();
        $_boutique = EntityName::IP_BOUTIQUE;

        $_dql = "SELECT
                    SUM(geste.montant) as montant
                 FROM $_boutique bout 
                 LEFT JOIN bout.user as user
                 LEFT JOIN bout.gestecommercials as geste
                 WHERE bout.id = $id
                 AND geste.date <= '$_date' 
                 GROUP BY bout.id";

        $_query  = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_result = $_query->getResult();

        return ($_result) ? $_result[0]['montant'] : 0;
    }

}