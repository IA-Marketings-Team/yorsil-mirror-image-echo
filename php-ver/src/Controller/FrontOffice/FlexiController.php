<?php

namespace App\Controller\FrontOffice;

use App\Entity\Rechargeflexi;
use App\Entity\Transfert;
use App\Entity\Debit;
use App\Entity\Notification;
use App\Entity\NotificationTrx;
use App\Repository\BoutRepository;
use App\Repository\FraiserviceboutiqueRepository;
use App\Repository\FraiserviceRepository;
use App\Repository\NotificationTrxRepository;
use App\Repository\OperateurRepository;
use App\Repository\RechargeflexiRepository;
use App\Service\Metier\ServiceMetierRechargementFlexi;
use App\Service\Metier\ServiceMetierBoutique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FlexiController extends AbstractController
{

	private $_entity_manager;
	private $_bout_repository;
	private $_operateur_repository;
	private $_rechargeflexi_repository;
	private $_rechargementflexi_manager;
	private $_boutique_manager;
    private $_frais_service_repo;
    private $_frais_service_bout_repo;
    private $notifTrxRepo;

	public function __construct( EntityManagerInterface $_entity_manager,
								BoutRepository $boutRepository,
                                OperateurRepository $operateurRepository,
                                RechargeflexiRepository $rechargeflexiRepository,
                                ServiceMetierRechargementFlexi $rechargementfleximanager,
                                ServiceMetierBoutique $_boutique_manager,
                                FraiserviceRepository $_frais_service_repo,
                                FraiserviceboutiqueRepository $_frais_service_bout_repo,
                                NotificationTrxRepository $notifTrxRepo){
	    $this->_entity_manager            = $_entity_manager;
	    $this->_bout_repository           = $boutRepository;
        $this->_operateur_repository      = $operateurRepository;
	    $this->_rechargeflexi_repository  = $rechargeflexiRepository;
        $this->_rechargementflexi_manager = $rechargementfleximanager;
        $this->_boutique_manager          = $_boutique_manager;
        $this->_frais_service_repo        = $_frais_service_repo;
        $this->_frais_service_bout_repo   = $_frais_service_bout_repo;
        $this->notifTrxRepo               = $notifTrxRepo;
    }

    /**
     *@Route("/flexi", name="flexi")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
    */
    public function index()
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('FrontOffice/flexi/index.html.twig');
    }

    /**
     * @Route("/liste-offre-flexi", name="liste_offre_flexi")
    */
    public function listeOffreOperateurs(Request $request){

        $_off = $request->request->get('off');

        return new JsonResponse([
            'html' => $this->renderView('FrontOffice/flexi/liste_offre.html.twig', [
                "off" => $_off, 
            ])
        ]);
    }
    
    /**
     * @Route("/ajout-rechargement-manuel", name="new_rechargement_manuel")
    */
    public function ajoutRechargementManuel(Request $request){

        $user = $this->getUser();
        $bout  = $this->_bout_repository->findOneBy(["user" => $user]);
        $_off = $request->request->get('off');
        $_id  = $request->request->get('id');
        $_tel = $request->request->get('tel');
        $_montant = (float)$request->request->get('montant');

        $fraisService = $this->_frais_service_repo->findOneByType("5");
        $fraisServiceBout = $this->_frais_service_bout_repo->findOneBy(["boutique" => $bout,"type" => "5"]);

        $frais = $fraisService->getPourcentage();

        if ($fraisServiceBout) {
            $fraisBoutique = (float)$fraisServiceBout->getPourcentage();
        } else {
            $fraisBoutique = (float)$fraisService->getPourcentageBoutique();
        }

        $pourcentageBoutique = $_montant * ($fraisBoutique / 100);

        $debitSold = $_montant - $pourcentageBoutique;

        $_op = $this->_operateur_repository->findOneBy(['id' => $_id ]);

        $_recharge = new Rechargeflexi();
        $_recharge->setNumero($_tel);
        $_recharge->setNomoffre($_off);
        $_recharge->setMontant($_montant);
        $_recharge->setDate(new \Datetime());
        $_recharge->setOperateur($_op);
        $_recharge->setUser($user);
        $_recharge->setFrais($frais);
        $_recharge->setFraisBout($pourcentageBoutique);

        $this->_entity_manager->persist($_recharge);
        $this->_entity_manager->flush();

        $_message = "La boutique " . $bout->getNom() . " a effectuée un transfert de crédit de $_montant €";

        $_credit = $this->_boutique_manager->creditBoutique($user);
        $_debit  = $this->_boutique_manager->debitBoutique($user);
        $_geste  = $this->_boutique_manager->gesteBoutique($user);

        $sold_Bout = (float)($_credit + $_geste - $_debit);

        $new_solde = $sold_Bout - $debitSold;

        $_debits = new Debit();
        $_debits->setDate(new \Datetime());
        $_debits->setMontant($debitSold);
        $_debits->setBout($bout);
        $_debits->setAdmin(null);
        $_debits->setNote($_message);

        $this->_entity_manager->persist($_debits);
        $this->_entity_manager->flush();

        $notification_admin = new NotificationTrx();
        $notification_admin->setBoutique($bout);
        $notification_admin->setTrx($_recharge);
        $notification_admin->setMessage($_message);
        $notification_admin->setIsAdmin(true);
        $this->_entity_manager->persist($notification_admin);
        $this->_entity_manager->flush();

        return new JsonResponse([
            'status' => true,
            'solde' => $new_solde
        ]);
    }

    /**
     * @Route("/ajout-rechargement-api", name="new_rechargement_api")
    */
    public function ajoutRechargementApi(Request $request){

        $user = $this->getUser();

        $_credit = $this->_boutique_manager->creditBoutique($user);
        $_debit  = $this->_boutique_manager->debitBoutique($user);
        $_geste  = $this->_boutique_manager->gesteBoutique($user);

        $sold_Bout = (float)($_credit + $_geste - $_debit);

        $country   = $request->request->get('country');
        $number    = $request->request->get('number');
        $offre     = $request->request->get('offre');
        $operator  = $request->request->get('operator');
        $status    = $request->request->get('status');
        $trx_Id    = $request->request->get('trx_Id');
        $type      = $request->request->get('type');
        
        $_boutique = $this->_bout_repository->findOneBy(['user' => $this->getUser()]);
        
        $commition  = $offre['commition'];
        $apiName    = $offre['apiName'];
        $_tarif     = (float)$offre['tarifbalance'];
        $_devise    = $offre['currencycode'];
        $_desc      = "Transfert de crédit de $_tarif $_devise ";

        $response = false ;

        $fraisType = $apiName == "reloadly" ? "3" : "2";

        $fraisService = $this->_frais_service_repo->findOneByType($fraisType);
        $fraisServiceBout = $this->_frais_service_bout_repo->findOneBy(["boutique" => $_boutique,"type" => $fraisType]);

        $frais = $fraisService->getPourcentage();

        if ($fraisServiceBout) {
            $fraisBoutique = (float)$fraisServiceBout->getPourcentage();
        } else {
            $fraisBoutique = (float)$fraisService->getPourcentageBoutique();
        }

        $pourcentageBoutique = $_tarif * ($fraisBoutique / 100);

        $debitSold = $_tarif - $pourcentageBoutique;

        $new_solde = $sold_Bout - $debitSold;

        if ($status=='SUCCESSFUL') {
            if( $sold_Bout >= $_tarif ){

                $transfert = new Transfert();
                $transfert->setBout($_boutique);
                $transfert->setCodePays($country['code']);
                $transfert->setNumero($number);
                $transfert->setMontant($_tarif);
                $transfert->setDate(new \DateTime());
                $transfert->setOperateur($operator['name']); 
                $transfert->setTrxId($trx_Id);
                $transfert->setStatus('success');
                $transfert->setPays($country['pays']);
                $transfert->setDeviseLocal($offre['currencycode']);
                $transfert->setDeviseCompte('EUR');
                $transfert->setEmail($_boutique->getEmail());
                $transfert->setMontantDevise((float)$offre['tarif']);
                $transfert->setOperateurInfo($operator);
                $transfert->setFx(array($offre['fx']));
                $transfert->setType($type);
                $transfert->setCommission($commition);
                $transfert->setApiName($apiName);
                $transfert->setFraisBoutique($pourcentageBoutique);
                ($offre['desc']!='') ? $transfert->setDescription($offre['desc']) : $transfert->setDescription($_desc);

                $this->_entity_manager->persist($transfert);
                $this->_entity_manager->flush();

                $_debits = new Debit();
                $_debits->setDate(new \Datetime());
                $_debits->setMontant($debitSold);
                $_debits->setBout($_boutique);
                $_debits->setAdmin(null);
                $_debits->setNote($_desc);
                $this->_entity_manager->persist($_debits);
                $this->_entity_manager->flush();

                $_message = "La boutique ".$_boutique->getNom()." a effectuée un transfert de crédit de $_tarif $_devise €"; 

                $notification_admin = new Notification();
                $notification_admin->setBoutique($_boutique);
                $notification_admin->setTransfert($transfert);
                $notification_admin->setDate(new \DateTime());
                $notification_admin->setMessage($_message);
                $notification_admin->setIsAdmin(true);
                $this->_entity_manager->persist($notification_admin);
                $this->_entity_manager->flush();

                $response = true;
            }
        }

        return new JsonResponse([
            'status' => $response,
            'solde' => $new_solde
        ]);
    }

    /**
     * @Route("/new-rechargement", name="new_rechargement")
     */
    public function newRechargement(Request $request){
        $id = (int)strip_tags($request->request->get('id'));

        $newRechargement    = $this->_rechargementflexi_manager->notifRechargement($id);
        $lastRechargement   = $this->_rechargeflexi_repository->findOneBy([],['id' => 'DESC']);
        $lastIdRechargement = $lastRechargement ? $lastRechargement->getId() : 0;

        $newRechargement = json_encode($newRechargement);

        return new JsonResponse ([
            'newRechargements' => $newRechargement,
            'lastIdRechargement' => $lastIdRechargement
        ]);
    }

    /**
     *@Route("/detail-notification-trx/{id}", name="detail_notif_trx")
     */
    public function detailNotifTrx($id = null)
    {

        if ($id) {
            $notif = $this->notifTrxRepo->findOneBy(['id' => $id]);
            $notif->setIsRead(true);

            $this->_entity_manager->persist($notif);
            $this->_entity_manager->flush();
        }

        $notifications = ($this->getUser()->getRoles()[0] != 'ROLE_BOUT') ? $this->notifTrxRepo->findBy(['is_admin' => true], ['id' => 'DESC']) : $this->notifTrxRepo->findBy(['is_admin' => false], ['id' => 'DESC']);

        return $this->render('BackOffice/journal/notification_trx.html.twig', [
            'notifications' => $notifications,
            'idNotif' => $id
        ]);
    }
}