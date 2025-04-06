<?php

namespace App\Controller\FrontOffice;

use App\Service\Api\ServiceBlablacar;
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

class BlablacarController extends AbstractController
{

	private $_entity_manager;
    private $_blablacar_manager;
    private $_session;

	public function __construct(
        EntityManagerInterface $_entity_manager,
        ServiceBlablacar $_blablacar_manager,
        SessionInterface $_session )
    {
	    $this->_entity_manager = $_entity_manager;
        $this->_blablacar_manager = $_blablacar_manager;
        $this->_session = $_session;
    }

    /**
     *@Route("/test-stations", name="test_stations")
     */
    public function testStations()
    {
        $_json  = $this->_blablacar_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        if (isset($_token->access_token)) {
            $_stations = $this->_blablacar_manager->getStationsBlablacar($_token->access_token);
            return new JsonResponse($_stations);
        }

        return new JsonResponse(['error' => 'Unable to retrieve access token'], Response::HTTP_BAD_REQUEST);
    }

    /**
     *@Route("/blablacar", name="blablacar")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function index()
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $station = $this->_session->get('dataStationBlablaCar');

        if (empty($station) && empty($trajets)) {
            $station = $this->getStations();
            
            $this->_session->set('dataStationBlablaCar', $station);
        }

        return $this->render('FrontOffice/blablacar/index.html.twig', [
            'stations' => $station,
        ]);
    }

    /**
     *@Route("/trajets-disponibles", name="trajets_disponibles")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function trajetsDisponibles(Request $request)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $trajets = $request->request->get('trajets');

        if (empty($trajets)) {
            $trajets = $this->_session->get('dataTrajetsBlablabus');

            if ($trajets === null) {
                $this->addFlash(
                    'warning',
                    "Aucune donnée reçue pour le trajets !"
                );
                return $this->redirectToRoute('blablacar');
            }
        } else {
            $this->_session->set('dataTrajetsBlablabus', $trajets);
        }

        return $this->render('FrontOffice/blablacar/trajets.html.twig', [
            'trajets' => json_decode($trajets, true)
        ]);
    }

    /**
     *@Route("/trajet-selectionner", name="trajets_selected_blabla")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function trajetSelected(Request $request)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $this->_session->remove('dataTrajetSelectedBlabla');
        $this->_session->remove('dataTrajetSelectedRetourBlabla');

        $trajets = $request->request->get('trajetSelected');
        $trajetsRetour = $request->request->get('trajetSelectedRetour');

        if (!empty($trajets)) {
            $this->_session->set('dataTrajetSelectedBlabla', $trajets);

            if (empty($trajetsRetour)) {
                $this->_session->remove('dataTrajetSelectedRetourBlabla');
            } else {
                $this->_session->set('dataTrajetSelectedRetourBlabla', $trajetsRetour);
            }
        } else {
            $trajets = $this->_session->get('dataTrajetSelectedBlabla');
            $trajetsRetour = $this->_session->get('dataTrajetSelectedRetourBlabla');
        }

        if (empty($trajets)) {
            $this->addFlash('warning', "Aucune donnée reçue pour les trajets !");
            return $this->redirectToRoute('blablacar');
        }

        $initTrajet = $this->_session->get('initTrajetsBlablacar');

        return $this->render('FrontOffice/blablacar/selectedTrx.html.twig', [
            'trajets'       => json_decode($trajets, true),
            'trajetsRetour' => json_decode($trajetsRetour, true),
            'initTrajet'    => $initTrajet
        ]);
    }

    /**
     *@Route("/mode-paiement", name="mode_paiement_blabla")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function modePaiement(Request $request)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $reservation = $request->request->get('reservation');

        if (empty($reservation)) {
            $reservation = $this->_session->get('dataReservationBlabla');

            if ($reservation === null) {
                $this->addFlash(
                    'warning',
                    "Aucune donnée reçue pour le réservation !"
                );
                return $this->redirectToRoute('blablacar');
            }
        } else {
            $this->_session->set('dataReservationBlabla', $reservation);
        }

        return $this->render('FrontOffice/blablacar/creatResarvation.html.twig', [
            'reservation' => json_decode($reservation, true)
        ]);
    }

    public function getStations() {
        $_json  = $this->_blablacar_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        $this->_session->set('token', $_token->access_token);

        $_stations = $this->_blablacar_manager->getStationsBlablacar($_token->access_token);
        $_list     = ($_stations[1]) ? null : json_decode($_stations[0]);

        return $_list;
    }

}
