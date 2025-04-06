<?php

namespace App\Controller\FrontOffice;

use App\Service\Metier\ServiceCA;
use App\Repository\BoutRepository;
use App\Repository\FlixbusRepository;
use App\Repository\RechargeRepository;
use App\Repository\RechargeflexiRepository;
use App\Repository\TransfertRepository;
use App\Controller\Api\AledaController;
use App\Service\Metier\ServiceMetierBoutique;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController extends AbstractController
{

    public function __construct(
            ServiceCA $serviceCA,
            BoutRepository $boutRepo,
            FlixbusRepository $flixbusRepository,
            RechargeRepository $rechargeRepository,
            RechargeflexiRepository $rechargeflexiRepository,
            TransfertRepository $transfertRepository
    )
    {
        $this->service_ca               = $serviceCA;
        $this->_bout_repository         = $boutRepo;
        $this->_flixbusRepository       = $flixbusRepository;
        $this->_rechargeRepository      = $rechargeRepository;
        $this->_rechargeflexiRepository = $rechargeflexiRepository;
        $this->_transfertRepository     = $transfertRepository;
    }
 
    /**
     * @Route("/mon-compte", name="home_office")
     */
    public function dashboard(ServiceMetierBoutique $serv, AledaController $aleda, SessionInterface $session)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('tableau_de_bord');
        } else if ($this->isGranted('ROLE_PERC')) {
            return $this->redirectToRoute('dashboard_percepteur');
        }

        $_boutique = $this->_bout_repository->findOneBy(['user' => $this->getUser()]);

        // Chiffre d'affaire 
        $_flix_days   = $this->_flixbusRepository->findByDays($_boutique->getId());
        $_flix_months = $this->_flixbusRepository->findByMonths($_boutique->getId());
        $_flix_years  = $this->_flixbusRepository->findByYears($_boutique->getId());

        $_recharge_days   = $this->_rechargeRepository->findByDays($_boutique->getId());
        $_recharge_months = $this->_rechargeRepository->findByMonths($_boutique->getId());
        $_recharge_years  = $this->_rechargeRepository->findByYears($_boutique->getId());

        $_diaspo_days   = $this->_rechargeflexiRepository->findByDays($this->getUser()->getId());
        $_diaspo_months = $this->_rechargeflexiRepository->findByMonths($this->getUser()->getId());
        $_diaspo_years  = $this->_rechargeflexiRepository->findByYears($this->getUser()->getId());

        $_transfert_days   = $this->_transfertRepository->findByDays($_boutique->getId());
        $_transfert_months = $this->_transfertRepository->findByMonths($_boutique->getId());
        $_transfert_years  = $this->_transfertRepository->findByYears($_boutique->getId());

        $entities_days = array_merge($_flix_days, $_recharge_days, $_transfert_days, $_diaspo_days);
        $_ca_days = $this->service_ca->formatBoutiqueCA($entities_days);
        
        $entities_months = array_merge($_flix_months, $_recharge_months, $_transfert_months, $_diaspo_months);
        $_ca_months = $this->service_ca->formatBoutiqueCA($entities_months);
        
        $entities_years = array_merge($_flix_years, $_recharge_years, $_transfert_years, $_diaspo_years);
        $_ca_years = $this->service_ca->formatBoutiqueCA($entities_years);

        return $this->render('FrontOffice/homebout.html.twig',[
            'ca_days'   => $_ca_days['total'],
            'ca_months' => $_ca_months['total'],
            'ca_years'  => $_ca_years['total'],           
        ]);

    }
    /**
     * @Route("/matricule", name="matricule")
     * @IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function matricule(){
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('FrontOffice/matricule/index.html.twig');

    }

    /**
     * @Route("/matricule-detect", name="matricule_detect")
    */
    public function detect(Request $request){
        $return = '';
        $is_error ='';
        $username  = 'RakLandry';
        $regNumber = $request->get('matricule');
        //$regNumber = 'AA123BB';
        //$xmlData = file_get_contents("https://www.regcheck.org.uk/api/reg.asmx/Check?RegistrationNumber=".$regNumber."&username=".$username);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://www.regcheck.org.uk/api/reg.asmx/CheckFrance?RegistrationNumber=".$regNumber."&username=".$username,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        if($response == "Out of credit\r\n" || $response == "French Lookup failed\r\n"){
            $return = 'Immatriculation invalide';
            if($response == "Out of credit\r\n"){
                $return = 'Erreur de jeton';
            }
           $is_valid = false;
           return new JsonResponse([
                'status' => false,
                'message' => $return,
                'is_valid' => $is_valid,
            ]);
        }else{
            $xml=simplexml_load_string($response);
            $strJson = $xml->vehicleJson;
            $json = json_decode($strJson);
            $return = $json->Description;
            $is_valid = true;

            return new JsonResponse([
                'status' => true,
                'message' => 'Immatriculation valide',
                "matricule" => $return,
                'is_valid' => $is_valid,
            ]);
        }

        if($error){
            $is_error = $error ;
            
            return new JsonResponse([
                'message' => $error,
            ]);
        }

        //dd($return);

        curl_close($curl);

        return $this->render('FrontOffice/matricule/index.html.twig');
    }

}
