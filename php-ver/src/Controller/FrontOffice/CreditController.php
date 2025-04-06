<?php

namespace App\Controller\FrontOffice;

use App\Entity\Credit;
use App\Entity\Notificationrechargement;
use App\Repository\CreditRepository;
use App\Repository\BoutRepository;
use App\Service\UploadFileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class CreditController extends AbstractController
{
    private $_entity_manager;
    private $_uploadFileService;
    private $boutRepository;

    public function __construct(
        EntityManagerInterface $_entity_manager,
        UploadFileService $_uploadFileService,
        BoutRepository $boutRepository,
         CreditRepository $creditRepository
    ) {
        $this->_entity_manager = $_entity_manager;
        $this->_uploadFileService = $_uploadFileService;
        $this->boutRepository   = $boutRepository;
        $this->creditRepository = $creditRepository;
    }
    
    /**
     *@Route("/credit-compte", name="credit_compte")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function creditCompte(Request $request)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $type   = $request->request->get("checkPaye");
        $amount = (float)$request->request->get("montant");
        $preuve = $request->files->get('preuve');
        $typeCredit ='';
        $_value_type=($type == '3')? 'VIR': 'DEP';

        switch ($type) {
            case '3':
                $typeCredit = "Virement bancaire";
                break;

            case '4':
                $typeCredit = "Dépôt bancaire";
                break;
        }

        $_last_recharge = $this->creditRepository->findOneByType($type);
        if ($_last_recharge) {
            $_last_number = (int)substr($_last_recharge->getRef(), 4);
            $_new_number  = $_last_number + 1;
        } else {
            $_new_number = 1;
        }
        $new_code = $_value_type . str_pad($_new_number, 3, '0', STR_PAD_LEFT);

        $credit = new Credit();

        $_boutique = $this->boutRepository->findOneBy(['user' => $this->getUser()]);

        $credit->setDate(new \DateTime());
        $credit->setBout($_boutique);
        $credit->setMontant($amount);
        $credit->setNote($typeCredit);
        $credit->setType($type);
        $credit->setRef($new_code);
        // $credit->setIsvalid(null);

        if ($preuve) {
            $preuve_file = $this->_uploadFileService->saveFile($preuve, "credit_compte", "/uploads/images/rechargement/$typeCredit/");
            if ($preuve_file) $credit->setFile($preuve_file);
        }

        $this->_entity_manager->persist($credit);
        $this->_entity_manager->flush();

        $_message_notif = "Rechargement de ".$amount."€ par ".$typeCredit." effectuée par la boutique ".$_boutique->getNom();
        $notification = new Notificationrechargement();
        $notification->setBoutique($_boutique);
        $notification->setDate(new \DateTime());
        $notification->setCredit($credit);
        $notification->setMessage($_message_notif);
        $notification->setIsAdmin(true);

        $this->_entity_manager->persist($notification);
        $this->_entity_manager->flush();


        $this->addFlash(
            'success',
            "Rechargement effectué avec succès"
        );

        return $this->redirectToRoute('journal_rechargement'); //paiement_form
    }

    /**
     * @Route("/validation-rechargement", name="validation_rechargement")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
    */
    public function validationRechargement(Request $request){

        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_id           = $request->request->get('rechargement_id');
        $_validation   = ($request->request->get('validation') == 'oui') ? 1 : 0; 
        $_rechargement =  $this->creditRepository->findOneBy(['id' => $_id]);
        $_admin        = $this->getUser();

        $_message = ( $_validation == 0 ) ? "Le rechargement de ".$_rechargement->getMontant()."€ effectuée par la boutique ".$_rechargement->getBout()->getNom()." a été rejeté" : "Le rechargement de ".$_rechargement->getMontant()."€ effectuée par la boutique ".$_rechargement->getBout()->getNom()." a été validé"; 

        $_message_notif = ( $_validation == 0 ) ? "Le rechargement de ".$_rechargement->getMontant()."€ que vous avez effectué a été rejeté" : "Le rechargement de ".$_rechargement->getMontant()."€ vous avez effectué a été validé"; 

        $_rechargement->setAdmin($_admin);
        $_rechargement->setIsvalid($_validation);
        $this->_entity_manager->flush();

        $notification = new Notificationrechargement();
        $notification->setBoutique($_rechargement->getBout());
        $notification->setCredit($_rechargement);
        $notification->setDate(new \DateTime());
        $notification->setMessage($_message_notif);
        $notification->setIsAdmin(false);

        $this->_entity_manager->persist($notification);
        $this->_entity_manager->flush();

        $this->addFlash(
            'success',
            "{$_message}"
        );

        return $this->redirectToRoute('credit');
    }

        /**
     * @Route("{id}/archivage-rechargement", name="suppression_rechargement")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
    */
    public function suppressionRechargement(Request $request,String $id){

        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_rechargement =  $this->creditRepository->findOneBy(['id' => $id]);
        // $_rechargement->setIsdelete(true);
        
        $this->_entity_manager->flush();
        $this->addFlash(
            'success',
            "Mise en archive effectuée avec succès"
        );

        return $this->redirectToRoute('credit');
    }
}