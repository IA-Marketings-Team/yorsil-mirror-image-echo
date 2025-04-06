<?php

namespace App\Controller\Api;

use App\Service\Api\ServiceFlixBus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FlixBusController extends AbstractController
{
    private $_flixbus_manager;
    private $_entity_manager;

    public function __construct(
        EntityManagerInterface $_entity_manager,
        ServiceFlixBus $_flixbus_manager
    ) {
        $this->_entity_manager  = $_entity_manager;
        $this->_flixbus_manager = $_flixbus_manager;
    }

    /**
     * @Route("/liste-villes", name="liste_ville")
     */
    public function listeVille(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_flixbus_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Session pour le token
        $session->set('token', $_token->token);

        $_cities = $this->_flixbus_manager->getCities();
        $_list   = ($_cities[1]) ? null : json_decode($_cities[0]);

        return new JsonResponse([
            'villes' => $_list
        ]);
    }

    /**
     * @Route("/liste-station-par-villes", name="liste_station_ville")
     */
    public function listeStationVille(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // $_json  = $this->_flixbus_manager->getAccessToken();
        // $_token = json_decode($_json[0]);
        $_stations = $this->_flixbus_manager->getStations();
        $_list     =  ($_stations[1]) ? null : json_decode($_stations[0]);

        return new JsonResponse([
            'stations' => $_list
        ]);
    }

    /**
     * @Route("/liste-recherche-voyage", name="liste_recherche_voyage")
     */
    public function listeTrajetVille(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Soit stations ou cities
        $_type_recherche = $request->request->get('type');

        // Soit id stations ou id cities 
        // Exemple id cities 88/94
        $_id_depart = $request->request->get('depart');
        $_id_arrive = $request->request->get('arrive');

        // Format d.m.Y , la date arrivé est nullable (n'est pas forcément requis)
        $_date_depart = $request->request->get('dateDepart');
        $_date_arrive = $request->request->get('dateArrive');

        $_nbre_adult = $request->request->get('nbrAdult');
        $_nbre_child = $request->request->get('nbrChild');
        $_bike       = $request->request->get('nbrBike');
        $_passeport  = $request->request->get('passeport');


        $_trips = $this->_flixbus_manager->getTripSearch($_type_recherche, $_id_depart, $_id_arrive, $_date_depart, $_date_arrive, $_nbre_adult, $_nbre_child, $_bike);
        $_list  = ($_trips[1]) ? null : json_decode($_trips[0]);

        $currentDate = new \DateTime($_date_depart);
        $nextDate = false;
        $attempts = 0;

        while (empty($_list->trips) && $attempts < 5) {
            $dateDepart = $currentDate->format('d.m.Y');
            $_trips = $this->_flixbus_manager->getTripSearch($_type_recherche, $_id_depart, $_id_arrive, $dateDepart, $_date_arrive, $_nbre_adult, $_nbre_child, $_bike);
            $_list  = ($_trips[1]) ? null : json_decode($_trips[0]);

            if (empty($_list->trips)) {
                $currentDate->modify('+1 day');
                $nextDate = true;
            }
            
            $attempts++;
        }

        return new JsonResponse([
            'voyages' => $_list,
            'passeport' => $_passeport,
            'suggest' => [
                'next' => $nextDate,
                'emptyDate' => $_date_depart,
                'date' => $currentDate->format('d.m.Y'),
            ]
        ]);
    }

    /**
     * @Route("/recherche-aller-retour", name="recherche_aller_retour")
     */
    public function searchRoundTrip(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Validation des dates
        $_date_depart = $request->request->get('dateDepart');
        $_date_arrive = $request->request->get('dateArrive');
        
        if (!$_date_arrive) {
            return new JsonResponse(['error' => 'Date de retour requise'], 400);
        }

        // Recherche aller
        $aller = $this->getTripData($request, $_date_depart);
        if ($aller['error']) {
            return new JsonResponse(['error' => $aller['error']], 400);
        }

        // Recherche retour
        $retour = $this->getTripData($request, $_date_arrive);
        if ($retour['error']) {
            return new JsonResponse(['error' => $retour['error']], 400);
        }

        // Calcul prix total (prix aller x 2)
        $totalPrice = $aller['price'] * 2;

        return new JsonResponse([
            'aller' => $aller['data'],
            'retour' => $retour['data'], 
            'total_price' => round($totalPrice, 2),
            'reduction' => null
        ]);
    }

    private function getTripData(Request $request, string $date): array 
    {
        $_type_recherche = $request->request->get('type');
        $_id_depart = $request->request->get('depart');
        $_id_arrive = $request->request->get('arrive');
        $_nbre_adult = $request->request->get('nbrAdult');
        $_nbre_child = $request->request->get('nbrChild');
        $_bike = $request->request->get('nbrBike');

        $_trips = $this->_flixbus_manager->getTripSearch(
            $_type_recherche, 
            $_id_depart, 
            $_id_arrive, 
            $date,
            null,
            $_nbre_adult,
            $_nbre_child,
            $_bike
        );

        if ($_trips[1]) {
            return ['error' => $_trips[0], 'data' => null, 'price' => 0];
        }

        $data = json_decode($_trips[0], true);
        $price = $data['trips'][0]['price'] ?? 0;

        return ['error' => null, 'data' => $data, 'price' => $price];
    }


    /**
     * @Route("/ajout-reservation", name="ajout_reservation")
     */
    public function ajoutReservation(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Session token
        $_token = $session->get('token');

        // $_trip_uid = 'direct:236502053:1:10';

        $_trip_uid = $request->request->get('uid');
        $dataTrajet = $session->get('dataTrajet');
        $_nbre_child = $dataTrajet["passager"]["enfant"];
        $_bike = $dataTrajet["passager"]["velo"];
        $_nbre_adult = $dataTrajet["passager"]["adulte"];

        $_reservation = $this->_flixbus_manager->createReservation($_token, $_trip_uid, $_nbre_child, $_bike, $_nbre_adult);
        $_ticket   = ($_reservation[1]) ? null : json_decode($_reservation[0]);
        
        // Session pour la reservation
        $session->set('reservation_id', $_ticket->reservation->id);
        $session->set('reservation_token', $_ticket->reservation->token);

        return new JsonResponse([
            'reservation' => $_ticket
        ]);
    }

    /**
     * @Route("/ajout-retour-reservation", name="ajout_retour_reservation")
     */
    public function ajoutRetourReservation(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Session pour la reservation
        $_reservation_id    = $session->get('reservation_id');
        $_reservation_token = $session->get('reservation_token');

        // Session token
        $_token = $session->get('token');

        $_trip_uid = $request->request->get('uid');

        $dataTrajet = $session->get('dataTrajet');

        $_nbre_child = $dataTrajet["passager"]["enfant"];
        $_bike = $dataTrajet["passager"]["velo"];
        $_nbre_adult = $dataTrajet["passager"]["adulte"];

        $_reservation = $this->_flixbus_manager->addReturnTripReservation($_token, $_trip_uid, $_nbre_child, $_bike, $_nbre_adult, $_reservation_id, $_reservation_token);
        $_ticket   = ($_reservation[1]) ? null : json_decode($_reservation[0]);

        return new JsonResponse([
            'reservation' => $_ticket
        ]);
    }

    /**
     * @Route("/passagers-details", name="passagers_details")
     */
    public function detailsPassagers(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Session pour la reservation
        $_reservation_id    = $session->get('reservation_id');
        $_reservation_token = $session->get('reservation_token');

        $_passagers = $this->_flixbus_manager->getPassengersDetails($_reservation_id, $_reservation_token);
        $_details   = ($_passagers[1]) ? null : json_decode($_passagers[0]);

        return new JsonResponse([
            'passagers' => $_details
        ]);
    }


    /**
     * @Route("/modification-passagers-details", name="modification_passagers_details")
     */
    public function modificationDetailsPassagers(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Session pour la reservation
        $_reservation_id    = $session->get('reservation_id');
        $_reservation_token = $session->get('reservation_token');

        // Définir les passagers avec leurs attributs
        $passengers = $request->request->get('details');
        // $passengers = [
        //     [
        //         'firstname' => 'John',
        //         'lastname'  => 'Doe',
        //         'phone'     => '+18678989988',
        //         'birthdate' => '01.01.1990',
        //         'type'      => 'adult',
        //         'reference_id' => '30864193906'
        //     ],
        //     [
        //         'firstname' => 'Jane',
        //         'lastname'  => 'Smith',
        //         'phone'     => '+18678980988',
        //         'birthdate' => '01.01.1994',
        //         'type'      => 'adult',
        //         'reference_id' => '30864193916'
        //     ],
        //     [
        //         'firstname' => 'Jr',
        //         'lastname'  => 'Smith',
        //         'phone'     => '',
        //         'birthdate' => '01.01.2012',
        //         'type'      => 'children',
        //         'reference_id' => '30864193926'
        //     ],
        // ];

        $_passagers = $this->_flixbus_manager->putPassengersDetails($_reservation_id, $_reservation_token, $passengers);

        $_details   = ($_passagers[1]) ? null : json_decode($_passagers[0]);

        return new JsonResponse([
            'passagers' => $_details
        ]);
    }

    /**
     * @Route("/offres-supplementaire", name="offres_supplementaire")
     */
    public function offresSupplementaire(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Session pour la reservation
        $_reservation_id    = $session->get('reservation_id');
        $_reservation_token = $session->get('reservation_token');

        $_offers = $this->_flixbus_manager->getAncillaryOffers($_reservation_id, $_reservation_token);

        $_liste = ($_offers[1]) ? null : json_decode($_offers[0]);

        return new JsonResponse([
            'offres' => $_liste
        ]);
    }

    /**
     * @Route("/ajouter-offre-supplementaire", name="ajouter_offre_supplementaire")
     */
    public function ajoutOffresSupplementaire(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Donnee depuis l'offre supplementaire choisis (dans la fonction getAncillaryOffers)
        $_reference_id = "tpbof|236501473:1:10|52f2c161-54c6-401c-9609-a309d8287051";
        $_product_type = "luggage_additional";
        $_amount = 5.49;

        // Session pour la reservation
        $_reservation_id    = $session->get('reservation_id');
        $_reservation_token = $session->get('reservation_token');

        // Session token
        $_token = $session->get('token');

        $_offers = $this->_flixbus_manager->putAncillaryOffers($_token, $_reservation_id, $_reservation_token, $_reference_id, $_product_type, $_amount);

        $_liste = ($_offers[1]) ? null : json_decode($_offers[0]);

        return new JsonResponse([
            'offres' => $_liste
        ]);
    }

    /**
     * @Route("/ajouter-un-bon", name="ajouter_un_bon")
     */
    public function ajoutUnBon(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Code de bon TST1D
        $_code = $request->request->get("codeBon");

        // Session pour la reservation
        $_reservation_id    = $session->get('reservation_id');
        $_reservation_token = $session->get('reservation_token');

        // Session token
        $_token = $session->get('token');

        $_offers = $this->_flixbus_manager->redeemVoucher($_token, $_reservation_id, $_reservation_token, $_code);

        $_liste = ($_offers[1]) ? null : json_decode($_offers[0]);

        dump($_liste);

        return new JsonResponse([
            'bon' => $_liste
        ]);
    }

    /**
     * @Route("/methode-paiement", name="methode_paiement")
     */
    public function methodePaiement(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Session pour la reservation
        $_reservation_id    = $session->get('reservation_id');
        $_reservation_token = $session->get('reservation_token');

        // Session token
        $_token = $session->get('token');

        $_offers = $this->_flixbus_manager->getPaymentMethods($_token, $_reservation_id, $_reservation_token);

        $_liste = ($_offers[1]) ? null : json_decode($_offers[0]);

        dump($_liste);

        return new JsonResponse([
            'methode_paiement' => $_liste
        ]);
    }

    /**
     * @Route("/ajout-paiement", name="ajout_paiement")
     */
    public function ajoutPaiement(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Paiement data freduciotamby@gmail.com
        $_email = $request->request->get('email');

        // provider depuis listePaiement
        $_psp    = 'offline';
        $_method = 'cash';

        // Session pour la reservation
        $_reservation_id    = $session->get('reservation_id');
        $_reservation_token = $session->get('reservation_token');

        // Session token
        $_token = $session->get('token');

        $_pay = $this->_flixbus_manager->startPayment($_token, $_reservation_id, $_reservation_token, $_email, $_psp, $_method);

        $_liste = ($_pay[1]) ? null : json_decode($_pay[0]);
        dump($_liste);
        // Session pour l'id paiement
        $session->set('paiement_id', $_liste->payment_id);

        return new JsonResponse([
            'paiement' => $_liste
        ]);
    }

    /**
     * @Route("/finalisation-paiement", name="finalisation_paiement")
     */
    public function finalisationPaiement(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Paiement data
        $_email = 'freduciotamby@gmail.com';
        // provider depuis listePaiement
        $_psp    = 'offline';
        $_method = 'cash';

        // Session pour la reservation
        $_reservation_id    = $session->get('reservation_id');
        $_reservation_token = $session->get('reservation_token');

        // Session pour le paiement
        $_payment_id = $session->get('paiement_id');

        // Session token
        $_token = $session->get('token');

        $_pay = $this->_flixbus_manager->finalizePaymentBooking($_token, $_reservation_id, $_reservation_token, $_payment_id);

        $_liste = ($_pay[1]) ? null : json_decode($_pay[0]);

        // Session pour le ticket
        $session->set('order_id', $_liste->order_id);
        $session->set('download_hash', $_liste->download_hash);

        return new JsonResponse([
            'paiement' => $_liste
        ]);
    }

    /**
     * @Route("/tickets", name="tickets")
     */
    public function ticketDeVoyage(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Session pour le ticket
        $_order_id = $session->get('order_id');
        $_download_hash = $session->get('download_hash');

        // Session token
        $_token = $session->get('token');

        $_pay = $this->_flixbus_manager->ticketForOrder($_token, $_order_id, $_download_hash);

        $_ticket = ($_pay[1]) ? null : json_decode($_pay[0]);

        return new JsonResponse([
            'tickets' => $_ticket
        ]);
    }

    /**
     * @Route("/annuler-voyage", name="annuler_voyage")
     */
    public function annuleVoyage(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Session pour le ticket
        $_order_id = $request->request->get('orderId');
        $_download_hash = $request->request->get('downloadHash');

        // Session token
        if (!$session->get('token')) {
            $_json  = $this->_flixbus_manager->getAccessToken();
            $_tokenDecode = json_decode($_json[0]);
            $session->set('token', $_tokenDecode->token);
        }

        $_token = $session->get('token');

        $_annul = $this->_flixbus_manager->cancelOrder($_token, $_order_id, $_download_hash);

        $_cofirm = ($_annul[1]) ? null : json_decode($_annul[0]);

        dd($_cofirm);

        return new JsonResponse([
            'tickets' => $_cofirm
        ]);
    }
}
