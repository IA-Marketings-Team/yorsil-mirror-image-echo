<?php

namespace App\Controller\Api;

use App\Repository\BoutRepository;
use App\Service\Api\ServiceAleda;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AledaController extends AbstractController
{
    private $_aleda_manager;
    private $_entity_manager;
    private $_session;
    private $boutRepository;

    public function __construct(
        EntityManagerInterface $_entity_manager,
        ServiceAleda $_aleda_manager,
        SessionInterface $_session,
        BoutRepository $boutRepository
    ) {
        $this->_entity_manager = $_entity_manager;
        $this->_aleda_manager  = $_aleda_manager;
        $this->_session        = $_session;
        $this->boutRepository  = $boutRepository;
    }

    public function initializeAuth()
    {
        $_return = false;
        // prod
        $_secret = '202609240657676390C3DF294AB0CE9A21C96E2392B6E510B4860998722F4BA5F21C235051E94AD85F510A543792E8379F94ACCD6A7B12D1244831716FA';
        // test
        // $_secret = '2026081967FA8BB04ABBBB72DD4C3D960ADBC0CE5E4A8F64F9AF727B1E2936AB8CA03DF7973A95C0F5B39BC29D376BBF02E12A64381C0D791A4AB4CD02EA83';
        $_xauth = $this->_aleda_manager->getAuth($_secret);
        $_json  = $this->_aleda_manager->getAccessToken($_xauth);
        $_token = ($_json[1]) ? null : json_decode($_json[0]);
        if ($_token->resultCode == 'OK') {
            $_xauth_g = $this->_aleda_manager->getAuth($_token->body->accessToken);
            $_yorsil  = $this->_aleda_manager->getXAuth($_xauth_g);
            $_hello   = ($_yorsil[1]) ? null : json_decode($_yorsil[0]);
            if ($_hello) {
                ($_hello->resultCode == 'OK') ? $this->_session->set('x-auth', $_xauth_g) : '';
                $_return = ($_hello->resultCode == 'OK') ? true : false;
            }
        }
        return $_return;
    }

    /**
     * @Route("/solde-aleda", name="solde_aleda")
     */
    public function getSoldeAgent()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->initializeAuth()) {
            $_x_auth    = $this->_session->get('x-auth');
            $_agent_ref = '131041';
            $_json  = $this->_aleda_manager->getBalance($_x_auth, $_agent_ref);
            $_solde = ($_json[1]) ? null : json_decode($_json[0]);

            return new JsonResponse([
                'solde' => $_solde->globalBalanceLeft
            ]);
        }

        return new JsonResponse([
            'solde' => 'Erreur de requête'
        ]);
    }

    /**
     * @Route("/liste-catalogue-produit", name="liste_catalogue_produit")
     */
    public function listeCatalogueProduit(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->initializeAuth()) {
            $_x_auth    = $this->_session->get('x-auth');
            $_agent_ref = '131041';
            $_json      = $this->_aleda_manager->getCatalogueTree($_x_auth, $_agent_ref);
            $_catalogue = ($_json[1]) ? null : json_decode($_json[0]);


            return new JsonResponse([
                'catalogues' => $_catalogue
            ]);
        }

        return new JsonResponse([
            'catalogues' => 'Erreur de requête'
        ]);
    }

    public function getCatalogueProduit()
    {
        if ($this->initializeAuth()) {
            $_x_auth    = $this->_session->get('x-auth');
            $_agent_ref = '131041';
            $_json      = $this->_aleda_manager->getCatalogueTree($_x_auth, $_agent_ref);
            $_catalogue = ($_json[1]) ? null : json_decode($_json[0], true);

            if (isset($_catalogue['navigate']['categories'])) {
                $_catalogue = $_catalogue['navigate']['categories'];
                return $_catalogue;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * @Route("/ajout-vente", name="ajout_vente")
     */
    public function ajoutVente(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->initializeAuth()) {
            $_x_auth    = $this->_session->get('x-auth');
            $_agent_ref = '131041';

            // '3526358005131'
            $_product_code = $request->request->get('productCode');
            $_qte = 1;

            // Generer automatiquement par nous (c'est a nous de le definir)
            // Ex : code_boutique + VNT + 001-100

            $_year = (new \DateTime())->format('Y');
            $unicode = mt_rand(100, 999);
            // 2024001VNT004
            $_sale_ref = (int)$_year . "VNT" . $unicode;

            // Methode de paiement : NONE / CASH / CREDIT_CARD / OTHER
            $_paiement  = 'CASH';

            // Email de la boutique ou du client
            $email = $this->boutRepository->findOneByUser($this->getUser())->getEmail();
            $customr_id = $email;

            $_json   = $this->_aleda_manager->postSalesWithVoucher($_x_auth, $_agent_ref, $_product_code, $_qte, $_sale_ref, $_paiement, $customr_id);
            $_ventes = ($_json[1]) ? null : json_decode($_json[0]);

            return new JsonResponse([
                'ventes' => $_ventes
            ]);
        }
        return new JsonResponse([
            'ventes' => 'Erreur de requête'
        ]);
    }

    /**
     * @Route("/ajout-vente-pdf", name="ajout_vente_pdf")
     */
    public function ajoutVenteAvecPdf(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->initializeAuth()) {
            $_x_auth    = $this->_session->get('x-auth');
            $_agent_ref = '131041';

            $_product_code = $request->request->get('productCode');
            $_qte = 1;

            // Generer automatiquement par nous (c'est a nous de le definir)
            // Ex : code_boutique + VNT + 001-100

            $_year = (new \DateTime())->format('Y');
            $unicode = mt_rand(100, 999);
            // 2024001VNT004
            $_sale_ref = (int)$_year . "VNT" . $unicode;

            // Methode de paiement : NONE / CASH / CREDIT_CARD / OTHER
            $_paiement  = 'CASH';

            // Email de la boutique ou du client
            $email = $this->boutRepository->findOneByUser($this->getUser())->getEmail();
            $customr_id = $email;

            $_json   = $this->_aleda_manager->postSalesWithVoucherPdf($_x_auth, $_agent_ref, $_product_code, $_qte, $_sale_ref, $_paiement, $customr_id);
            $_ventes = ($_json[1]) ? null : json_decode($_json[0]);

            return new JsonResponse([
                'ventes' => $_ventes
            ]);
        }
        return new JsonResponse([
            'ventes' => 'Erreur de requête'
        ]);
    }

    /**
     * @Route("/reservation-vente", name="reservation_vente")
     */
    public function ajoutReservationVente()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->initializeAuth()) {
            $_x_auth    = $this->_session->get('x-auth');
            $_agent_ref = '131041';

            $_product_code = '3526358005131';
            $_qte = 2;

            // Generer automatiquement par nous (c'est a nous de le definir)
            // Ex : code_boutique + VNT + 001-100
            $_sale_ref = '2024001VNT005';

            // Methode de paiement : NONE / CASH / CREDIT_CARD / OTHER
            $_paiement  = 'CASH';

            $_json   = $this->_aleda_manager->bookingSale($_x_auth, $_agent_ref, $_product_code, $_qte, $_sale_ref, $_paiement);
            $_ventes = ($_json[1]) ? null : json_decode($_json[0]);

            return new JsonResponse([
                'ventes' => $_ventes
            ]);
        }
        return new JsonResponse([
            'ventes' => 'Erreur de requête'
        ]);
    }

    /**
     * @Route("/confirmation-vente", name="confirmation_vente")
     */
    public function confirmationVente(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->initializeAuth()) {
            $_x_auth    = $this->_session->get('x-auth');
            $_agent_ref = '131041';

            // Generer automatiquement par nous (c'est a nous de le definir)
            // Ex : code_boutique + VNT + 001-100
            $_sale_ref = $request->request->get('sale_ref');

            // Methode de paiement : NONE / CASH / CREDIT_CARD / OTHER
            $_paiement  = 'CASH';

            $_json   = $this->_aleda_manager->confirmSale($_x_auth, $_agent_ref, $_sale_ref, $_paiement);
            $_ventes = ($_json[1]) ? null : json_decode($_json[0]);

            return new JsonResponse([
                'ventes' => $_ventes
            ]);
        }
        return new JsonResponse([
            'ventes' => 'Erreur de requête'
        ]);
    }

    /**
     * @Route("/details-vente", name="details_vente")
     */
    public function detailsVente()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->initializeAuth()) {
            $_x_auth    = $this->_session->get('x-auth');
            $_agent_ref = '131041';

            // Generer automatiquement par nous (c'est a nous de le definir)
            // Ex : code_boutique + VNT + 001-100
            $_sale_ref = '2024001VNT005';

            $_json   = $this->_aleda_manager->getSales($_x_auth, $_agent_ref, $_sale_ref);
            $_ventes = ($_json[1]) ? null : json_decode($_json[0]);

            return new JsonResponse([
                'ventes' => $_ventes
            ]);
        }
        return new JsonResponse([
            'ventes' => 'Erreur de requête'
        ]);
    }

    /**
     * @Route("/annulation-vente", name="annulation_vente")
     */
    public function annulationVente(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->initializeAuth()) {
            $_x_auth    = $this->_session->get('x-auth');
            $_agent_ref = '131041';

            // Generer automatiquement par nous (c'est a nous de le definir)
            // Ex : code_boutique + VNT + 001-100 -> '2024001VNT005'
            $_sale_ref =  $request->request->get('saleRef');

            // serial number sur la variable articles dans la vente ex: '151144554509'
            $_serial_number = $request->request->get('serialNumber');

            // Type de motif : UNREADABLE/ WRONG_PINCODE/ TECHNICAL_PROBLEM/ POINT_OF_SALE_ERROR/ FRAUD
            $_motif = 'WRONG_PINCODE';

            $_json   = $this->_aleda_manager->cancellingSale($_x_auth, $_agent_ref, $_sale_ref, $_serial_number, $_motif);
            $_ventes = ($_json[1]) ? null : json_decode($_json[0]);

            return new JsonResponse([
                'ventes' => $_ventes
            ]);
        }
        return new JsonResponse([
            'ventes' => 'Erreur de requête'
        ]);
    }
}
