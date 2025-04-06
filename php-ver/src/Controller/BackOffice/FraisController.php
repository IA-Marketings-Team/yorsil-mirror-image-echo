<?php

namespace App\Controller\BackOffice;

use App\Entity\Fraiservice;
use App\Entity\Fraiserviceboutique;
use App\Form\BackOffice\FraiserviceType;
use App\Form\BackOffice\FraiserviceboutiqueType;
use App\Repository\FraiserviceRepository;
use App\Repository\FraiserviceboutiqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FraisController extends AbstractController
{

    public function __construct(
        EntityManagerInterface $_entity_manager,
        FraiserviceRepository $fraiserviceRepository,
        FraiserviceboutiqueRepository $fraiserviceboutiqueRepository
    )
    {
        $this->_entity_manager = $_entity_manager;
        $this->_frais_repository = $fraiserviceRepository;
        $this->_frais_boutique_repository = $fraiserviceboutiqueRepository;
    }

    /**
     * @Route("/liste-des-frais-de-services", name="frais_services")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function index(): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('BackOffice/frais/index.html.twig', [
            'frais_services' => $this->_frais_repository->findAll(),
        ]);
    }

     /**
     * @Route("/{id}/modification-frais-de-service", name="edit_frais_service")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function modificationFraiservice(Request $request,Fraiservice $frais)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(FraiserviceType::class, $frais);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
            {
                $frais->setDate(new \Datetime());
                $this->_entity_manager->persist($frais);
                $this->_entity_manager->flush();
                $this->addFlash(
                    'success',
                    "Le frais de service a été modifié avec succès"
                );
                return $this->redirectToRoute('frais_services');
            }

        return $this->render('BackOffice/frais/modification.html.twig',[
            'form'=>$form->createView(),
            'frais_service' => $frais
        ]);
    }

    /**
     * @Route("/liste-des-frais-de-services-personnalises", name="frais_services_perso")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function fraisDeServicePerso(): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('BackOffice/frais/fraisboutique/index.html.twig', [
            'frais_services' => $this->_frais_boutique_repository->findAll(),
        ]);
    }

    /**
     * @Route("/ajout-de-frais-de-service-personalise", name="new_frais_service")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutFraisDeService(Request $request)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        $frais = new Fraiserviceboutique();
        $form = $this->createForm(FraiserviceboutiqueType::class, $frais);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
            {
                $frais->setDate(new \Datetime());
                $this->_entity_manager->persist($frais);
                $this->_entity_manager->flush();
                $this->addFlash(
                    'success',
                    "Le frais de service a été ajouté avec succès"
                );
                return $this->redirectToRoute('frais_services_perso');
            }

        return $this->render('BackOffice/frais/fraisboutique/ajout.html.twig',[
            'form'=>$form->createView()
        ]);
    }

     /**
     * @Route("/{id}/modification-frais-de-service-personnalise", name="edit_frais_service_perso")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function modificationFraiservicePerso(Request $request,Fraiserviceboutique $frais)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(FraiserviceboutiqueType::class, $frais);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
            {
                $frais->setDate(new \Datetime());
                $this->_entity_manager->persist($frais);
                $this->_entity_manager->flush();
                $this->addFlash(
                    'success',
                    "Le frais de service a été modifié avec succès"
                );
                return $this->redirectToRoute('frais_services_perso');
            }

        return $this->render('BackOffice/frais/fraisboutique/modification.html.twig',[
            'form'=>$form->createView(),
            'frais_service' => $frais
        ]);
    }

}