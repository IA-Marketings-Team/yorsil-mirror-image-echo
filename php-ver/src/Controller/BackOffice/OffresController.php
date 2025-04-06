<?php

namespace App\Controller\BackOffice;

use App\Entity\Offres;
use App\Entity\TypeOffres;
use App\Form\BackOffice\OffresType;
use App\Form\BackOffice\TypeOffresType;
use App\Repository\TypeOffresRepository;
use App\Repository\OffresRepository;
use App\Service\UploadFileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OffresController extends AbstractController
{

	public function __construct(
        EntityManagerInterface $_entity_manager,
        TypeOffresRepository $typeOffresRepository,
        OffresRepository $offresRepository,
        UploadFileService $uploadFileService
    )
    {
        $this->_entity_manager = $_entity_manager;
        $this->_uploadFileService = $uploadFileService;
        $this->_type_offres_repository = $typeOffresRepository;
        $this->_offres_repository = $offresRepository;
    }

    /**
     * @Route("/offres", name="offres")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function index(): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_offres = $this->_offres_repository->findAll();

        return $this->render('BackOffice/operateurs/offres/index.html.twig', [
            'offres' => $_offres
        ]);
    }

    /**
     * @Route("/ajout-offre", name="new_offre")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutOffre(Request $request): Response
    {   
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $typeOffres = $this->_type_offres_repository->findAll();

        $_offres = new Offres();
        $form = $this->createForm(OffresType::class, $_offres);
        $form->handleRequest($request);

         if($form->isSubmitted() && $form->isValid()){
            
            $_type_offres = $this->_type_offres_repository->findOneBy(['id' => $request->request->get('type_offres')]);
            $_offres->setTypeOffres($_type_offres);
            $this->_entity_manager->persist($_offres);
            $this->_entity_manager->flush();
            
            $this->addFlash(
                'success',
                "Insertion effectuée avec succès"
            );
            return $this->redirectToRoute('offres');
        }
        

        return $this->render('BackOffice/operateurs/offres/ajout.html.twig', [
            'type_offres' => $typeOffres,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier-offre/{id}", name="edit_offre")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function modifierOffre(Request $request, Offres $_offres): Response
    {   
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $typeOffres = $this->_type_offres_repository->findAll();

        $form = $this->createForm(OffresType::class, $_offres);
        $form->handleRequest($request);

         if($form->isSubmitted() && $form->isValid()){
            
            $_type_offres = $this->_type_offres_repository->findOneBy(['id' => $request->request->get('type_offres')]);
            $_offres->setTypeOffres($_type_offres);
            $this->_entity_manager->persist($_offres);
            $this->_entity_manager->flush();
            
            $this->addFlash(
                'success',
                "Modification effectuée avec succès"
            );
            return $this->redirectToRoute('offres');
        }

        return $this->render('BackOffice/operateurs/offres/modification.html.twig', [
            'type_offres' => $typeOffres,
            'offres' => $_offres,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/type-offres", name="type_offres")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function typeOffres(): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_type_offres = $this->_type_offres_repository->findAll();

        return $this->render('BackOffice/operateurs/type_offres/index.html.twig', [
            'type_offres' => $_type_offres
        ]);
    }

	/**
     * @Route("/ajout-type-offres", name="new_type_offres")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutTypeOffres(Request $request): Response
    {   
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_type_offres = new TypeOffres();
        $form = $this->createForm(TypeOffresType::class, $_type_offres);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $this->_entity_manager->persist($_type_offres);
            $this->_entity_manager->flush();
            
            $this->addFlash(
                'success',
                "Insertion effectuée avec succès"
            );
            return $this->redirectToRoute('type_offres');
        }

        return $this->render('BackOffice/operateurs/type_offres/ajout.html.twig', [
            'type_offres' => $_type_offres,
            'form' => $form->createView()
        ]);
    }

	/**
     * @Route("/modifier-type-offres/{id}", name="edite_type_offres")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function modifierTypeOffres(Request $request, TypeOffres $_type_offres): Response
    {   
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(TypeOffresType::class, $_type_offres);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $this->_entity_manager->persist($_type_offres);
            $this->_entity_manager->flush();
            
            $this->addFlash(
                'success',
                "Modification effectuée avec succès"
            );
            return $this->redirectToRoute('type_offres');
        }

        return $this->render('BackOffice/operateurs/type_offres/modifier.html.twig', [
            'type_offres' => $_type_offres,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/liste-type-offres-operateur", name="liste_type_offres_operateur")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function getTypeOffresByOperateur(int $id)
    {
        $_type_offres = $this->_type_offres_repository->findBy(['operateur' => $id]);

        $response = [];
        foreach ($_type_offres as $_type_offre) {
            $response[] = [
                'id'   => $_type_offre->getId(),
                'nom' => $_type_offre->getNom(),
            ];
        }

        return new JsonResponse(['type_offres' => $response]);
    }

}