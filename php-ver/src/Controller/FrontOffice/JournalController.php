<?php

namespace App\Controller\FrontOffice;

use App\Entity\Rechargeflexi;
use App\Entity\Credit;
use App\Entity\Debit;
use App\Entity\Notification;
use App\Service\Metier\PdfService;
use App\Repository\BoutRepository;
use App\Repository\CreditRepository;
use App\Repository\FlixbusRepository;
use App\Repository\TransfertRepository;
use App\Repository\RechargeRepository;
use App\Repository\RechargeflexiRepository;
use App\Repository\NotificationrechargementRepository;
use App\Service\Metier\ServiceMetierBoutique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JournalController extends AbstractController
{

	private $_entity_manager;
	private $_bout_repository;
	private $_credit_repository;

	public function __construct( EntityManagerInterface $_entity_manager,
                                PdfService $pdfService,
								BoutRepository $boutRepository,
                                CreditRepository $creditRepository,
                                FlixbusRepository $_flix_repo,
                                TransfertRepository $_transfertRepo,
                                RechargeRepository $rechargeRepository,
                                RechargeflexiRepository $rechargeFlexiRepository,
                                NotificationrechargementRepository $notificationrechargementRepository){
	    $this->_entity_manager    = $_entity_manager;
	    $this->_bout_repository   = $boutRepository;
        $this->_credit_repository = $creditRepository;
        $this->pdfService         = $pdfService;
        $this->_flix_repo         = $_flix_repo;
        $this->_transfertRepo     = $_transfertRepo;
        $this->rechargeRepository = $rechargeRepository;
        $this->rechargeFlexiRepository = $rechargeFlexiRepository;
        $this->_notificationrechargement_repository = $notificationrechargementRepository;
    }

    /**
     *@Route("/journal-de-rechargement", name="journal_rechargement")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
    */
    public function index()
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        $_boutique     = $this->_bout_repository->findOneBy(['user' => $this->getUser()]);
        $_rechargement = $this->_credit_repository->findBy(['bout' => $_boutique]);
        
        return $this->render('FrontOffice/journal/rechargement.html.twig',[
            'rechargements' => $_rechargement
        ]);
    }

    /**
     *@Route("/detail-notification-rechargement/{id}", name="detail_notif_rechargement")
     */
    public function detailNotif($id = null){

        if($id){
            $notif = $this->_notificationrechargement_repository->findOneBy(['id' => $id]);
            $notif->setIsRead(true);

            $this->_entity_manager->persist($notif);
            $this->_entity_manager->flush();
        }

        $notifications = ($this->getUser()->getRoles()[0] != 'ROLE_BOUT') ? $this->_notificationrechargement_repository->findBy(['isAdmin' => true], ['id' => 'DESC']) : $this->_notificationrechargement_repository->findBy(['isAdmin' => false], ['id' => 'DESC']) ;

        return $this->render('BackOffice/journal/notification_rechargement.html.twig', [
            'notifications' => $notifications,
            'idNotif' => $id
        ]);
    }

    /**
     *@Route("/chiffre-daffaire-boutique", name="chiffre_affaire_boutiques")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
    */
    public function facturationServices(Request $request)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_boutique     = $this->_bout_repository->findOneBy(['user' => $this->getUser()]);;
        $_reservation  = $this->_flix_repo->findBy(['boutique' => $_boutique]);
        $_recharge     = $this->rechargeRepository->findBy(['boutique' => $_boutique]);
        $_transfert    = $this->_transfertRepo->findBy(['bout' => $_boutique]);
        $_diaspo       = $this->rechargeFlexiRepository->findBy(['user' => $this->getUser()]);

        // Combine toutes les entités en un seul tableau
        $entities = array_merge($_reservation, $_recharge, $_transfert, $_diaspo);

        // Formate les données pour la facturation
        $billingItems = $this->pdfService->formatForBillingCA($entities);

        return $this->render('FrontOffice/journal/chiffre_affaires.html.twig', [
            'items' => $billingItems
        ]);

    }


}