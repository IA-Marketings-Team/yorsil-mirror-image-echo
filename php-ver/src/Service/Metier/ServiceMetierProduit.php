<?php

namespace App\Service\Metier;

use App\Entity\Produit;
use App\Service\Utils\Util;
use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;


class ServiceMetierProduit
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

    public function listeProduits($_page, $_nb_max_page, $_search, $_order_by){
        $_order_by = $_order_by ? $_order_by : "prod.id DESC";
        $_produit = EntityName::IP_PRODUIT;

        $_dql = "SELECT prod.nom,
                        cat.nom as nomCateg,
                        prod.prix_achat,
                        prod.prix_vente,
                        SUM(stock.qte) as qte,
                        prod.id
                 FROM $_produit prod
                 LEFT JOIN prod.categorie cat
                 LEFT JOIN prod.stocks stock
                 WHERE ( prod.nom LIKE :search
                     OR prod.prix_achat LIKE :search
                     OR prod.prix_vente LIKE :search
                     OR cat.nom LIKE :search
                     )
                 GROUP BY prod.id
                 ORDER BY $_order_by";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%")
            ->setFirstResult($_page)
            ->setMaxResults($_nb_max_page);

        return [$_query->getResult(), $this->countProduits($_search)];
    }

    public function countProduits($_search){
        $_produit = EntityName::IP_PRODUIT;

        $_dql = "SELECT COUNT(prod.id) AS nbTotal
                 FROM $_produit prod
                 LEFT JOIN prod.categorie cat
                 WHERE ( prod.nom LIKE :search
                     OR prod.prix_achat LIKE :search
                     OR prod.prix_vente LIKE :search
                     OR cat.nom LIKE :search
                     )
                  ";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%");

        return $_query->getOneOrNullResult()['nbTotal'];
    }

    public function getSlugProduit(Produit $produit, $_last_slug = null)
    {
        $_slug      = Util::slug($produit->getNom());
        $_and_where = $produit->getId() > 0 ? "AND prod.id != " . $produit->getId() : "";
        $_produit   = EntityName::IP_PRODUIT;
        $_dql = "SELECT prod.slug
                 FROM $_produit prod
                 WHERE prod.slug LIKE :prod_slug
                 $_and_where";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql)
            ->setParameter('prod_slug', "$_slug%");
        $_count_result = count($_query->getResult());
        if ($_count_result && $_slug != $_last_slug) $_slug = "$_slug-$_count_result";

        return $_slug;
    }

}
