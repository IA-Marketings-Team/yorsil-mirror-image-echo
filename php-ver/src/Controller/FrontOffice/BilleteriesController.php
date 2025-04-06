<?php

namespace App\Controller\FrontOffice;

use App\Entity\Debit;
use App\Entity\Flixbus;
use App\Entity\Rechargeflexi;
use App\Repository\BoutRepository;
use App\Repository\CreditRepository;
use App\Repository\FlixbusRepository;
use App\Repository\FraiserviceboutiqueRepository;
use App\Repository\FraiserviceRepository;
use App\Repository\PaysRepository;
use App\Service\Api\ServiceFlixBus;
use App\Service\Metier\ServiceMetierBoutique;
use DateTime;
use DateTimeZone;
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
use Symfony\Component\Validator\Constraints\Length;

class BilleteriesController extends AbstractController
{

	private $_entity_manager;
    private $_flixbus_manager;
    private $_bout_repo;
    private $_credit_repo;
    private $_flix_repo;
    private $_boutique_manager;
    private $_frais_service_repo;
    private $_frais_service_bout_repo;

	public function __construct( EntityManagerInterface $_entity_manager,
                                ServiceFlixBus $_flixbus_manager,
                                BoutRepository $_bout_repo,
                                CreditRepository $_credit_repo,
                                FlixbusRepository $_flix_repo,
                                ServiceMetierBoutique $_boutique_manager,
                                FraiserviceRepository $_frais_service_repo,
                                FraiserviceboutiqueRepository $_frais_service_bout_repo ){
	    $this->_entity_manager          = $_entity_manager;
        $this->_flixbus_manager         = $_flixbus_manager;
        $this->_bout_repo               = $_bout_repo;
        $this->_credit_repo             = $_credit_repo;
        $this->_flix_repo               = $_flix_repo;
        $this->_boutique_manager        = $_boutique_manager;
        $this->_frais_service_repo      = $_frais_service_repo;
        $this->_frais_service_bout_repo = $_frais_service_bout_repo;
    }

    /**
     *@Route("/billeteries", name="billeteries")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function index(SessionInterface $session)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('FrontOffice/billeteries/index.html.twig');
    }

    /**
     *@Route("/flixBus", name="flixBus")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function flixBus(SessionInterface $session)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_flixbus_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Session pour le token
        $session->set('token', $_token->token);

        $_cities = $this->_flixbus_manager->getCities();
        $_cities = ($_cities[1]) ? null : json_decode($_cities[0]);

        $_stations = $this->_flixbus_manager->getStations();
        $_stations = ($_stations[1]) ? null : json_decode($_stations[0]);

        return $this->render('FrontOffice/billeteries/flixBus.html.twig', [
            "cities" => $_cities,
            "stations" => $_stations
        ]);
    }

    /**
     *@Route("/listes-trajet", name="listes_trajet")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function listesTrajet(Request $request, SessionInterface $session)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $trajet = $request->request->get('trajet');
        $trajet = json_decode($trajet, true);

        $passeport = $request->request->get('passeport', null);
        $passeport = json_decode($passeport, true);

        $suggest = $request->request->get('suggest', null);
        $suggest = json_decode($suggest, true);

        if ($suggest === null) {
            $sessionSuggest = $session->get('suggest');
            if ($sessionSuggest === null) {
                return $this->redirectToRoute('flixBus');
            }

            $suggest = $sessionSuggest;
        } else {
            $session->set('suggest', $suggest);
        }
        
        if ($trajet === null) {
            $sessionTrajet = $session->get('trajet');
            if ($sessionTrajet === null) {
                return $this->redirectToRoute('flixBus');
            }

            $trajet = $sessionTrajet;
        } else {
            $session->set('trajet', $trajet);
        }

        $dataTrajet = $request->request->get('data');
        $dataTrajet = json_decode($dataTrajet, true);
        
        if ($dataTrajet === null) {
            $sessionDataTrajet = $session->get('dataTrajet');
            if ($sessionDataTrajet === null) {
                return $this->redirectToRoute('flixBus');
            }

            $dataTrajet = $sessionDataTrajet;
        } else {
            $session->set('dataTrajet', $dataTrajet);
        }

        $_json  = $this->_flixbus_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Session pour le token
        $session->set('token', $_token->token);

        $_cities = $this->_flixbus_manager->getCities();
        $_cities = ($_cities[1]) ? null : json_decode($_cities[0]);

        $_stations = $this->_flixbus_manager->getStations();
        $_stations = ($_stations[1]) ? null : json_decode($_stations[0]);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                "dataTrajet" => $dataTrajet,
                'nombreTrajet' => $this->renderView('FrontOffice/billeteries/NombreTrajet.html.twig', [
                    "trajets" => $trajet,
                ]),
                'listesTrajetCards' => $this->renderView('FrontOffice/billeteries/listesTrajetCards.html.twig', [
                    "dataTrajet" => $dataTrajet,
                    "trajets" => $trajet,
                    "suggest" => $suggest
                ])
            ]);
        }

        return $this->render('FrontOffice/billeteries/trajet.html.twig', [
            "dataTrajet" => $dataTrajet,
            "trajets"    => $trajet,
            "suggest"    => $suggest,
            "cities"     => $_cities,
            "stations"   => $_stations,
            "passeport"  => $passeport
        ]);
    }

    /**
     *@Route("/listes-trajet-retour", name="listes_trajet_retour")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function listesTrajetRetour(Request $request, SessionInterface $session)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $trajet = $request->request->get('trajet');
        $trajet = json_decode($trajet, true);

        $passeport = $request->request->get('passeport', null);
        $passeport = json_decode($passeport, true);

        $suggest = $request->request->get('suggest', null);
        $suggest = json_decode($suggest, true);

        if ($suggest === null) {
            $sessionSuggest = $session->get('suggest');
            if ($sessionSuggest === null) {
                return $this->redirectToRoute('flixBus');
            }
            $suggest = $sessionSuggest;
        } else {
            $session->set('suggest', $suggest);
        }
        
        if ($trajet === null) {
            $sessionTrajet = $session->get('trajet');
            if ($sessionTrajet === null) {
                return $this->redirectToRoute('flixBus');
            }
            $trajet = $sessionTrajet;
        } else {
            $session->set('trajet', $trajet);
        }

        $dataTrajet = $request->request->get('data');
        $dataTrajet = json_decode($dataTrajet, true);
        
        if ($dataTrajet === null) {
            $sessionDataTrajet = $session->get('dataTrajet');
            if ($sessionDataTrajet === null) {
                return $this->redirectToRoute('flixBus');
            }
            $dataTrajet = $sessionDataTrajet;
        } else {
            $session->set('dataTrajet', $dataTrajet);
        }

        $_json  = $this->_flixbus_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Session pour le token
        $session->set('token', $_token->token);

        $_cities = $this->_flixbus_manager->getCities();
        $_cities = ($_cities[1]) ? null : json_decode($_cities[0]);

        $_stations = $this->_flixbus_manager->getStations();
        $_stations = ($_stations[1]) ? null : json_decode($_stations[0]);

        return new JsonResponse([
            "dataTrajet" => $dataTrajet,
            'nombreTrajet' => $this->renderView('FrontOffice/billeteries/NombreTrajet.html.twig', [
                "trajets" => $trajet,
            ]),
            'listesTrajetCards' => $this->renderView('FrontOffice/billeteries/listesTrajetCards.html.twig', [
                "dataTrajet" => $dataTrajet,
                "trajets" => $trajet,
                "suggest" => $suggest,
                "passeport" => $passeport,
                "retour" => true
            ])
        ]);
    }

    /**
     *@Route("/checkout", name="checkout")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function checkout(Request $request, SessionInterface $session, PaysRepository $listeCountry)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $reservation = $request->request->get('reservation');
        $reservation = json_decode($reservation, true);

        $passeport = $request->request->get('passeport');
        $passeport = json_decode($passeport, true);

        $typeTrajet = $request->request->get('typeTrajet');

        if (!$reservation['result']) {
            $sessionReservation = $session->get('reservation');
            if ($sessionReservation === null) {
                $this->addFlash(
                    'warning',
                    "Reservation expiré"
                );
                return $this->redirectToRoute('flixBus');
            }

            $reservation = $sessionReservation;
        } else {
            $session->set('reservation', $reservation);
        }

        $countries = $listeCountry->findAll();

        $countries = array_map(function ($pays) {
            return [
                'nom' => $pays->getNom(),
                'code' => $pays->getCode()
            ];
        }, $countries);

        return $this->render('FrontOffice/billeteries/checkout.html.twig', [
            'reservation' => $reservation,
            'typeTrajet'  => $typeTrajet,
            'passeport'   => $passeport,
            'countries'  => $countries
        ]); 
    }

    /**
     * @Route("/verif-sold-bout-flix", name="verif_sold_bout_flix")
     */
    public function verifSolde(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_bout = $this->getUser();

        $tarif = (float)$request->request->get('tarif');

        $_credit = $this->_boutique_manager->creditBoutique($_bout);
        $_debit  = $this->_boutique_manager->debitBoutique($_bout);
        $_geste  = $this->_boutique_manager->gesteBoutique($_bout);
        $_solde  = (float)($_credit + $_geste - $_debit);

        if ($tarif > $_solde) {
            return new JsonResponse([
                'solde' => false,
                'message' => 'Votre solde actuel ne permet pas de réaliser cette transaction. Veuillez recharger votre compte.',
                'infos' => "$tarif > $_solde"
            ]);
        }

        return new JsonResponse([
            'solde' => $_solde,
            'message' => 'ok',
        ]);
    }

    /**
     * @Route("/ajout-reservation-flix", name="debit_reservation_flix")
     */
    public function saveReservation(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $bout  = $this->_bout_repo->findOneBy(["user" => $user]);

        $tarif = (float)$request->request->get('tarif');

        $typeTrajet = $request->request->get('typeTrajet');

        $tickets = $request->request->get('tickets');

        $downloadHash = $session->get('download_hash');

        if ($tickets) {
            $orderId = $tickets["tickets"]["order"]["id"];
            $descriptions = [
                "lien_rappel" => $tickets["tickets"]["order"]["reminder_link"],
                "download_hash" => $downloadHash
            ];
        } else {
            $orderId = (float)$session->get('order_id');
            $descriptions = [
                "download_hash" => $downloadHash
            ];
        }


        $email = $request->request->get('email');
        $tel = $request->request->get('tel');

        $reservation = $session->get('reservation');
        $items = $reservation['cart']['items'];

        $reservationId = $reservation['reservation']['id'];
        $prixService = $reservation['cart']['price']['service_fee'];
        $prixTotal = $reservation['cart']['price']['value'];

        $fraisService = $this->_frais_service_repo->findOneByType("1");
        $fraisServiceBout = $this->_frais_service_bout_repo->findOneBy(["boutique" => $bout,"type" => "1"]);

        $frais = $fraisService->getPourcentage();

        if ($fraisServiceBout) {
            $fraisBoutique = (float)$fraisServiceBout->getPourcentage();
        } else {
            $fraisBoutique = (float)$fraisService->getPourcentageBoutique();
        }

        $pourcentageBoutique = $tarif * ($fraisBoutique / 100);

        $debitSold = $tarif - $pourcentageBoutique;
        
        foreach ($items as $key => $item) {
            $reserveFixBus = new Flixbus();
            // Conversion de l'heure d'arrivée
            $arrivalTimestamp = $item['arrival']['timestamp'];
            $arrivalTimeZone = $item['arrival']['tz'];
            $arrivalDateTime = new DateTime('@' . $arrivalTimestamp);
            $arrivalDateTime->setTimezone(new DateTimeZone($arrivalTimeZone));

            // Conversion de l'heure de départ
            $departureTimestamp = $item['departure']['timestamp'];
            $departureTimeZone = $item['departure']['tz'];
            $departureDateTime = new DateTime('@' . $departureTimestamp);
            $departureDateTime->setTimezone(new DateTimeZone($departureTimeZone));
            
            // from
            $stationDepart  = $item['from']['name'];
            $stationArriver = $item['to']['name'];

            // montant
            $montan  = $item['price']['total'];
            
            // nbr passagers nbrPassagers
            $nbrPassagers = $item['reserved'];

            $reserveFixBus->setBoutique($bout);
            $reserveFixBus->setReservationId($reservationId);
            $reserveFixBus->setDateDepart($departureDateTime);
            $reserveFixBus->setDateArriver($arrivalDateTime);
            $reserveFixBus->setStationDepart($stationDepart);
            $reserveFixBus->setStationArriver($stationArriver);
            $reserveFixBus->setTypeTrajet($typeTrajet);
            $reserveFixBus->setMontantService($prixService);
            $reserveFixBus->setMontantTotal($prixTotal);
            $reserveFixBus->setNbrePassagers($nbrPassagers);
            $reserveFixBus->setEmail($email);
            $reserveFixBus->setTel($tel);
            $reserveFixBus->setOrderId($orderId);
            $reserveFixBus->setMontant($montan);
            $reserveFixBus->setDescription($descriptions);
            $reserveFixBus->setDateResa(new DateTime());
            $reserveFixBus->setFrais($frais);
            $reserveFixBus->setFraisBoutique($pourcentageBoutique);

            $this->_entity_manager->persist($reserveFixBus);
        };

        $_credit = $this->_boutique_manager->creditBoutique($user);
        $_debit  = $this->_boutique_manager->debitBoutique($user);
        $_geste  = $this->_boutique_manager->gesteBoutique($user);
        $sold_Bout = (float)($_credit + $_geste - $_debit);

        $new_solde = $sold_Bout - $debitSold;

        $_desc = "Réservation de $tarif €";

        $_debits = new Debit();
        $_debits->setDate(new \Datetime());
        $_debits->setMontant($debitSold);
        $_debits->setBout($bout);
        $_debits->setAdmin(null);
        $_debits->setNote($_desc);
        $this->_entity_manager->persist($_debits);
        $this->_entity_manager->flush();

        return new JsonResponse([
            'solde' => $new_solde,
            'message' => 'Votre réservation a bien été enregistrée. Nous vous enverrons les détails par email.',
        ]);
    }

    /**
     *@Route("/historique-reservation", name="hist_reservation")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function HistReservation(BoutRepository $boutRepo)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $boutique = $boutRepo->findOneBy(['user' => $this->getUser()]);
        $reservation = $this->_flix_repo->findBy(['boutique' => $boutique],["id" => "DESC"]);

        return $this->render('FrontOffice/billeteries/hist.html.twig', [
            'reservations' => $reservation
        ]);
    }

    /**
     *@Route("/gerer-reservation", name="gerer_reservation")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function gererReservation()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('FrontOffice/billeteries/gererReservation.html.twig');
    }

    /**
     *@Route("/search-gerer-reservation", name="search_gerer_reservation")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function searchGererReservation(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $orderId = $request->request->get('orderId');
        $emailOrTel    = $request->request->get('emailOrTel');

        if (filter_var($emailOrTel, FILTER_VALIDATE_EMAIL)) {
            $reservation = $this->_flix_repo->findBy([
                "order_id" => $orderId,
                "email" => $emailOrTel
            ]);
        } else {
            $reservation = $this->_flix_repo->findBy([
                "order_id" => $orderId,
                "tel" => $emailOrTel
            ]);
        }

        if($reservation) {

            $reservationListe = [];
            
            for ($i=0; $i < COUNT($reservation); $i++) {
                $item = [
                    "id" => $reservation[$i]->getReservationId(),
                    "orderId" => $reservation[$i]->getOrderId(),
                    "dateDepart" => $reservation[$i]->getDateDepart()->format('d-m-Y'),
                    "dateArriver" => $reservation[$i]->getDateArriver()->format('d-m-Y'),
                    "heureDepart" => $reservation[$i]->getDateDepart()->format('H:i'),
                    "heureArriver" => $reservation[$i]->getDateArriver()->format('H:i'),
                    "stationDepart" => $reservation[$i]->getStationDepart(),
                    "stationArriver" => $reservation[$i]->getStationArriver(),
                    "type" => $reservation[$i]->getTypeTrajet(),
                    "montant" => $reservation[$i]->getMontant(),
                    "montantTotal" => $reservation[$i]->getMontantTotal(),
                    "montantService" => $reservation[$i]->getMontantService(),
                    "passager" => $reservation[$i]->getNbrePassagers(),
                    "description" => $reservation[$i]->getDescription(),
                ];
                array_push($reservationListe, $item);
            };
            
            return new JsonResponse([
                'reservation' => $reservationListe
            ]);
        }

        return new JsonResponse([
            'reservation' => false
        ]);
    }

    /**
    * Format nbre passagers
    */
    public function nbrPassagers(array $reserved): string
    {
        $parts = [];

        // Gestion des adultes
        if ($reserved['adult'] > 0) {
            $adultLabel = $reserved['adult'] > 1 ? 'adultes' : 'adulte';
            $parts[] = $reserved['adult'] . " " . $adultLabel;
        }

        // Gestion des enfants
        if ($reserved['children'] > 0) {
            $childrenLabel = $reserved['children'] > 1 ? 'enfants' : 'enfant';
            $parts[] = $reserved['children'] . " " . $childrenLabel;
        }

        // Gestion des vélos (facultatif, puisqu'il semble que vous ne vouliez pas l'afficher si à 0)
        if ($reserved['bike_slot'] > 0) {
            $bikeLabel = $reserved['bike_slot'] > 1 ? 'vélos' : 'vélo';
            $parts[] = $reserved['bike_slot'] . " " . $bikeLabel;
        }

        // Assemblage des parties en une seule chaîne
        return implode(', ', $parts);
    }

}
