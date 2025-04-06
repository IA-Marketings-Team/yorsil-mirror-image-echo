<?php

namespace App\Controller\BackOffice;

use App\Controller\Api\AledaController;
use App\Controller\Api\DingController;
use App\Repository\AccountRepository;
use App\Repository\BoutRepository;
use App\Repository\FlixbusRepository;
use App\Repository\RechargeRepository;
use App\Repository\RechargeflexiRepository;
use App\Repository\TransfertRepository;
use App\Service\Api\ServiceReloadly;
use App\Service\Metier\ServiceCA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\Utils\Util;

class HomeController extends AbstractController
{
    private $_entity_manager;
    private $_boutRepo;
    private $_accountRepo;
    private $aledaController;
    private $dingController;
    private $service_reloadly;

    public function __construct(
        EntityManagerInterface $_entity_manager,
        BoutRepository $boutRepo,
        AccountRepository $_accountRepo,
        AledaController $aledaController,
        DingController $dingController,
        ServiceReloadly $service_reloadly,
        ServiceCA $serviceCA,
        FlixbusRepository $flixbusRepository,
        RechargeRepository $rechargeRepository,
        RechargeflexiRepository $rechargeflexiRepository,
        TransfertRepository $transfertRepository
    ) {
        $this->_entity_manager  = $_entity_manager;
        $this->_boutRepo        = $boutRepo;
        $this->_accountRepo     = $_accountRepo;
        $this->aledaController  = $aledaController;
        $this->dingController   = $dingController;
        $this->service_reloadly = $service_reloadly;
        $this->service_ca       = $serviceCA;
        $this->_flixbusRepository       = $flixbusRepository;
        $this->_rechargeRepository      = $rechargeRepository;
        $this->_rechargeflexiRepository = $rechargeflexiRepository;
        $this->_transfertRepository     = $transfertRepository;
    }

    public function getIpAdress()
    {
        $ip = '';
        try {
            $ip = system("curl -s https://ipecho.net/plain");
            //dd($this->httpClient->request('GET', 'https://ipecho.net/plain')->getContent());
        } catch (\Exception $e) {
            echo "An error occured when getting the public IP of the server.";
        }
        return $ip;
    }

    /**
     * @Route("/Accueil", name="home_back")
     */
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $ip   = $this->getIpAdress();

        if ($this->isGranted('ROLE_ADMIN')) {
            $user->setIpAdress($ip);
            $user->setDateLastLogin(new \DateTime());
            $this->_entity_manager->persist($user);
            $this->_entity_manager->flush();
            return $this->redirectToRoute('tableau_de_bord');
        } else {
            $is_actif = $this->getUser()->getIsActif();
            if (!$is_actif) {
                return $this->redirectToRoute('contact_admin');
            }
            $user->setIpAdress($ip);
            $user->setDateLastLogin(new \DateTime());
            $this->_entity_manager->persist($user);
            $this->_entity_manager->flush();
            $_slug = Util::slug($this->getUser()->getNom());
            return $this->redirectToRoute('home_office');
        }
    }

    /**
     * @Route("/tableau-de-bord", name="tableau_de_bord")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function dashboard()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $boutCompt = COUNT($this->_boutRepo->findAll());
        $account   = $this->_accountRepo->findOneBy([], ["id" => "DESC"]);
        $boutiques = $this->_boutRepo->findAll();

        $soldeAleda = $this->aledaController->getSoldeAgent();
        $soldeDing  = $this->dingController->soldeDing();

        $jsonContent = $soldeAleda->getContent();
        // Décoder le JSON pour accéder aux données
        $data = json_decode($jsonContent, true); // true pour un tableau associatif
        // Accéder à une valeur spécifique
        $solde = $data['solde'] ?? null;
        //$solde = 0;

        $_json  = $this->service_reloadly->getAccessToken();
        $_token = json_decode($_json[0]);
        $soldeReloadly = json_decode($this->service_reloadly->getAccountBalance($_token->access_token));
        $soldeReloadly = $soldeReloadly->balance;

        $soldeApi = [
            "aleda"    => $solde,
            "reloadly" => $soldeReloadly,
            "ding"     => $soldeDing
        ];

        // Commission 
        $_flix = $this->_flixbusRepository->findBy([], ["id" => "DESC"]);
        $_recharge = $this->_rechargeRepository->findBy([], ["id" => "DESC"]);
        $_diaspo = $this->_rechargeflexiRepository->findBy([], ["id" => "DESC"]);
        $_transfert = $this->_transfertRepository->findBy([], ["id" => "DESC"]);

        // Chiffre d'affaires
        $_ca_flix      = [$this->_flixbusRepository->findCAByDays(),$this->_flixbusRepository->findCAByMonth(),$this->_flixbusRepository->findCAAll()];
        $_ca_recharge  = [$this->_rechargeRepository->findCAByDays(),$this->_rechargeRepository->findCAByMonth(),$this->_rechargeRepository->findCAAll()];
        $_ca_transfert = [($this->_transfertRepository->findCAByDays()+$this->_rechargeflexiRepository->findCAByDays()),($this->_transfertRepository->findCAByMonth()+$this->_rechargeflexiRepository->findCAByMonth()),($this->_transfertRepository->findCAAll()+$this->_rechargeflexiRepository->findCAAll())];

        $entities = array_merge($_flix, $_recharge, $_transfert, $_diaspo);

        $_comm = $this->service_ca->formatCA($entities);

        return $this->render('BackOffice/home/index.html.twig', [
            'nbrBoutique' => $boutCompt,
            'account'     => $account,
            'boutiques'   => $boutiques,
            'soldeApi'    => $soldeApi,
            'comm'          => $_comm['total'],
            'comm_flix'     => $_comm['ca_flix'],
            'comm_recharge' => $_comm['ca_recharge'],
            'comm_transfert' => $_comm['ca_transfert'],
            'ca_flix'     => $_ca_flix,
            'ca_recharge' => $_ca_recharge,
            'ca_transfert' => $_ca_transfert,

        ]);
    }

    /**
     * @Route("/contact-administrateur", name="contact_admin")
     */
    public function contactAdmin()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('FrontOffice/contact.html.twig');
    }
}
