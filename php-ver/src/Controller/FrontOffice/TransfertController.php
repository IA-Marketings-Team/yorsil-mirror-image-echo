<?php

namespace App\Controller\FrontOffice;

use App\Entity\Account;
use DateTime;
use App\Entity\Bout;
use App\Entity\Transfert;
use App\Entity\Transaction;
use App\Entity\Notification;
use App\Repository\AccountRepository;
use App\Repository\BoutRepository;
use App\Repository\CreditRepository;
use App\Repository\TransfertRepository;
use Doctrine\ORM\EntityManagerInterface;
use DoctrineExtensions\Query\Mysql\Date;
use App\Repository\TransactionRepository;
use App\Repository\NotificationRepository;
use App\Repository\OffresRepository;
use App\Repository\PaysRepository;
use App\Repository\RechargeflexiRepository;
use App\Service\Metier\ServiceMetierBoutique;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Metier\ServiceMetierTransfert;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use JetBrains\PhpStorm\Internal\ReturnTypeContract;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TransfertController extends AbstractController
{
    private $_entity_manager;
    private $_transfertRepo;
    private $_transactionRepo;
    private $_bout_repo;
    private $_credit_repo;
    private $_accountRepo;
    private $serialize;
    private $listeCountry;
    private $offreRepo;
    private $rechargeFexiRepo;

    public function __construct(
        EntityManagerInterface $_entity_manager,
        TransfertRepository $_transfertRepo,
        TransactionRepository $_transactionRepo,
        BoutRepository $_bout_repo,
        CreditRepository $_credit_repo,
        AccountRepository $_accountRepo,
        SerializerInterface $serialize,
        PaysRepository $listeCountry,
        OffresRepository $offreRepo,
        RechargeflexiRepository $rechargeFexiRepo
    ){
        $this->_entity_manager  = $_entity_manager;
        $this->_transfertRepo   = $_transfertRepo;
        $this->_transactionRepo = $_transactionRepo;
        $this->_bout_repo       = $_bout_repo;
        $this->_credit_repo     = $_credit_repo;
        $this->_accountRepo     = $_accountRepo;
        $this->serialize        = $serialize;
        $this->listeCountry     = $listeCountry;
        $this->offreRepo        = $offreRepo;
        $this->rechargeFexiRepo = $rechargeFexiRepo;
    }

    /**
     *@Route("/transfert-credit", name="transfert_credit")
     */
    public function transfertArgent(SessionInterface $session)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $countries = $this->listeCountry->findAll();

        $offresArray = $session->get('offresManue', null);

        if ($offresArray == null) {
            $offres = $this->offreRepo->findAll();
            $offresArray = array_map(function($offre) {
                $countrie = $this->listeCountry->findOneById($offre->getOperateur()->getIdPays());
                return $arrayOffre = [
                    'id' => $offre->getId(),
                    'nom' => $offre->getNom(),
                    'montant' => $offre->getMontant(),
                    'devise' => $offre->getMontantDevise(),
                    'description' => $offre->getDescription(),
                    'type' => [
                        'id' => $offre->getTypeOffres()->getId(),
                        'nom' => $offre->getTypeOffres()->getNom(),
                    ],
                    'operateur' => [
                        'id' => $offre->getOperateur()->getId(),
                        'nom' => $offre->getOperateur()->getNom(),
                        'code' => $countrie->getCode(),
                        'pays' => $countrie->getNom(),
                        'prefixe' => $countrie->getPrefixe(),
                        'type' => $countrie->getTypeApi(),
                    ],
                    'logo' => $offre->getOperateur()->getLogo()->getUrlFichier(),
                ];
            }, $offres);
            $session->set('offresManue', $offresArray);
        }

        if($this->isGranted('ROLE_ADMIN')){
            return $this->render('BackOffice/home/credit.html.twig');
        } else {
            return $this->render('FrontOffice/transfert/transfert_argent.html.twig', [
                "listeCountry" => $countries,
                "offres" => $offresArray
            ]);
        }
    }

    /**
     *@Route("/transfertcredit", name="transfert_ding")
     */
    public function transfertDing()
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $countries = $this->listeCountry->findAll();

        if($this->isGranted('ROLE_ADMIN')){
            return $this->render('BackOffice/home/credit.html.twig');
        } else {
            return $this->render('FrontOffice/transfert/transfert_argent_ding.html.twig', [
                "listeCountry" => $countries
            ]);
        }
    }

    /**
     * @Route("/handle-callback", name="handle_callback", methods={"POST"})
     */
    public function handleCallback(Request $request): Response
    {
        // Récupérer les données JSON du corps de la requête
        $data = json_decode($request->getContent(), true);

        // Traiter les données reçues
        if (isset($data['transaction']) && isset($data['hash'])) {
            // Votre logique de traitement ici
            $trxID = $data['transaction']['id'];
            $status = $data['status'];
            // Par exemple, enregistrez les données dans votre base de données ou effectuez d'autres actions nécessaires.
            if(isset($trxID)){
                $trx = $this->_transfertRepo->findOneBy(['trxId' => $trxID]);
                if(isset($trx)){
                    $trx->setStatus($status);
        
                    $this->_entity_manager->persist($trx);
                    $this->_entity_manager->flush();
                }
            }

            $this->addFlash(
                'success',
                "Changement de status Transaction"
            );
            // Répondre à l'API externe
            $response = [
                'status' => 'success'
            ];

            return new JsonResponse($response);
        }

        // En cas de données invalides ou de problème de traitement
        return new Response('Bad Request', Response::HTTP_BAD_REQUEST);
    }

    /**
     *@Route("/transfert-credit-api", name="transfert_credit_test")
     */
    public function transfertCreditTest(Request $request, TransfertRepository $trxRepo, BoutRepository $boutRepo)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $user     = $this->getUser();
        $boutique = $boutRepo->findOneBy(["user" => $user])->getId();

        $trxId = $trxRepo->findOneBy([], ['id' => 'DESC']);
        $trxId = $trxId ? $trxId->getId() : 0;
        $trxId += 1;

        $receiver_fullname = $user->getNom() . " ". $user->getPrenom();

        $phone  = $request->request->get('phone');
        $amount = (float)$request->request->get('amount');
        $pays   = $request->request->get('pays');
        $prefix = $request->request->get('prefix');
        $flag   = $request->request->get('flag');

        try {
            $curl = curl_init();

            $payload = array(
                "api_key" => "7ee4bed57e38a7f562bfabf312de22de23e68b8292745731c2592d31d278d666",
                // "api_key" => "5eaede1f5c5d373c913afca7c8177215a17e113a5eafd7e9bac7eb6569fcb548", 
                "service_items_id" => 1,
                "phone_number"     => $phone, // 327011700
                "amount"           => $amount,
                "custom_data"      => json_encode([
                    "ext_transaction_id" => $trxId,
                    "receiver_fullname"  => $receiver_fullname,
                    "is_uv_value"        => 0,
                    // data perso
                    "pays"     => $pays,
                    "prefix"   => $prefix,
                    "flag"     => $flag,
                    "boutique" => $boutique,
                ])
            );
            
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://apiv2.creditemoi.com/api/v2/credit/send",
                // CURLOPT_URL => "https://restv2.almfinance.com/api/v2/credit/send", 
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json"
                ],
            ]);
            
            $data  = curl_exec($curl);
            $error = curl_error($curl);
            
            curl_close($curl);

            $data = json_decode($data, true);

            $setAccount = $this->setAccount($data);

            $setSoldBout = $this->setSoldeBout($data, $boutique, $amount);
            
            if(isset($data['transaction'])){
                return new JsonResponse([
                    'status' => true,
                    'data'   => $data,
                    'solde'  => $setSoldBout["solde"],
                    'message'=> "transaction ". $data['transaction']['id'] . " success",
                    'error'  => $error,
                ], Response::HTTP_OK);
            } else {
                return new JsonResponse([
                    'status' => true,
                    'data'   => $data,
                    'solde'  => $setSoldBout["solde"],
                    'message'=> "transaction effectué succès",
                    'error'  => $error,
                ], Response::HTTP_OK);
            }
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'status'  => false,
                'data'    => $error,
                'solde'   => false,
                'message' => 'Une erreur est survenue, veuillez réessayer par la suite',
                'error'   => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function setSoldeBout($data, $boutId, $amount)          
    {
        $bout              = $this->_bout_repo->findOneBy(["id" => $boutId]);
        $credit            = $this->_credit_repo->findOneBy(["bout" => $bout]);
        $montant           = $credit->getMontant();
        $trxStatus         = "pending";
        $responseMessage   = "";
        $verifyTransaction = "";

        if(isset($data['response'])){
            $data = $data['response'];
        }

        if(isset($data["transaction"])){
            $responseTransaction = $data["transaction"];

            $verifyTransaction = $this->verifyTransaction($responseTransaction['id']);
            $updateData        = $verifyTransaction['response'];

            if(isset($updateData["transaction"])){
                try {
                    $trxStatus = $updateData["status"] ?? $data["status"];
                } catch (\Exception $e) {
                    $responseMessage = $e->getMessage();
                }
            } else { $responseMessage = "updateData transaction null"; }
        }

        if($trxStatus == "success"){
            $solde = (float)$montant - (float)$amount;
            $credit->setMontant($solde);

            $this->_entity_manager->persist($credit);
            $this->_entity_manager->flush();

            return [
                "status"            => $trxStatus,
                "solde"             => $solde,
                "data"              => $data,
                "verifyTransaction" => $verifyTransaction,
                "erreur"            => ""
            ];
        } else {
            return [
                "status"            => $trxStatus,
                "solde"             => false,
                "data"              => $data,
                "verifyTransaction" => $verifyTransaction,
                "erreur"            => $responseMessage
            ];
        }
    }

    public function setAccount($data)                                                                                                                                                                                                                                       
    {
        if(isset($data['account'])){
            try {
                $dataAccount = $data['account'];

                $account = $this->_accountRepo->findOneBy(["id" => $dataAccount['id']]);

                if(!isset($account)){
                    $account = new Account();
                    $account->setId($dataAccount['id']);
                }

                $account->setName($dataAccount['name']);
                $account->setSolde($dataAccount['solde']);
                $account->setOwner($dataAccount['owner']);
                $account->setActive($dataAccount['active']);
                $account->setCreatedAt(new \DateTime($dataAccount['created_at']));
                $account->setUpdatedAt(new \DateTime($dataAccount['updated_at']));
                $account->setPostPayedAmount($dataAccount['post_payed_amount']);
                $account->setIsHybrid($dataAccount['is_hybrid']);
                $account->setType($dataAccount['type']);

                $this->_entity_manager->persist($account);
                $this->_entity_manager->flush();

                return [
                    "status" => true,
                    "erreur" => ""
                ];
            } catch (\Exception $e) {
                return [
                    "status" => false,
                    "erreur" => $e->getMessage()
                ];
            }

        }
    }

    /**
     *@Route("/api/notification-cb-test", name="callback", methods={"POST"})
     */
    public function callbackTrx(): JsonResponse                                                                                                                                                                                                                                         
    {
        $trxRepo = $this->_transfertRepo->findAll();
        $callback = [];
        for ($i=0; $i < COUNT($trxRepo); $i++) { 
            $trxId = $trxRepo[$i]->getTrxId();
            $resp = $this->verifyTransaction($trxId);
            $resp['hash'] = "######################";
            array_push($callback, $resp);
        }

        return new JsonResponse($callback);
    }

    /**
     *@Route("/notif-trx", name="notif_trx")
     */
    public function notifTrx()                                                                                                                                                                                                                                        
    {
        $trxRepo = $this->_transactionRepo->findAll();

        $TrxMessage = "aucun modification";
        $trxError   = false;
        $trxStatus  = false;
        $TrxNotify  = [];
        $Trx        = [];

        for ($i=0; $i < COUNT($trxRepo); $i++) { 
            $trxId     = $trxRepo[$i]->getId();
            $trxResp   = $this->verifyTransaction($trxId);
            
            if($trxResp['error']){
                $trxError = true;
                return new JsonResponse([
                    'error'    => $trxError,
                    'status'   => $trxStatus,
                    'message'  => $trxResp['error'],
                    'trx'      => $trxId
                ], Response::HTTP_BAD_GATEWAY);
            } else {
                if(isset($trxResp['response']['transaction'])){
                    $newTrx = $trxResp['response']['transaction'];

                    if(isset($trxResp['response']['status'])){
                        $oldStatusTrx = $trxRepo[$i]->getTrxStatus();
                        $newStatusTrx = $trxResp['response']['status'];

                        if($oldStatusTrx != $newStatusTrx){
                            $trxRepo[$i]->setTrxStatus($newStatusTrx);
                            $Trx[] = "trx_status";
                        }
                    }
                    foreach ($newTrx as $key => $value) {
                        $key = $this->snakeCase($key);
                        $set = "set$key";
                        $get = "get$key";

                        if(isset($value)){
                            if($get == "getCreatedAt" || $get == "getUpdatedAt"){
                                $getdate = $trxRepo[$i]->$get()->format('Y-m-d H:i:s');

                                if($getdate != $value){
                                    $trxRepo[$i]->$set(new \DateTime($value));
                                    $Trx[] = $key;
                                }
                                
                            } else {
                                if($get != "getId"){
                                    if($trxRepo[$i]->$get() != $value){
                                        $trxRepo[$i]->$set($value);
                                        $Trx[] = $key;
                                    }
                                }
                            }
                        }
                    }

                    if(COUNT($Trx)){
                        try {
                            $newNotif = implode(", ", $Trx);
                            $newNotif = "modification ".$trxId.": ".$newNotif;
                            $TrxNotify[] = $newNotif;

                            $Trx = [];

                            $this->_entity_manager->persist($trxRepo[$i]);
                            $this->_entity_manager->flush();
    
                            $notification = new Notification();
                            $updatedAt    = $trxResp['response']['transaction']['updated_at'];
    
                            $notification->setTransaction($trxRepo[$i]);
                            $notification->setDate(new \DateTime($updatedAt));
                            $notification->setMessage($newNotif);
    
                            $this->_entity_manager->persist($notification);
                            $this->_entity_manager->flush();
    
                            $TrxMessage = "Modification d'un transaction";
                            $trxStatus = true;
                        } catch (\Exception $e) {
                            $trxError = true;
                            return new JsonResponse([
                                'error'   => $trxError,
                                'status'  => $trxStatus,
                                'message' => $e->getMessage(),
                                'trx'     => $TrxNotify
                            ], Response::HTTP_BAD_REQUEST);
                        }
                    }
                } else{
                    return new JsonResponse([
                        'error'   => $trxError,
                        'status'  => $trxStatus,
                        'message' => "verify transaction ".$trxId." a une erreur",
                        'trx'     => $TrxNotify
                    ], Response::HTTP_BAD_GATEWAY);
                }
            }
        }

        return new JsonResponse([
            'error'    => $trxError,
            'status'   => $trxStatus,
            'message'  => $TrxMessage,
            'trx'      => $TrxNotify
        ], Response::HTTP_OK);
    }

    public function snakeCase($snack)
    {
        $snakeCase = str_replace('_', '', ucwords($snack, '_'));

        return $snakeCase;
    }

    /**
     *@Route("/verify-trx/{id}", name="verify_trx")
     */
    public function verifyTrx($id = null)
    {
        if($id){
            try {
                $response = $this->verifyTransaction($id);
                return new JsonResponse([
                    'response' => $response
                ], Response::HTTP_OK);
            } catch (\Exception $e) {
                return new JsonResponse([
                    'error' => $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);
            }
            
        }
    }

    public function verifyTransaction($trxId)
    {
        $curl = curl_init();

        $payload = array(
            // "api_key" => "5eaede1f5c5d373c913afca7c8177215a17e113a5eafd7e9bac7eb6569fcb548",
            "api_key" => "7ee4bed57e38a7f562bfabf312de22de23e68b8292745731c2592d31d278d666",
            "transaction_id" => $trxId // 1039006 - 1039013
        );
          
        curl_setopt_array($curl, [
            // CURLOPT_URL => "https://restv2.almfinance.com/api/v2/public/verifyTransactionV2",
            CURLOPT_URL => "https://apiv2.creditemoi.com/api/v2/public/verifyTransactionV2",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
        ]);
        
        $data       = curl_exec($curl);
        $error      = curl_error($curl);
        $response   = json_decode($data, true);

        curl_close($curl);

        return [
            "response" => $response,
            "error"    => $error
        ];
    }

    /**
     *@Route("/detail-notif/{id}", name="detail_notif")
     */
    public function detailNotif(NotificationRepository $notifRepo, $id = null){

        if($id){
            $notif = $notifRepo->findOneBy(['id' => $id]);
            $notif->setIsRead(true);

            $this->_entity_manager->persist($notif);
            $this->_entity_manager->flush();
        }

        $notifications = ($this->getUser()->getRoles()[0] != 'ROLE_BOUT') ? $notifRepo->findBy(['is_admin' => true], ['id' => 'DESC']) : $notifRepo->findBy(['is_admin' => false], ['id' => 'DESC']) ;

        return $this->render('BackOffice/journal/notification.html.twig', [
            'notifications' => $notifications,
            'idNotif' => $id
        ]);
    }

    /**
     * @Route("/credit-transfert-api", name="transfert_argent_test")
     */
    public function transfertTest(Request $request) {
        $tel = $request->get('tel');
        $countries = $request->get('countries');
        $montant = (float)$request->get('montant');

        $curl = curl_init();

        $payload = array(
            "numero" => $tel,
            "pays" => $countries,
            "somme" => $montant
        );

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "x-api-key: 65b04f9e2d35dafd77afcbf99290377ff36ee7d46ee9cb77ddd682ae7b4cbbd0"
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_URL => "http://api.jupiter-data.fr/solde/transfert/TCWEiWEiM1hdbFgYgJOjQ2TqXwC3",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);
        
        $response = curl_exec($curl);
        $error = curl_error($curl);
        
        curl_close($curl);

        $is_error = null;

        if ($error) {
            dd($error);
            /* return new JsonResponse([
                'status' => false,
                'message' => $error,
            ]); */
        } else {
            $responseData = json_decode($response, true); // Convertir en tableau associatif
            if ($responseData !== null) {
                /* if (isset($responseData['message'])) {
                    $message = $responseData['message'];
                } else {
                    $message = $responseData['message'];
                } */
                dd($responseData);
            }

            /* $motRecherche = "Erreur";

            if (strpos($message, $motRecherche) !== false) {
                return new JsonResponse([
                    'status' => false,
                    'message' => $message,
                ]);
            } else {
                return new JsonResponse([
                    'status' => true,
                    'message' => $message,
                ]);
            } */
        }

        return new JsonResponse([
            'status' => false,
            'message' => 'Le numero est incorrecte',
        ]);
    }

    /**
     * @Route("/transfert-credit-reloadly", name="transfert_credit_reloadly")
     */
    public function transfertCreditReloadly(ServiceMetierTransfert $transfert_manager)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $is_actif = $this->getUser()->getIsActif();
        if(!$is_actif){
            return $this->redirectToRoute('contact_admin');
        }

        list($response, $error, $errorCode) = $transfert_manager->getAccessToken();
        
        if ($error) {
            error_log("Erreur lors de la récupération du token: $error");
            $this->addFlash('error', "Erreur de connexion à l'API Reloadly");
            return $this->render('FrontOffice/transfert/index.html.twig');
        }

        try {
            $tokenData = json_decode($response);
            $countries = json_decode(file_get_contents("https://topups.reloadly.com/countries"));
            
            return $this->render('FrontOffice/transfert/index.html.twig', [
                "countries" => $countries,
                "response" => $tokenData,
                "token_expires_in" => $tokenData->expires_in ?? null
            ]);

        } catch (\Exception $e) {
            error_log("Erreur de traitement: " . $e->getMessage());
            $this->addFlash('error', "Erreur de traitement des données");
            return $this->render('FrontOffice/transfert/index.html.twig');
        }
    }

    /**
     * @Route("/Auto-Detect-Operator", name="auto_detect_operator")
     */
    public function detectOperator(Request $request, ServiceMetierTransfert $transfert_manager)
    {
        try {
            $phone = $request->get('tel');
            $countryisocode = $request->get('countries');
            $tokken = $request->get('tokken');
            $numero = $request->get('numero');

            // Log des paramètres entrants
            error_log("Auto-Detect Operator Request: " . json_encode([
                'phone' => $phone,
                'country' => $countryisocode,
                'numero' => $numero
            ]));

            // Validation du numéro avec gestion d'erreur améliorée
            if(!$transfert_manager->isValidNumber($numero)) {
                $errorMsg = $transfert_manager->getValidationError();
                error_log("Numéro invalide: " . $errorMsg);
                return new JsonResponse([
                    'status' => false,
                    'message' => $errorMsg,
                    'error_code' => 'INVALID_NUMBER'
                ], 400);
            }

            // Récupération des opérateurs avec gestion d'erreur structurée
            list($response, $error, $errorCode) = $transfert_manager->getOperators($tokken, $phone, $countryisocode);

            if ($error) {
                error_log("Reloadly API Error [$errorCode]: " . $error);
                return new JsonResponse([
                    'status' => false,
                    'message' => 'Erreur API Reloadly',
                    'api_error' => $error,
                    'error_code' => $errorCode,
                    'debug_info' => [
                        'phone' => $phone,
                        'country' => $countryisocode
                    ]
                ], 500);
            }

            $operateurs = json_decode($response[0]);
            
            // Vérification des offres disponibles
            if(empty($operateurs->operators)) {
                return new JsonResponse([
                    'status' => false,
                    'message' => 'Aucun opérateur disponible pour ce pays',
                    'country' => $countryisocode
                ], 404);
            }

            return new JsonResponse([
                'status' => true,
                'message' => 'Numéro validé',
                'operateur' => $this->renderView('FrontOffice/transfert/operateur.html.twig', [
                    "operateurs" => $operateurs,
                ]),
                'forfait' => $this->renderView('FrontOffice/transfert/montantDispo.html.twig', [
                    "forfaits" => $operateurs,
                ]),
                'debug_info' => [
                    'country' => $countryisocode,
                    'operator_count' => count($operateurs->operators)
                ]
            ]);

        } catch (\Exception $e) {
            error_log("DetectOperator Exception: " . $e->getMessage());
            return new JsonResponse([
                'status' => false,
                'message' => 'Erreur technique',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @Route("/operateur-par-pays", name="operateur_pays")
     */
    public function operateurPays(Request $request)
    {
        dd($request->request->all());
        return new JsonResponse(true);                                                                                                                      
    }

     /**
     * @Route("/ajout-transfert", name="ajout_transfert")
     */
    public function ajouTransfert(Request $request,BoutRepository $bout_repository){

        $_boutique = $bout_repository->findOneBy(['user' => $this->getUser()]);

        $num  = $request->request->get('numero');
        $op   = $request->request->get('operateur');
        $code = $request->request->get('code');
        $montant = $request->request->get('montant');      

        $transfert = new Transfert();
        $transfert->setBout($_boutique);
        $transfert->setCodePays($code);
        $transfert->setNumero($num);
        $transfert->setMontant($montant);
        $transfert->setDate(new \DateTime());
        $transfert->setOperateur($op); 

        $this->_entity_manager->persist($transfert);
        $this->_entity_manager->flush();

        return new JsonResponse(true);
    }

    /**
     *@Route("/historique-transfert", name="hist_transf")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function HistTransfert(BoutRepository $boutRepo, TransfertRepository $transfertRepo)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }


        $is_actif = $this->getUser()->getIsActif();
        if(!$is_actif){
            return $this->redirectToRoute('contact_admin');
        }
        
        $boutique = $boutRepo->findOneBy(['user' => $this->getUser()]);
        $transfert = $transfertRepo->findBy(['bout' => $boutique]);
        $transfertManuel = $this->rechargeFexiRepo->findBy(['user' => $this->getUser()]);

        return $this->render('FrontOffice/transfert/hist.html.twig', [
            'boutique' => $boutique->getId(),
            'transferts' => $transfert,
            'transfertManuel' => $transfertManuel
        ]);
    }

    /**
     *@Route("/api/notification-cb/{id}", name="transactions")
     */
    public function addtransactions(Request $request, SerializerInterface $serialize, $id = null)
    {
        $method  = $request->getMethod();

        $data = json_decode($request->getContent(), true);

        if(isset($data['response'])){
            $data = $data['response'];
        }

        if(isset($data["transaction"])){
            $responseTransaction = $data["transaction"];

            $responseStatus  = $data["status"] ?? "pending";
            $responseError   = "";
            $responseMessage = "";

            if($method === "POST" || $method === "PUT"){
                
                try {
                    $transaction = $this->setTransaction($data);

                    if($method === "POST"){
                        $responseMessage = "Transaction créée";
                    } else if( $method === "PUT"){
                        $responseMessage = "Transaction modifiée";
                    }
                } catch (\Exception $e) {
                    $responseError   = 'Une erreur est survenue, veuillez réessayer par la suite';
                    $responseMessage = $e->getMessage();
                }

                $verifyTransaction = $this->verifyTransaction($responseTransaction['id']);

                if(!$verifyTransaction['error']){
                    $updateData    = $verifyTransaction['response'];
                    $updateDataTrx = $updateData["transaction"];

                    if(isset($updateDataTrx)){
                        try {
                            $transaction = $this->setTransaction($updateData);

                            if($method === "POST"){
                                $responseMessage = "Transaction créée";
                            } else if( $method === "PUT"){
                                $responseMessage = "Transaction modifiée";
                            }

                            $responseStatus      = $updateData["status"] ?? $data["status"] ?? "pending";
                            $responseTransaction = $updateDataTrx;
                        } catch (\Exception $e) {
                            $responseError       = $verifyTransaction['error'] ?? 'Une erreur est survenue, veuillez réessayer par la suite';
                            $responseMessage     = $e->getMessage();
                        }
                        
                    }
                }
                
                $response = [
                    "error"       => $responseError,
                    "message"     => $responseMessage,
                    "transaction" => $responseTransaction,
                    "status"      => $responseStatus,
                    "hash"        => "######################"
                ];

                return new JsonResponse($response, Response::HTTP_OK);

            } else if($method === "GET"){

                if($id){
                    $getTrx = $this->verifyTransaction($id);
                } else {
                    $getTrx = $this->verifyTransaction($responseTransaction['id']);
                }

                if(!$getTrx['error']){
                    $response    = $getTrx['response'];
                    $transaction = $response["transaction"];
                    $status      = $response["status"] ?? "pending";
                } else {
                    $transaction = $responseTransaction;
                    $status = $response["status"] ?? "pending";
                }

                $response = [
                    "transaction" => $transaction,
                    "status"      => $status,
                    "hash"        => "######################",
                    "error"       => $getTrx['error'] ?? ""
                ];

                return new JsonResponse($response, Response::HTTP_OK);
            }
        } else if($id) {
            if($method === "GET"){
                $getTrx = $this->verifyTransaction($id);

                try {
                    $response    = $getTrx['response'];
                    $transaction = $response["transaction"];
                    $status      = $response["status"] ?? "pending";

                    $response = [
                        "transaction" => $transaction,
                        "status"      => $status,
                        "hash"        => "######################",
                        "error"       => $getTrx['error'] ?? ""
                    ];
    
                    return new JsonResponse($response, Response::HTTP_OK);
                } catch (\Exception $e) {
                    return new JsonResponse([
                        "message" => "transaction Introuvable",
                        "error"   => $e->getMessage()
                    ], Response::HTTP_NOT_FOUND);    
                }
            }
        }
        return new JsonResponse([
            "message"  => "Aucun transaction",
            "response" => $data
        ], Response::HTTP_OK);
    }

    public function setTransaction($data)
    {
        if(isset($data['response'])){
            $data = $data['response'];
        }

        $dataTrx = $data["transaction"];

        $transaction = $this->_transactionRepo->findOneBy(["id" => $dataTrx['id']]);
    
        if(!isset($transaction)){
            $transaction = new Transaction();
            $transaction->setId($dataTrx['id'] ?? 0);
        }
        
        $dataTrx = $data["transaction"];
        
        $transaction->setTrxStatus($data["status"] ?? "pending");

        $transaction->setAmount($dataTrx['amount'] ?? 0);
        $transaction->setStatus($dataTrx['status'] ?? "");
        $transaction->setType($dataTrx['type'] ?? "");
        $transaction->setSeller($dataTrx['seller'] ?? 0);
        $transaction->setBenefits($dataTrx['benefits'] ?? 0);
        $transaction->setFees($dataTrx['fees'] ?? 0);
        $transaction->setDebitFeesFrom($dataTrx['debit_fees_from'] ?? $dataTrx['debitFeesFrom'] ?? 0);
        $transaction->setDestinationNumber($dataTrx['destination_number'] ?? $dataTrx['destinationNumber']) ?? "";
        $transaction->setAccountId($dataTrx['account_id'] ?? $dataTrx['accountId'] ?? 0);
        $transaction->setTransactionCode($dataTrx['transaction_code'] ?? $dataTrx['transactionCode'] ?? "");
        $transaction->setApiKey($dataTrx['api_key'] ?? $dataTrx['apiKey'] ?? 0);
        $transaction->setServiceItemsId($dataTrx['service_items_id'] ?? $dataTrx['serviceItemsId'] ?? 0);
        $transaction->setWalletId($dataTrx['wallet_id'] ?? $dataTrx['walletId'] ?? 0);
        $transaction->setSmsValidationStatus($dataTrx['sms_validation_status'] ?? $dataTrx['smsValidationStatus'] ?? "");
        $transaction->setValidationStatusCode($dataTrx['validation_status_code'] ?? $dataTrx['validationStatusCode'] ?? 0);
        $transaction->setExternalId($dataTrx['external_id'] ?? $dataTrx['externalId'] ?? "");
        $transaction->setErrorTypeId($dataTrx['error_type_id'] ?? $dataTrx['errorTypeId'] ?? 0);
        $transaction->setNewWalletAmount($dataTrx['new_wallet_amount'] ?? $dataTrx['newWalletAmount'] ?? 0);
        $transaction->setNewAccountAmount($dataTrx['new_account_amount'] ?? $dataTrx['newAccountAmount'] ?? 0);
        $transaction->setCustomData($dataTrx['custom_data'] ?? $dataTrx['customData'] ?? "");
        $transaction->setJsonData($dataTrx['json_data'] ?? $dataTrx['jsonData'] ?? "");
        $transaction->setMetaData($dataTrx['meta_data'] ?? $dataTrx['metaData'] ?? "");
        $transaction->setCreatedAt($dataTrx['created_at'] ? new \DateTime($dataTrx['created_at']) : new \DateTime($dataTrx['createdAt']));
        $transaction->setUpdatedAt($dataTrx['updated_at'] ? new \DateTime($dataTrx['updated_at']) : new \DateTime($dataTrx['updatedAt']));
        $transaction->setChecked($dataTrx['checked'] ?? null);
        $transaction->setIsFromAutoRecharge($dataTrx['is_from_auto_recharge'] ?? $dataTrx['isFromAutoRecharge'] ?? null);

        $this->_entity_manager->persist($transaction);
        $this->_entity_manager->flush();

        $transaction = $this->serialize->serialize($transaction, 'json', ['groups' => 'transaction']);
        $transaction = json_decode($transaction, true);

        return $transaction;
    }

    /**
     *@Route("/facturation-pdf-trx", name="facturation_pdf_trx")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function facturationPdfTrx(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $param = $request->query->get('infosTrx');
        $dataProduit = json_decode($param, true);

        return $this->render('FrontOffice/transfert/facturation.html.twig', [
            'dataProduit' => $dataProduit
        ]);
    }
}
