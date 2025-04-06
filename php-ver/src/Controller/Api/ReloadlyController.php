<?php

namespace App\Controller\Api;

use App\Repository\TransfertRepository;
use App\Service\UploadFileService;
use App\Service\Api\ServiceReloadly;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReloadlyController extends AbstractController
{
    private $_reloadly_manager;
    private $_entity_manager;
    private $_uploadFileService;
    private $_transfert_repo;

    public function __construct(
        EntityManagerInterface $_entity_manager,
        UploadFileService $_uploadFileService,
        ServiceReloadly $_reloadly_manager,
        TransfertRepository $_transfert_repo
    )
    {
        $this->_entity_manager    = $_entity_manager;
        $this->_uploadFileService = $_uploadFileService;
        $this->_reloadly_manager  = $_reloadly_manager;
        $this->_transfert_repo    = $_transfert_repo;
    }


    /**
     * @Route("/liste-operateur-pays", name="liste_operateur_pays")
     */
    public function listeOperateurPays(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $code = $request->get('code');

        $_json  = $this->_reloadly_manager->getAccessToken();
        $_token = json_decode($_json[0]);
        //$_account   = $this->_reloadly_manager->getAccountBalance($_token->access_token);
        $_operators = $this->_reloadly_manager->getOperatorByIso($_token->access_token, $code);
        $_list      = ($_operators[1]) ? null : json_decode($_operators[0]);
    
        return new JsonResponse([
            'operateurs' => $_list
        ]);

    }

    /**
     * @Route("/liste-offre-operateur-pays", name="liste_offre_operateur_pays")
     */
    public function listeOffreOperateur(Request $request){
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $code   = $request->get('code');
        $prefix = $request->get('prefix');
        $numero = $request->get('numero');
        
        $tel = $prefix . $numero;

        $_json  = $this->_reloadly_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        $_offres = $this->_reloadly_manager->getOperatorAutoDetect($_token->access_token, $tel, $code); //329055113
        $_list   = ($_offres[1]) ? null : json_decode($_offres[0]);

        return new JsonResponse([
            'offre_operateurs' => $_list
        ]);
    }

    /**
     * @Route("/ajout-recharge-reloadly", name="new_recharge_reloadly")
     */
    public function ajoutRechargeReloadly(Request $request){
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_reloadly_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        $user = $this->getUser();
        
        $operator = $request->get('operator');
        $offre    = $request->get('offre');
        $country  = $request->get('country');
        $number   = $request->get('number');

        $identifiant = uniqid('Identifiant', true);

        $payload = [
            "operatorId"       => $operator["id"],
            "amount"           => $offre["tarifbalance"],
            "useLocalAmount"   => false,
            "customIdentifier" => $identifiant,
            "recipientEmail"   => $user->getEmail(),
            "recipientPhone"   => [
                "countryCode"  => $country["code"],
                "number"       => $number,
            ],
            "senderPhone" => [
                "countryCode" => "MA",
                "number"      => "+212678475113"
            ]
        ];
        
        $_transaction = $this->_reloadly_manager->postTopUp($_token->access_token, $payload);

        $_retour = ($_transaction[1]) ? null : json_decode($_transaction[0]);
        return new JsonResponse([
            'transaction' => $_retour,
        ]);
    }

    /**
     * @Route("/status-recharge-reloadly", name="status_recharge_reloadly")
     */
    public function statusrechargement(){
         if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_reloadly_manager->getAccessToken();
        $_token = json_decode($_json[0]);
        
        $_transaction = $this->_reloadly_manager->getTopUpStatus($_token->access_token,'118412');

        $_retour = ($_transaction[1]) ? null : json_decode($_transaction[0]);
        return new JsonResponse([
            'transaction' => $_retour,
        ]);
    }
    

}