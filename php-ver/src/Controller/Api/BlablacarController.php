<?php

namespace App\Controller\Api;

use App\Service\Api\ServiceBlablacar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlablacarController extends AbstractController
{
    private $_blablacar_manager;
    private $_entity_manager;

    public function __construct(
        EntityManagerInterface $_entity_manager,
        ServiceBlablacar $_blablacar_manager
    ) {
        $this->_entity_manager  = $_entity_manager;
        $this->_blablacar_manager = $_blablacar_manager;
    }

    /**
     * @Route("/liste-stations-blablacar", name="liste_station")
     */
    public function listeStation(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_blablacar_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Session pour le token
        $session->set('token', $_token->access_token);

        $_stations = $this->_blablacar_manager->getStationsBlablacar($_token->access_token);
        
        $_list     = ($_stations[1]) ? null : json_decode($_stations[0]);
        
        return new JsonResponse([
            'stations' => $_list
        ]);
    }

    /**
     * @Route("/liste-trajets-blablacar", name="liste_trajet")
     */
    public function listeTrajets(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_blablacar_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Session pour le token
        $session->set('token', $_token->access_token);

        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            $data = $session->get('initTrajetsBlablacar');

            if ($data === null) {
                $this->addFlash(
                    'warning',
                    "Aucune donnée reçue pour le trajets !"
                );
                return $this->redirectToRoute('blablacar');
            }
        } else {
            $session->set('initTrajetsBlablacar', $data);
        }

        $_trajets = $this->_blablacar_manager->postOrientation($_token->access_token, $data);
        $_list    = ($_trajets[1]) ? null : json_decode($_trajets[0]);
        
        return new JsonResponse([
            'trajets' => $_list
        ]);
    }

    /**
     * @Route("/creation-reservations-blablacar", name="creation_reservation_blablacar")
     */
    public function creerReservation(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_blablacar_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Session pour le token
        $session->set('token', $_token->access_token);

        $data = json_decode($request->getContent(), true);
        
        flush(); // Force l'affichage immédiat
        $_reservations = $this->_blablacar_manager->postCreateBooking($_token->access_token, $data);
        $_list         = ($_reservations[1]) ? null : json_decode($_reservations[0]);

        return new JsonResponse([
            'reservations' => $_list
        ]);
    }

    /**
     * @Route("/modification-partielle-customer", name="modification-partielle_customer")
     */
    public function modifierCustomer(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_blablacar_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Session pour le token
        $session->set('token', $_token->access_token);

        $_booking = $this->_blablacar_manager-> patchCustomer($_token->access_token,'B9QX32');
        $_list         = ($_booking[1]) ? null : json_decode($_booking[0]);

        return new JsonResponse([
            'reservations' => $_list
        ]);
    }

    /**
     * @Route("/modification-partielle-passengers", name="modification-partielle_passengers")
     */
    public function modifierPassengers(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_blablacar_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Session pour le token
        $session->set('token', $_token->access_token);

        $_booking = $this->_blablacar_manager-> patchPassengers($_token->access_token,'B9QX32');
        $_list         = ($_booking[1]) ? null : json_decode($_booking[0]);

        return new JsonResponse([
            'reservations' => $_list
        ]);
    }

    /**
     * @Route("/creation-paiement-blablacar", name="creation_paiement")
     */
    public function creerPaiement(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_blablacar_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Session pour le token
        $session->set('token', $_token->access_token);

        $data = json_decode($request->getContent(), true);

        $_booking = $this->_blablacar_manager->createPayment($_token->access_token, $data);
        $_list         = ($_booking[1]) ? null : json_decode($_booking[0]);

        return new JsonResponse([
            'reservations' => $_list
        ]);
    }

    /**
     * @Route("/confirmation-paiement-blablacar", name="confirmation_paiement")
     */
    public function confirmerPaiement(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_blablacar_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Session pour le token
        $session->set('token', $_token->access_token);

        $data = json_decode($request->getContent(), true);

        $_booking = $this->_blablacar_manager->confirmBooking($_token->access_token, $data["booking_number"]);
        $_list    = ($_booking[1]) ? null : json_decode($_booking[0]);

        return new JsonResponse([
            'reservations' => $_list
        ]);
    }

    /**
     * @Route("/reservation-finale-blablacar", name="reservation_finale")
     */
    public function reservationFinale(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_blablacar_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Session pour le token
        $session->set('token', $_token->access_token);

        $data = json_decode($request->getContent(), true);

        $_booking = $this->_blablacar_manager->getFinalBooking($_token->access_token, $data["booking_number"]);
        $_list    = ($_booking[1]) ? null : json_decode($_booking[0]);

        return new JsonResponse([
            'reservations' => $_list
        ]);
    }
    /**
     * @Route("/trajet-selected", name="trajet-selected", methods={"GET", "POST"})
     */
    public function destinationDisponible(Request $request): Response
 {
    // Récupérer les données envoyées dans le formulaire via 'trajets'
    $trajetsJson = $request->request->get('trajets');
    

    // Si les données existent, les décoder (car elles sont envoyées en JSON)
    $trajetsData = null;
    if ($trajetsJson) {
        $trajetsData = json_decode($trajetsJson, true);  // Convertir les données JSON en tableau PHP
    }

    // Passer les données récupérées à la vue
    return $this->render('FrontOffice/blablacar/trajets.html.twig', [
        'trajets' => $trajetsData
    ]);
}
}
