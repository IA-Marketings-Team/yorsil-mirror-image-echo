<?php

namespace App\Controller\Api;

use App\Entity\Credit;
use App\Entity\Notificationrechargement;
use App\Repository\BoutRepository;
use App\Repository\CreditRepository;
use App\Service\Api\ServiceSog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SogController extends AbstractController
{
    private $_sog_manager;
    private $_entity_manager;

    public function __construct(
        EntityManagerInterface $_entity_manager,
        ServiceSog $_sog_manager,
        BoutRepository $boutRepository,
        CreditRepository $creditRepository
    )
    {
        $this->_entity_manager  = $_entity_manager;
        $this->_sog_manager     = $_sog_manager;
        $this->boutRepository   = $boutRepository;
        $this->creditRepository = $creditRepository;
    }

    /**
     * @Route("/paiement-form", name="paiement_form")
     * @IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function paiementForm(Request $request, SessionInterface $session): Response
    {   

        $ip = $request->getClientIp();

	// IP d'Abdelhak et Mimoun, svp les gars ne touchez pas cela.
        if ($ip != "91.173.81.170" && $ip != "37.64.127.218" && $ip != "102.68.192.186") {
            die("<h3>Cette page est en maintenance, veuillez nous excuser du dérangement.</h3>");
        }

        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('FrontOffice/sog/index.html.twig');
    }

    /**
     * @Route("/creer-paiement", name="creer_paiement")
     * @IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function creationPaiement(Request $request,SessionInterface $session): Response
    {   

        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_last_recharge = $this->creditRepository->findOneByType('2');
        $_value_type    = 'CB';

        if ($_last_recharge) {
            $_last_number = (int)substr($_last_recharge->getRef(), 4);
            $_new_number  = $_last_number + 1;
        } else {
            $_new_number = 1;
        }
        $new_code = $_value_type . str_pad($_new_number, 3, '0', STR_PAD_LEFT);

        $amount = $request->request->get("montant");
        $_auth  = $this->_sog_manager->postAuth();
        $_token = ($_auth[1]) ? null : json_decode($_auth[0]);
        $_form_token = ($_token) ? $this->_sog_manager->createFormToken($amount,$new_code,$this->getUser()->getEmail()) : null;
        $_formulaire = ($_form_token[1]) ? null : json_decode($_form_token[0]);
        
        $session->set('formToken',$_formulaire->answer->formToken);

        return $this->render('FrontOffice/sog/credit.html.twig');
    }

    public function checkHash($data, $key)
    {
	//var_dump($_POST);    
	$supported_sign_algos = array('sha256_hmac');
        if (!in_array($data['kr-hash-algorithm'], $supported_sign_algos)) {
            return false;
        }
        $kr_answer = str_replace('\/', '/', $data['kr-answer']);
        $hash = hash_hmac('sha256', $kr_answer, $key);
	//file_put_contents("/tmp/calculated_hmac.txt", $_POST . "\n", FILE_APPEND | LOCK_EX);
	return ($hash == $data['kr-hash']);
    }

    // Cela doît être bougé d'ici.
    // Mimoun 27/12/2024
    private function isSogecommerceBot(Request $request) {
	$ip = $request->getClientIp();
	$agent = $request->headers->get('User-Agent');

	return str_starts_with($ip, "194.50.38.") && $agent == "Lyra-Network Agent";
    }

    /**
     * @Route("/paiement-succes", name="paiement_succes")
     */
    public function paiementSucces(Request $request,SessionInterface $session): Response
    {
	
	//if (!$this->isSogecommerceBot($request) || !$this->getUser())
        //{
        //    return $this->redirectToRoute('app_login');
        //}

	/* DEBUG FUNCTION */
	$logFile = '/tmp/request_log.txt';

	$clientIp = $request->getClientIp();

	//$dataJson = json_encode($data);
	$dataJson = json_encode($request->request->all());

	$logMessage = sprintf(
		"-----------------------------------------------------\n[%s] Request from IP: %s with data: %s\n",
		    date('Y-m-d H:i:s'),
			$clientIp,
			    $dataJson
	);

	file_put_contents($logFile, $logMessage, FILE_APPEND);
	/* DEBUG FUNCTION END*/

	$data = $request->request->all();
	//$data = json_decode($request->getContent());
	// PROD.
	//$shaKey = 'dgnmlo4TvmXCSuSnS8Cr2ZN1M4gQEXWFU9xmCd5P8RTuL';
	// TEST.
	$shaKey = 'TA2TsYbhFBgapy20Bte24O2ywcJGFZ6cNVmhRvvu8YolN';

        if (!$this->checkHash($data, $shaKey)) {
            // Redirigez ou renvoyez une réponse avec un message d'erreur.
            return new Response('Signature invalide.', 400);
        }

        $json      = json_decode($data['kr-answer'], true);
        $status    = $json['orderStatus'];
        $ref       = $json['orderDetails']['orderId'];
        $montant   = ((float)$json['orderDetails']['orderTotalAmount']) / 100; 
        $_boutique = $this->boutRepository->findOneBy(['user' => $this->getUser()]);
        if($status == 'PAID'){
            $_credit = new Credit();
            $_credit->setDate(new \Datetime());
            $_credit->setMontant((float)$montant);
            $_credit->setBout($_boutique);
            $_credit->setType('2');
            $_credit->setRef($ref);
            $_credit->setIsvalid(1);

            $this->_entity_manager->persist($_credit);
            $this->_entity_manager->flush();

            $_message_notif = "Rechargement de ".$montant."€ par Cartes Bancaire effectuée par la boutique ".$_boutique->getNom();
            $notification = new Notificationrechargement();
            $notification->setBoutique($_boutique);
            $notification->setDate(new \DateTime());
            $notification->setCredit($_credit);
            $notification->setMessage($_message_notif);
            $notification->setIsAdmin(true);

            $this->_entity_manager->persist($notification);
            $this->_entity_manager->flush();

        }
        
        if($status == 'PAID'){
            $this->addFlash(
                'success',
                "Rechargement effectué avec succès"
            );
            return $this->redirectToRoute('journal_rechargement');
        }

        $this->addFlash(
            'danger',
            "Rechargement non effectué"
        );
        return $this->redirectToRoute('journal_rechargement');
        // return $this->render('FrontOffice/sog/succes.html.twig', [
        //     'answer' => [
        //         'kr-hash' => $data['kr-hash'],
        //         'kr-hash-algorithm' => $data['kr-hash-algorithm'],
        //         'kr-answer-type' => $data['kr-answer-type'],
        //         'kr-answer' => json_decode($data['kr-answer'], true),
        //     ],
        // ]);
    }

}
