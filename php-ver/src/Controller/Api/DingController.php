<?php

namespace App\Controller\Api;

use App\Entity\Dingproduct;
use App\Entity\Dingproductdescriptions;
use App\Repository\DingproductRepository;
use App\Repository\DingproductdescriptionsRepository;
use App\Repository\TransfertRepository;
use App\Service\UploadFileService;
use App\Service\Api\ServiceDing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DingController extends AbstractController
{
    private $_reloadly_manager;
    private $_entity_manager;
    private $_uploadFileService;
    private $_transfert_repo;
    private $_ding_manager;
    private $_dingproductRepository;
    private $_dingproductdescriptionsRepository;

    public function __construct(
        EntityManagerInterface $_entity_manager,
        UploadFileService $_uploadFileService,
        ServiceDing $_ding_manager,
        TransfertRepository $_transfert_repo,
        DingproductRepository $_dingproductRepository,
        DingproductdescriptionsRepository $_dingproductdescriptionsRepository
    )
    {
        $this->_entity_manager    = $_entity_manager;
        $this->_uploadFileService = $_uploadFileService;
        $this->_ding_manager  = $_ding_manager;
        $this->_transfert_repo    = $_transfert_repo;
        $this->_dingproductRepository = $_dingproductRepository;
        $this->_dingproductdescriptionsRepository = $_dingproductdescriptionsRepository;
    }

    public function soldeDing()
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }


        $_json  = $this->_ding_manager->getAccessToken();
        $_token = json_decode($_json[0]);
        $_balance = $this->_ding_manager->getBalance($_token->access_token);
        $_list    = ($_balance[1]) ? null : json_decode($_balance[0]);

        return $_list->Balance;
    }

    /**
     * @Route("/liste-operateur-pays-ding", name="liste_operateur_pays_ding")
     */
    public function listeOperateurPaysDing(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }


        $_json  = $this->_ding_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        $_code   = $request->request->get('code');
        $_number = ltrim($request->request->get('number'), '+');

        $_country_isos = $_code;
        $_region_codes = $_code;


        $_operators = $this->_ding_manager->getProviders($_token->access_token,$_country_isos,$_region_codes,$_number);
        $_list      = ($_operators[1]) ? null : json_decode($_operators[0]);
        return new JsonResponse([
            'operateurs' => $_list
        ]);
    }

    /**
     * @Route("/operateur-status-ding", name="operateur_status_ding")
     */
    public function operateurStatusDing(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }


        $_json  = $this->_ding_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Provider code
        $_code = '0MMA';

        $_operators = $this->_ding_manager->getProvidersStatus($_token->access_token,$_code);
        $_list      = ($_operators[1]) ? null : json_decode($_operators[0]);
    
        return new JsonResponse([
            'operateurs' => $_list
        ]);
    }

    /**
     * @Route("/offres-operateur-ding", name="offres_operateur_ding")
     */
    public function offresOperateurDing(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }


        $_json  = $this->_ding_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Provider code
        $_provider_code = $request->request->get('providerCode'); // '0MMA'
        $_country_isos  = $request->request->get('countryIsos'); // 'MA'        
        $_region_codes  = $request->request->get('regionCodes'); // 'MA'

        $_offers = $this->_ding_manager->getProducts($_token->access_token,$_country_isos,$_provider_code,$_region_codes);
        $_list   = ($_offers[1]) ? null : json_decode($_offers[0]);
        
        return new JsonResponse([
            'offres' => $_list
        ]);
    }

    /**
     * @Route("/ajout-transfert-ding", name="ajout_transfert_ding")
     */
    public function ajoutTransfertDing(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $operator = $request->request->get('operator');
        $offre = $request->request->get('offre');
        $country = $request->request->get('country');
        $number = ltrim($request->request->get('number'), '+');

        $_json  = $this->_ding_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        // Provider code
        $_numero     = $number; // 212678475113
        $_sku_code   = $offre["skuCode"]; // 'I0MAMA17257'
        $_send_value = (float)$offre["tarif"]; // 1.70

        $_transfert = $this->_ding_manager->sendTransfert($_token->access_token,$_numero,$_sku_code,$_send_value);
        $_list      = ($_transfert[1]) ? null : json_decode($_transfert[0]);
        
        return new JsonResponse([
            'transfert' => $_list
        ]);
    }
    

    /**
     * @Route("/transfert-record-ding", name="transfert_record_ding")
     */
    public function transfertRecordDing(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_ding_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        $_transfert_ref   ='739237664';
        $_distributor_ref ='1';
        $_account_number  ='261000000000'; 

        $_transfert = $this->_ding_manager->listeTransfertRecord($_token->access_token,$_transfert_ref,$_distributor_ref,$_account_number);
        $_list      = ($_transfert[1]) ? null : json_decode($_transfert[0]);
        
        return new JsonResponse([
            'transfert' => $_list
        ]);
    }

    /**
     * @Route("/description-offres-ding", name="description_offres_ding")
     */
    public function descriptionOffresDing(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }


        $_json  = $this->_ding_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        $_langage_code ='en';
        $_skuCodes     ='MG_OR_TopUp_3.00';


        $_offers = $this->_ding_manager->getProductDescriptions($_token->access_token,$_langage_code,$_skuCodes);
        $_list   = ($_offers[1]) ? null : json_decode($_offers[0]);
        
        return new JsonResponse([
            'offres' => $_list
        ]);
    }

    public function compareBenefits($_mechanism,$_benefits){
        $_string = '';

        if ($_mechanism == "Immediate" && implode(",", $_benefits) == "Mobile,Minutes") {
            $_string = 'TopUp';
        }
        if ($_mechanism == "Immediate" && implode(",", $_benefits) == "Mobile,Data") {
            $_string = 'Data';
        }
        if ($_mechanism == "ReadReceipt" && implode(",", $_benefits) == "Mobile,Minutes,Data") {
            $_string = 'PIN';
        }
        if ($_mechanism == "ReadReceipt" && implode(",", $_benefits) == "LongDistance,Minutes") {
            $_string = 'LDI';
        }
        if ($_mechanism == "ReadReceipt" && implode(",", $_benefits) == "DigitalProduct") {
            $_string = 'Voucher';
        }
        if ($_mechanism == "Immediate" && implode(",", $_benefits) == "Mobile,Minutes,Data") {
            $_string = 'Bundle';
        }
        if ($_mechanism == "Immediate" && implode(",", $_benefits) == "TV,Utility") {
            $_string = 'DTH';
        }

        return $_string;
    }

    /**
     * @Route("/insertion-product-ding", name="insert_product_ding")
     */
    public function ajoutProduitDing(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_json  = $this->_ding_manager->getAccessToken();
        $_token = json_decode($_json[0]);

        $_offers = $this->_ding_manager->getProducts($_token->access_token,'','','');
        $_list   = ($_offers[1]) ? null : json_decode($_offers[0]);

        foreach ($_list->Items as $item) {
            $_type = $this->compareBenefits($item->RedemptionMechanism,$item->Benefits);
            $_mechanism = $item->RedemptionMechanism;
            $_benefits  = implode(",",$item->Benefits);
            $_provider  = $item->ProviderCode;
            $_sku       = $item->SkuCode;
            $_value     = $item;

            $_product_last = $this->_dingproductRepository->findOneBy(['skucode' => $_sku]);

            if ($_product_last) {

            }else{
                $_product = new Dingproduct();
                $_product->setTransfertType($_type);
                $_product->setRedemptionmechanism($_mechanism);
                $_product->setBenefits($_benefits);
                $_product->setValue((array)$_value);
                $_product->setProvidercode($_provider);
                $_product->setSkucode($_sku);

                $this->_entity_manager->persist($_product);
                $this->_entity_manager->flush();
            }
        }
        
        return new JsonResponse([
            'response' => true
        ]);
    }

    /**
     * @Route("/find-all-type-product-ding", name="find_all_type_product_ding")
     */
    public function findAllTypeProductDing(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_products = $this->_dingproductRepository->findAll();

        $types = array_values(array_unique(array_filter(array_map(function ($item) {
            return $item->getTransfertType() ?: null;
        }, $_products))));

        return new JsonResponse([
            'typesProduct' => $types
        ]);
    }

    /**
     * @Route("/find-code-type-product-ding", name="find_code_type_product_ding")
     */
    public function findCodeTypeProductDing(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_products = $this->_dingproductRepository->findAll();

        $listeTypeCode = array_map(function ($codes) {
            return array_map(function ($code, $count) {
                return "$code ($count)";
            }, array_keys($codes), $codes);
        }, array_reduce(array_map(function ($item) {
            $type = $item->getTransfertType();
            $code = $item->getValue()['ProviderCode'];
            return !empty($type) ? ['type' => $type, 'code' => $code] : null;
        }, $_products), function ($carry, $item) {
            if ($item) {
                $type = $item['type'];
                $code = $item['code'];

                // Initialiser le tableau pour chaque type et code si nécessaire
                if (!isset($carry[$type])) {
                    $carry[$type] = [];
                }
                if (!isset($carry[$type][$code])) {
                    $carry[$type][$code] = 0;
                }

                // Incrémenter le compteur pour chaque code
                $carry[$type][$code]++;
            }
            return $carry;
        }, []));

        return new JsonResponse([
            'productsTypeCode' => $listeTypeCode
        ]);
    }

    /**
     * @Route("/filtre-product-ding", name="filtre_product_ding")
     */
    public function filtreProduitDing(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_code = '0JMM';
        $_type = 'TopUp';

        $_products = $this->_dingproductRepository->findBy(['providercode' => $_code,'transfertType' => $_type]);

        $_ding_products_array = array_map(function($dingProduct) {
            return $dingProduct->toArray(); // Transforme chaque Dingproduct en tableau
        }, $_products);

        return new JsonResponse([
            'products' => $_ding_products_array
        ]);
    }

    /**
     * @Route("/filtre-all-product-ding", name="filtre_all_product_ding")
     */
    public function filtreAllProduitDing(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        //$_code = '0JMM';
        $_code = $request->request->get('providerCode');
        $types = ['TopUp','PIN','Data','LDI','Voucher','Bundle','DTH'];

        $offres = [];

        // Boucle sur chaque type pour trouver les produits correspondants
        foreach ($types as $type) {

            $_products = $this->_dingproductRepository->findBy(['providercode' => $_code, 'transfertType' => $type]);

            $_ding_products_array = array_map(function ($dingProduct) {
                return $dingProduct->toArray(); // Transforme chaque Dingproduct en tableau
            }, $_products);

            // Si le tableau de produits n'est pas vide, on l'ajoute au résultat
            if (!empty($_ding_products_array)) {
                $offres[] = [
                    'type' => $type,
                    'produits' => $_ding_products_array
                ];
            }
        }

        return new JsonResponse([
            'offres' => $offres
        ]);
    }

     /**
     * @Route("/insertion-product-description-ding", name="insert_product_description_ding")
     */
    public function ajoutProduitDescriptionDing(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_json     = $this->_ding_manager->getAccessToken();
        $_token    = json_decode($_json[0]);
        $_products = $this->_dingproductRepository->findAll();

        foreach ($_products as $_product) {
            $_product_last = $this->_dingproductdescriptionsRepository->findOneBy(['skucode' => $_product->getSkucode()]);
            $_desc = $this->_ding_manager->getProductDescriptions($_token->access_token,'en',$_product->getSkucode());
            $_list = ($_desc[1]) ? null : json_decode($_desc[0]);
            if ($_product_last) {

            }else{
                $_product_desc = new Dingproductdescriptions();
                $_product_desc->setValue((array)$_list);
                $_product_desc->setSkucode($_product->getSkucode());

                $this->_entity_manager->persist($_product_desc);
                $this->_entity_manager->flush();
            }
        }
        
        return new JsonResponse([
            'response' => true
        ]);
    }

    /**
     * @Route("/filtre-product-desc-ding", name="filtre_product_desc_ding")
     */
    public function filtreProduitDescDing(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_code     = '0JMMMM98094';
        $_products = $this->_dingproductdescriptionsRepository->findBy(['skucode' => $_code]);

        $_ding_products_array = array_map(function($dingProduct) {
            return $dingProduct->toArray(); // Transforme chaque Dingproduct en tableau
        }, $_products);

        return new JsonResponse([
            'products_description' => $_ding_products_array
        ]);
    }
}