<?php

namespace App\Controller\BackOffice;

use App\Entity\Categorie;
use App\Form\BackOffice\CategorieType;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{

    public function __construct(
        EntityManagerInterface $_entity_manager,
        CategorieRepository $categorieRepository,
        ProduitRepository $produitRepository
    )
    {
        $this->_entity_manager = $_entity_manager;
        $this->categorie_repository = $categorieRepository;
        $this->produit_repository = $produitRepository;
    }

    /**
     * @Route("/liste-des-categories", name="categorie")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function index(): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('BackOffice/categorie/index.html.twig', [
            'categories' => $this->categorie_repository->findAll(),
        ]);
    }

    /**
     * @Route("/ajout-de-categorie", name="new_categorie")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutCategorie(Request $request)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
            {
                $this->_entity_manager->persist($categorie);
                $this->_entity_manager->flush();
                $this->addFlash(
                    'success',
                    "La catégorie {$categorie->getNom()} a été bien enregistrée"
                );
                return $this->redirectToRoute('categorie');
            }

        return $this->render('BackOffice/categorie/ajout.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/{id}/modification-de-categorie", name="edit_categorie")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function modificationCategorie(Request $request,Categorie $categorie)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
            {
                $this->_entity_manager->persist($categorie);
                $this->_entity_manager->flush();
                $this->addFlash(
                    'success',
                    "La catégorie a été modifié avec succès"
                );
                return $this->redirectToRoute('categorie');
            }

        return $this->render('BackOffice/categorie/modification.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/{id}/suppression-categorie", name="delete_categorie")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function suppressionCategorie(Categorie $categorie)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        if($this->produit_repository->findOneBy(['categorie' => $categorie])){
            $this->addFlash(
                'warning',"Vous ne pouvez pas supprimer la catégorie {$categorie->getNom()} !"
            );
        } else {
            $this->_entity_manager->remove($categorie);
            $this->_entity_manager->flush();

            $this->addFlash(
                'success',
                "La catégorie {$categorie->getNom()} a été supprimée avec succès"
            );
        }
            
        return $this->redirectToRoute('categorie');
    }

}