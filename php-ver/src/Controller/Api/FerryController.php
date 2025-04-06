<?php

namespace App\Controller\Api;

use App\Service\Api\ServiceFerry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FerryController extends AbstractController
{
    private $_ferry_manager;
    private $_entity_manager;

    public function __construct(
        EntityManagerInterface $_entity_manager,
        ServiceFerry $_ferry_manager
    ) {
        $this->_entity_manager  = $_entity_manager;
        $this->_ferry_manager = $_ferry_manager;
    }

    /**
     * @Route("/liste-routes", name="liste_route")
     */
    public function listeRoute(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_ferry_manager->getAccessToken();

        $_token = json_decode($_json[0]);

        // Session pour le token
        $session->set('token', $_token->token);

        $_routes = $this->_ferry_manager->getRoute($_token->token);
        $_list   = ($_routes[1]) ? null : json_decode($_routes[0]);

        return new JsonResponse([
            'routes' => $_list
        ]);
    }

    /**
     * @Route("/liste-traversees", name="liste_traversee")
     */
    public function listeTraversee(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_ferry_manager->getAccessToken();

        $_token = json_decode($_json[0]);

        // Session pour le token
        //$session->set('token', $_token->token);

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Données invalides'
            ], 400);
        }

        $dataRoute = array(
            "CodeRoute" => $data['CodeRoute'] ?? null,
            "DateDeparture" => $data['DateDeparture'] ?? null,
            "DateReturn" => $data['DateReturn'] ?? null,
            "Adult" => (int)($data['Adult']) ?? 0,
            "Child" => (int)$data['Child'] ?? 0,
            "Dog" => (int)$data['Dog'] ?? 0,
            "Cat" => (int)$data['Cat'] ?? 0,
            "transport"=> $data['transport'] ?? '',
            "Height" => (float)$data['Height'] ?? 0,
            "Length" => (float)$data['Length'] ?? 0,
            "Trailer1"=> $data['TrailerL'] ?? 0,
            "TrailerH"=> $data['TrailerH'] ?? 0,
            "DepartureName"=> $data['DepartureName'] ?? '',
            "DestinationName"=> $data['DestinationName'] ?? '',
            "language"=> "fr"
        );

        $ageChild = isset($data['AgeChild']) ? $data['AgeChild'] : [];

        foreach ($ageChild as $index => $age) {
            $dataRoute["AgeChild" . ($index + 1)] = $age;
        }

        $_traversee = $this->_ferry_manager->postSailings($_token->token, $dataRoute);
        $_list   = ($_traversee[1]) ? null : json_decode($_traversee[0]);

        return new JsonResponse([
            'status' => true,
            'traversee' => $_list
        ]);
    }

    /**
     * @Route("/liste-des-servicess", name="liste_services")
     */
    public function listeServices(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_ferry_manager->getAccessToken();

        $_token = json_decode($_json[0]);

        $_services = $this->_ferry_manager->postServices($_token->token);
        $_list   = ($_services[1]) ? null : json_decode($_services[0]);

        return new JsonResponse([
            'services' => $_list
        ]);
    }

    /**
     * @Route("/creation-de-reservation", name="creation_reservation")
     */
    public function creerReservationFerry(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_ferry_manager->getAccessToken();

        $_token = json_decode($_json[0]);

        $_reservations = $this->_ferry_manager->creerBookingFerry($_token->token);
        $_list   = ($_reservations[1]) ? null : json_decode($_reservations[0]);

        return new JsonResponse([
            'reservation' => $_list
        ]);
    }

    /**
     * @Route("/confirmation-de-reservation", name="confirmation_reservation")
     */
    public function confirmerReservationFerry(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_ferry_manager->getAccessToken();

        $_token = json_decode($_json[0]);

        $_reservations = $this->_ferry_manager->confirmerBookingFerry($_token->token);
        $_list   = ($_reservations[1]) ? null : json_decode($_reservations[0]);

        return new JsonResponse([
            'reservation' => $_list
        ]);
    }

    /**
     * @Route("/details-de-reservation", name="details_reservation")
     */
    public function detailsReservationFerry(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_ferry_manager->getAccessToken();

        $_token = json_decode($_json[0]);

        $_reservations = $this->_ferry_manager->detailsBookingFerry($_token->token);
        $_list   = ($_reservations[1]) ? null : json_decode($_reservations[0]);

        return new JsonResponse([
            'reservation' => $_list
        ]);
    }

    /**
     * @Route("/frais-annulation-de-reservation", name="frais_annulation_reservation")
     */
    public function fraisAnnulationReservationFerry(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_ferry_manager->getAccessToken();

        $_token = json_decode($_json[0]);

        $_reservations = $this->_ferry_manager->getCancelCharge($_token->token);
        $_list   = ($_reservations[1]) ? null : json_decode($_reservations[0]);

        return new JsonResponse([
            'reservation' => $_list
        ]);
    }

    /**
     * @Route("/annulation-de-reservation", name="_annulation_reservation")
     */
    public function annulationReservationFerry(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_ferry_manager->getAccessToken();

        $_token = json_decode($_json[0]);

        $_reservations = $this->_ferry_manager->annulerBooking($_token->token);
        $_list   = ($_reservations[1]) ? null : json_decode($_reservations[0]);

        return new JsonResponse([
            'reservation' => $_list
        ]);
    }
}