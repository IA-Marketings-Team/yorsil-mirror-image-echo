<?php

namespace App\Controller\BackOffice;

use App\Entity\Credit;
use App\Entity\Notification;
use App\Entity\NotificationTrx;
use App\Entity\Rechargeflexi;
use App\Repository\BoutRepository;
use App\Repository\DebitRepository;
use App\Repository\SeuilRepository;
use App\Repository\CreditRepository;
use App\Repository\DepotRepository;
use App\Repository\FlixbusRepository;
use App\Repository\FatouratiPaiementRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Metier\ServiceMetierDebit;
use App\Service\Metier\ServiceMetierDepot;
use App\Service\Metier\ServiceMetierGeste;
use App\Service\Metier\ServiceMetierSeuil;
use App\Service\Metier\ServiceMetierCredit;
use App\Service\Metier\ServiceMetierTransfert;
use App\Repository\GestecommercialRepository;
use App\Repository\RechargeflexiRepository;
use App\Repository\RechargeRepository;
use App\Repository\TransfertRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JournalController extends AbstractController
{

    public function __construct(
        EntityManagerInterface $_entity_manager,
        ServiceMetierCredit $_credit_manager,
        ServiceMetierDebit  $_debit_manager,
        ServiceMetierDepot  $_depot_manager,
        ServiceMetierSeuil $_seuil_manager,
        ServiceMetierGeste $_geste_manager,
        ServiceMetierTransfert $_transfert_manager,
        TransfertRepository $transfertRepository,
        CreditRepository $creditRepository,
        FlixbusRepository $flixbusRepository,
        FatouratiPaiementRepository $fatouratiPaiementRepository,
        BoutRepository $boutRepo
    )
    {
        $this->_entity_manager   = $_entity_manager;
        $this->_credit_manager = $_credit_manager;
        $this->_debit_manager = $_debit_manager;
        $this->_depot_manager = $_depot_manager;
        $this->_seuil_manager = $_seuil_manager;
        $this->_geste_manager = $_geste_manager;
        $this->_transfert_manager = $_transfert_manager;
        $this->_transfert_repository = $transfertRepository;
        $this->creditRepository = $creditRepository ;
        $this->_flixbus_repo = $flixbusRepository;
        $this->fatouratiPaiementRepository = $fatouratiPaiementRepository;
        $this->boutRepo = $boutRepo;
    }

    /**
     * @Route("/journal", name="journal")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function index(): Response
    {
        return $this->render('BackOffice/journal/index.html.twig', [
            'controller_name' => 'JournalController',
        ]);
    }

    /**
     * @Route("/credit", name="credit")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function credit(CreditRepository $creditRepo){
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('BackOffice/journal/credit.html.twig', [
            'credits' => $creditRepo->findAll(),
        ]);
    }

    /**
     * @Route("liste-des-credits", name="liste_credit")
     */
    public function listeCredit(Request $request){
        $_page           = $request->query->get('start');
        $_nb_max_page    = $request->query->get('length');
        $_search         = $request->query->get('search')['value'];
        $_order_by       = $request->query->get('order_by');

        $_liste = $this->_credit_manager->listeCredits($_page, $_nb_max_page, $_search, $_order_by);
        return new JsonResponse([
            'recordsTotal'    => $_liste[1],
            'recordsFiltered' => $_liste[1],
            'data'            => array_map(function ($val) {
                $_response = array_values($val);
                return $_response;
            }, $_liste[0])
        ]);
    }

    /**
     * @Route("/debit", name="debit")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function debit(DebitRepository $debitRepo){
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('BackOffice/journal/debit.html.twig', [
            'debits' => $debitRepo->findAll(),
        ]);
    }

    /**
     * @Route("liste-des-debit", name="liste_debit")
     */
    public function listeDebits(Request $request){
        $_page           = $request->query->get('start');
        $_nb_max_page    = $request->query->get('length');
        $_search         = $request->query->get('search')['value'];
        $_order_by       = $request->query->get('order_by');

        $_liste = $this->_debit_manager->listeDebits($_page, $_nb_max_page, $_search, $_order_by);
        return new JsonResponse([
            'recordsTotal'    => $_liste[1],
            'recordsFiltered' => $_liste[1],
            'data'            => array_map(function ($val) {
                $_response = array_values($val);
                return $_response;
            }, $_liste[0])
        ]);
    }

    /**
     * @Route("/Geste-commerciale", name="GestComm")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function GestComm(GestecommercialRepository $GestCommRepo){
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('BackOffice/journal/GestComm.html.twig', [
            'GestComms' => $GestCommRepo->findAll(),
        ]);
    }


    /**
     * @Route("geste-commerciales", name="gestecom_ajax")
     */
    public function listeGeste(Request $request){
        $_page           = $request->query->get('start');
        $_nb_max_page    = $request->query->get('length');
        $_search         = $request->query->get('search')['value'];
        $_order_by       = $request->query->get('order_by');

        $_liste = $this->_geste_manager->listeGestes($_page, $_nb_max_page, $_search, $_order_by);
        return new JsonResponse([
            'recordsTotal'    => $_liste[1],
            'recordsFiltered' => $_liste[1],
            'data'            => array_map(function ($val) {
                $_response = array_values($val);
                return $_response;
            }, $_liste[0])
        ]);
    }



    /**
     * @Route("/seuil", name="seuil")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function Seuil(SeuilRepository $seuilRepository){
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('BackOffice/journal/seuil.html.twig', [
            'seuils' => $seuilRepository->findAll(),
        ]);
    }

    
    /**
     * @Route("liste-des-seuil", name="liste_seuil")
     */
    public function listeSeuils(Request $request){
        $_page           = $request->query->get('start');
        $_nb_max_page    = $request->query->get('length');
        $_search         = $request->query->get('search')['value'];
        $_order_by       = $request->query->get('order_by');

        $_liste = $this->_seuil_manager->listeSeuils($_page, $_nb_max_page, $_search, $_order_by);
        return new JsonResponse([
            'recordsTotal'    => $_liste[1],
            'recordsFiltered' => $_liste[1],
            'data'            => array_map(function ($val) {
                $_response = array_values($val);
                return $_response;
            }, $_liste[0])
        ]);
    }

     /**
     * @Route("/journal-des-transfert", name="jour_transfert")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function Transfert(TransfertRepository $transRepo){
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('BackOffice/journal/transfert_credit.html.twig', [
            'transferts' => $transRepo->findAll()
        ]);
    }

    /**
     * @Route("/journal-diaspo-transfert", name="journal_transfert_manuel")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function journalTransfertManuel(RechargeflexiRepository $rechargeflexiRepo)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('BackOffice/journal/transfert_credit_manuel.html.twig', [
            'transferts' => $rechargeflexiRepo->findAll(),
        ]);
    }

    /**
     * @Route("/valider-transferts-manuel/{id}", name="valider_transfert_manuel")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function validerTrxManuel(Rechargeflexi $rechargeflexi)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        $rechargeflexi->setIsvalid(true);
        $this->_entity_manager->persist($rechargeflexi);
        $this->_entity_manager->flush();

        $offreNom = $rechargeflexi->getNomoffre();
        $_message = "Le transfert crédit de '$offreNom' est validé";

        $bout = $this->boutRepo->findOneByUser($rechargeflexi->getUser());

        $notification_bout = new NotificationTrx();
        $notification_bout->setBoutique($bout);
        $notification_bout->setTrx($rechargeflexi);
        $notification_bout->setMessage($_message);
        $notification_bout->setIsAdmin(false);
        $this->_entity_manager->persist($notification_bout);
        $this->_entity_manager->flush();

        return new JsonResponse([
            'status' => true
        ]);
    }

    /**
     * @Route("liste-ajax-transfert", name="liste_ajax_transfert")
     */
    public function listeTranfertAjax(Request $request){
        $_page           = $request->query->get('start');
        $_nb_max_page    = $request->query->get('length');
        $_search         = $request->query->get('search')['value'];
        $_order_by       = $request->query->get('order_by');

        $_liste = $this->_transfert_manager->listeTransfert($_page, $_nb_max_page, $_search, $_order_by);
        return new JsonResponse([
            'recordsTotal'    => $_liste[1],
            'recordsFiltered' => $_liste[1],
            'data'            => array_map(function ($val) {
                $_response = array_values($val);
                return $_response;
            }, $_liste[0])
        ]);

    }

    /**
     * @Route("liste-ajax-transaction", name="liste_ajax_transaction")
     */
    public function listeTransactionAjax(Request $request){
        $_page           = $request->query->get('start');
        $_nb_max_page    = $request->query->get('length');
        $_search         = $request->query->get('search')['value'];
        $_order_by       = $request->query->get('order_by');

        $_liste = $this->_transfert_manager->listeTransaction($_page, $_nb_max_page, $_search, $_order_by);
        return new JsonResponse([
            'recordsTotal'    => $_liste[1],
            'recordsFiltered' => $_liste[1],
            'data'            => array_map(function ($val) {
                $_response = array_values($val);
                return $_response;
            }, $_liste[0])
        ]);

    }

    /**
     * @Route("/validation-transfert", name="validation_transfert")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
    */
    public function validationTransfert(Request $request){

        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_id         = $request->request->get('transfert_id');
        $_validation = ($request->request->get('validation') == 'oui') ? 'success' : 'failed'; 
        $_transfert  = $this->_transfert_repository->findOneBy(['id' => $_id]);
        $_admin      = $this->getUser();
        $_note       = ($request->request->get('validation') == 'oui') ? "" : "Annulation d'un transfert de crédit de ". $_transfert->getMontant()."€" ;

        ($_validation == 'failed') ? $_transfert->setStatus('failed') : $_transfert->setStatus('success') ;

        $_message = ( $_validation == 'failed' ) ? "Le transfert de crédit de ".$_transfert->getMontant()."€ effectuée par la boutique ".$_transfert->getBout()->getNom()." a été rejeté" : "Le transfert de crédit de ".$_transfert->getMontant()."€ effectuée par la boutique ".$_transfert->getBout()->getNom()." a été validé"; 

        $_message_notif = ( $_validation == 'failed' ) ? "Le transfert de crédit de ".$_transfert->getMontant()."€ que vous avez effectué a été rejeté" : "Le transfert de crédit de ".$_transfert->getMontant()."€ vous avez effectué a été validé"; 

        $_last_recharge = $this->creditRepository->findOneByType($type);
        if ($_last_recharge) {
            $_last_number = (int)substr($_last_recharge->getRef(), 4);
            $_new_number  = $_last_number + 1;
        } else {
            $_new_number = 1;
        }
        $new_code = 'ANN' . str_pad($_new_number, 3, '0', STR_PAD_LEFT);


        if($_validation == 'failed'){
            $_credit = new Credit();
            $_credit->setDate(new \Datetime());
            $_credit->setMontant((float)$_transfert->getMontant());
            $_credit->setBout($_transfert->getBout());
            $_credit->setAdmin($_admin);
            $_credit->setNote($_note);
            $_credit->setType('5');
            $_credit->setRef($new_code);
            $_credit->setIsvalid(1);
            $this->_entity_manager->persist($_credit);
            $this->_entity_manager->flush();
        }

        $notification = new Notification();
        $notification->setBoutique($_transfert->getBout());
        $notification->setTransfert($_transfert);
        $notification->setDate(new \DateTime());
        $notification->setMessage($_message_notif);
        $notification->setIsAdmin(false);

        $this->_entity_manager->persist($notification);
        $this->_entity_manager->flush();

        $this->addFlash(
            'success',
            "{$_message}"
        );

        return $this->redirectToRoute('jour_transfert');
    }

    /**
     * @Route("/reservations-liste", name="reservations_litse")
     */
    public function listeResarvationFlix(Request $request)
    {
        $reservation = $this->_flixbus_repo->findAll();
        return $this->render('BackOffice/journal/reservation.html.twig', [
            'reservations' => $reservation
        ]);
    }

    /**
     *@Route("/journal-recharge", name="journal_recharge")
     *@IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function journalRecharge(RechargeRepository $rechargeRepo)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $recharge = $rechargeRepo->findAll();

        return $this->render('BackOffice/journal/recharge.html.twig', [
            'recharges' => $recharge
        ]);
    }

    /**
     * @Route("liste-des-depots", name="liste_ajax_depot")
     */
    public function listeDepot(Request $request){
        $_page           = $request->query->get('start');
        $_nb_max_page    = $request->query->get('length');
        $_search         = $request->query->get('search')['value'];
        $_order_by       = $request->query->get('order_by');

        $_liste = $this->_depot_manager->listeDepots($_page, $_nb_max_page, $_search, $_order_by);
        return new JsonResponse([
            'recordsTotal'    => $_liste[1],
            'recordsFiltered' => $_liste[1],
            'data'            => array_map(function ($val) {
                $_response = array_values($val);
                return $_response;
            }, $_liste[0])
        ]);
    }

    /**
     *@Route("/journal-depot", name="journal_depot")
     *@IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function journalDepot(DepotRepository $depotRepository)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $depot = $depotRepository->findAll();

        return $this->render('BackOffice/journal/depot.html.twig', [
            'depots' => $depot
        ]);
    }

    /**
     *@Route("/valider-depot", name="valider_depot")
     *@IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function validerDepot(Request $request, DepotRepository $depotRepo)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $idDepot = $request->request->get('id');
        $_validation   = ($request->request->get('validation') == 'oui') ? 1 : 0; 

        $depot = $depotRepo->findOneById($idDepot);
        $depot->setIsValid($_validation);

        $this->_entity_manager->persist($depot);
        $this->_entity_manager->flush();

        $_message = ( $_validation == 0 ) ? "Le dépôt de ".$depot->getMontant()."€ effectuée par le percepteur ".$depot->getPercepteur()->getNom()." a été rejeté" : "Le dépôt de ".$depot->getMontant()."€ effectuée par le percepteur ".$depot->getPercepteur()->getNom()." a été validé";

        $this->addFlash(
            'success',
            "{$_message}"
        );

       return $this->redirectToRoute('journal_depot');
    }

    /**
     *@Route("/journal-paiement-facture", name="journal_paiement_facture")
     *@IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function journalPaiement()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $paiement =  $this->fatouratiPaiementRepository->findAll();

        return $this->render('BackOffice/journal/paiement.html.twig', [
            'paiements' => $paiement
        ]);
    }

    /**
     *@Route("/historique-de-paiement", name="historique_paiement")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
    */
    public function journalPaiementBoutique()
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        $_boutique = $this->boutRepo->findOneBy(['user' => $this->getUser()]);
        $_paiement = $this->fatouratiPaiementRepository->findBy(['boutique' => $_boutique]);
        
        return $this->render('FrontOffice/journal/paiement.html.twig',[
            'paiements' => $_paiement
        ]);
    }

}
