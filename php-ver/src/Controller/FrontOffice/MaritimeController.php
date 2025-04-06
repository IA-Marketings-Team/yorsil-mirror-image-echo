<?php

namespace App\Controller\FrontOffice;

use DateTime;
use DateTimeZone;
use App\Service\Api\ServiceFerry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MaritimeController extends AbstractController
{

	private $_entity_manager;
    private $_ferry_manager;
    private $_session;

	public function __construct(
        EntityManagerInterface $_entity_manager,
        ServiceFerry $_ferry_manager,
        SessionInterface $_session
    ) {
	    $this->_entity_manager = $_entity_manager;
        $this->_ferry_manager  = $_ferry_manager;
        $this->_session        = $_session;
    }

    /**
     *@Route("/Maritime", name="maritime")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function index()
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_list_routes = $this->_session->get('dataRouteFerry');

        if ($_list_routes === null) {
            $_list_routes = $this->getRoutes();
            $this->_session->set('dataRouteFerry', $_list_routes);
        }

        return $this->render('FrontOffice/maritime/index.html.twig', [
            'routes' => $_list_routes
        ]);
    }

    /**
     *@Route("/traverses", name="traverses_reservation")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function traversesResarvation(Request $request)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $route = $request->request->get('route');
        $traversee = $request->request->get('traversee');

        if (empty($traversee)) {
            $traversee = $this->_session->get('dataTraversee');
            $route = $this->_session->get('dataRoute');

            if ($traversee === null) {
                $this->addFlash(
                    'warning',
                    "Aucune donnée reçue pour 'traversée' !"
                );
                return $this->redirectToRoute('maritime');
            }
        } else {
            $this->_session->set('dataTraversee', json_decode($traversee, true));
            $this->_session->set('dataRoute', json_decode($route, true));
        }


        return $this->render('FrontOffice/maritime/traverses.html.twig', [
            "route" => $route,
            "traversee" => $traversee
        ]);
    }

    /**
     *@Route("/recapitulatif", name="recapitulatif_reservation")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function recapResarvation()
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('FrontOffice/maritime/recap.html.twig');
    }

    public function getRoutes()
    {
        $_json  = $this->_ferry_manager->getAccessToken();

        $_token = json_decode($_json[0]);

        $this->_session->set('token', $_token->token);

        $_routes = $this->_ferry_manager->getRoute($_token->token);
        $_list   = ($_routes[1]) ? null : json_decode($_routes[0]);

        return $_list;
    }

}