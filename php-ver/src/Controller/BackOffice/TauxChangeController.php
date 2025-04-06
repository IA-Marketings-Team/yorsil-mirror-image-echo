<?php

namespace App\Controller\BackOffice;

use App\Entity\Pays;
use App\Entity\TauxChange;
use App\Form\BackOffice\TauxChangeType;
use App\Repository\PaysRepository;
use App\Repository\TauxChangeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TauxChangeController extends AbstractController
{

    public function __construct(
        EntityManagerInterface $_entity_manager,
        PaysRepository $paysRepository,
        TauxChangeRepository $tauxChangeRepository
    )
    {
        $this->_entity_manager = $_entity_manager;
        $this->_pays_repository = $paysRepository;
        $this->tauxChangeRepository = $tauxChangeRepository;
    }

    /**
     * @Route("/liste-des-taux-de-change", name="liste_changes")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function index(): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
		
		return $this->render('BackOffice/changes/index.html.twig', [
            'taux_changes' => $this->tauxChangeRepository->findAll()
        ]);
    }

    /**
     * @Route("/ajout-taux-de-change", name="new_taux_change")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutTauxChange(Request $request)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        $taux = new TauxChange();
        $form = $this->createForm(TauxChangeType::class, $taux);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
        	$_rec = $this->tauxChangeRepository->countTauxChangeJours($taux->getPaysChange()->getId(),$taux->getDevise());
        	//dd($_rec = $this->tauxChangeRepository->countTauxChangeJours($taux->getPaysChange()->getId(),$taux->getDevise()));
        	if ($_rec == '1') {
        		$this->addFlash(
	                'success',
	                "Taux de change existant pour aujourd'hui"
	            );
            	return $this->redirectToRoute('liste_changes');
        	}

        	$taux->setDateChange(new \DateTime());
            $this->_entity_manager->persist($taux);
            $this->_entity_manager->flush();
            $this->addFlash(
                'success',
                "Taux de change bien enregistré avec succès"
            );
            return $this->redirectToRoute('liste_changes');
        }

        return $this->render('BackOffice/changes/ajout.html.twig',[
            'form'=>$form->createView()
        ]);
    }

}