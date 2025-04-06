<?php

namespace App\Controller\BackOffice;

use App\Entity\Operateur;
use App\Form\BackOffice\OperateurType;
use App\Repository\OperateurRepository;
use App\Repository\PaysRepository;
use App\Service\UploadFileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OperateurController extends AbstractController
{
    private $_entity_manager;
    private $_operateur_repository;
    private $_uploadFileService;
    private $_liste_pays;

    public function __construct(
        EntityManagerInterface $_entity_manager,
        OperateurRepository $_operateur_repository,
        UploadFileService $_uploadFileService,
        PaysRepository $_liste_pays
    )
    {
        $this->_entity_manager = $_entity_manager;
        $this->_operateur_repository = $_operateur_repository;
        $this->_uploadFileService = $_uploadFileService;
        $this->_liste_pays = $_liste_pays;
    }

    /**
     * @Route("/operateurs", name="operateurs")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function index(): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $listePays = $this->_liste_pays->findAll();

        $_operateurs = $this->_operateur_repository->findAll();

        return $this->render('BackOffice/operateurs/index.html.twig', [
            'operateurs' => $_operateurs,
            'listePays' => $listePays,
        ]);
    }

     /**
     * @Route("/ajout-operateur", name="new_operateur")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutOperateur(Request $request): Response
    {   
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $operateur = new Operateur();
        $form = $this->createForm(OperateurType::class, $operateur);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $fichier   = $form->get('logo')->getData();
            $_is_actif = $request->request->get('is_active');
            $operateur->setNomPays($operateur->getIdPays()->getNom());
            ($_is_actif) ? $operateur->setActif(1) : $operateur->setActif(0);

            if ($fichier) {
                $_logo_file = $this->_uploadFileService->saveFile($fichier, 'logo_operateur', '/uploads/images/logo/operateur/');
                if ($_logo_file) $operateur->setLogo($_logo_file);
            }

            $this->_entity_manager->persist($operateur);
            $this->_entity_manager->flush();
            
            $this->addFlash(
                'success',
                "Insertion effectuée avec succès"
            );
            return $this->redirectToRoute('operateurs');
        }

        return $this->render('BackOffice/operateurs/ajout.html.twig', [
            'operateur' => $operateur,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modification-operateur", name="modification_operateur")
     */
    public function modificationOperateur(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $id = $request->request->get('id');
        $nom = $request->request->get('nom');
        $type = $request->request->get('type');
        $idPays = $request->request->get('idPays');
        $longueurCode = $request->request->get('longueurCode');
        $isActif = $request->request->get('isActif');
        $logoFile = $request->files->get('logo');
        
        $pays = $this->_liste_pays->findOneById($idPays);
        $operateur = $this->_operateur_repository->findOneById($id);

        $operateur->setNom($nom);
        $operateur->setType($type);
        $operateur->setIdPays($pays);
        $operateur->setLongueurCode($longueurCode);
        $operateur->setActif($isActif);
        $operateur->setNomPays($pays->getNom());

        if ($logoFile) {
            $_logo_file = $this->_uploadFileService->saveFile($logoFile, 'logo_operateur', '/uploads/images/logo/operateur/');
            if ($_logo_file) $operateur->setLogo($_logo_file);
        }

        $this->_entity_manager->persist($operateur);
        $this->_entity_manager->flush();

        return new JsonResponse([
            'message' => 'ok',
        ]);
    }


}