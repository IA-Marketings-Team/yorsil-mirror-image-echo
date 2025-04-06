<?php

namespace App\Controller\FrontOffice;

use App\Entity\Debit;
use App\Entity\Recharge;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Service\Api\ServiceAleda;
use App\Repository\BoutRepository;
use App\Repository\RechargeRepository;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Controller\Api\AledaController;
use App\Repository\CategorieRepository;
use App\Repository\FraiserviceboutiqueRepository;
use App\Repository\GrilleTarifaireRepository;
use App\Repository\GrilleTarifaireBoutiqueRepository;
use App\Repository\FraiserviceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProduitPhysiqueRepository;
use App\Service\Metier\ServiceMetierBoutique;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Repository\ProduitPhysiqueCodeRepository;
use DateTime;
use DateTimeZone;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RechargemobileController extends AbstractController
{

	private $kernel;
	private $_entity_manager;
	private $_bout_repository;
	private $_recharge_repo;
    private $_boutique_manager;
    private $_produit_physique_repo;
    private $_produit_code_repo;
    private $categories_repo;
    private $_frais_service_repo;
    private $_frais_service_bout_repo;

	public function __construct(KernelInterface $kernel,
                                EntityManagerInterface $_entity_manager,
								BoutRepository $boutRepository,
                                RechargeRepository $_recharge_repo,
                                ServiceMetierBoutique $_boutique_manager,
                                ProduitPhysiqueRepository $_produit_physique_repo,
                                ProduitPhysiqueCodeRepository $_produit_code_repo,
                                CategorieRepository $categories_repo,
                                FraiserviceRepository $_frais_service_repo,
                                FraiserviceboutiqueRepository $_frais_service_bout_repo,
                                GrilleTarifaireRepository $grilletarifaireRepository,
                                GrilleTarifaireBoutiqueRepository $grilletarifaireBoutiqueRepository){
        $this->kernel                   = $kernel;
	    $this->_entity_manager          = $_entity_manager;
	    $this->_bout_repository         = $boutRepository;
	    $this->_recharge_repo           = $_recharge_repo;
        $this->_boutique_manager        = $_boutique_manager;
        $this->_produit_physique_repo   = $_produit_physique_repo;
        $this->_produit_code_repo       = $_produit_code_repo;
        $this->categories_repo          = $categories_repo;
        $this->_frais_service_repo      = $_frais_service_repo;
        $this->_frais_service_bout_repo = $_frais_service_bout_repo;
        $this->grille_tarifaire_repository = $grilletarifaireRepository;
        $this->grille_tarifaire_boutique_repository = $grilletarifaireBoutiqueRepository;
	}

    /**
     *@Route("/recharge", name="recharge")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function index(SerializerInterface $serializer, AledaController $aleda, SessionInterface $session)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $categories = $this->categories_repo->findBy(["type" => "2"]);

        $sessionCatalogues = $session->get('catalogues', null); // null est la valeur par défaut si la clé n'existe pas

        if ($sessionCatalogues !== null) {
            $catalogues = $sessionCatalogues;
        } else {
            $catalogues = $aleda->getCatalogueProduit();
            $session->set('catalogues', $catalogues);
        }

        $categories = array_map(function ($categorie) {
            $categProduit = $categorie->getProduitPhysiques();
            $allCategProd = [];
            
            if(count($categProduit)) {
                foreach ($categProduit as $categ_produit) {
                    $verifCode = false;
                    foreach ($categ_produit->getProduitPhysiqueCodes() as $pCode) {
                        if ($pCode->getStatus() == "pending") {
                            $verifCode = true;
                            break;
                        }
                    }
                    if ($verifCode) {
                        $allCategProd[] = [
                            'nom' => $categ_produit->getNom(),
                            'prix' => $categ_produit->getPrixVente(),
                            'description' => $categ_produit->getDescription(),
                            'instruction' => $categ_produit->getInstruction(),
                            'gencode' => $categ_produit->getGencode(),
                            'isVisible' => $categ_produit->getIsVisible(),
                            'isNew' => $categ_produit->getIsProductNew(),
                            'operateur' => $categ_produit->getOperateur()->getNom(),
                            'operateurImage' => $categ_produit->getOperateur()->getLogo() ? $categ_produit->getOperateur()->getLogo()->getUrlFichier() : ""
                        ];
                    }
                }
            };

            return [
                "id" => $categorie->getId(),
                "nom" => $categorie->getNom(),
                "description" => $categorie->getDescription(),
                "produits" => $allCategProd
            ];

        }, $categories);

        $produitPhisique = $this->_produit_physique_repo->findBy(["categorie" => null]);

        $produitPhisique = array_map(function ($produit) {
            $physiqueCodes = $produit->getProduitPhysiqueCodes();
            $prodPhysiqueCodes = false;
            
            if(count($physiqueCodes)) {
                $getProdPhysiqueCodes = $this->_produit_code_repo->findBy(["produit_physique" => $produit]);
                foreach ($getProdPhysiqueCodes as $pCode) {
                    if ($pCode->getStatus() == "pending") {
                        $prodPhysiqueCodes = true;
                        break;
                    }
                }
            }

            return [
                    'nom' => $produit->getNom(),
                    'prix' => $produit->getPrixVente(),
                    'description' => $produit->getDescription(),
                    'gencode' => $produit->getGencode(),
                    'isVisible' => $produit->getIsVisible(),
                    'isNew' => $produit->getIsProductNew(),
                    'isCode' => $prodPhysiqueCodes,
                    'operateur' => $produit->getOperateur()->getNom(),
                    'instruction' => $produit->getInstruction()
                ];
        }, $produitPhisique);

        return $this->render('FrontOffice/recharge/index.html.twig', [
            'produitPhysique' => $produitPhisique,
            'categories'     => $categories,
            'catalogues'     => $catalogues
        ]);
    }

    /**
     * @Route("/save-rechargement-telephonie", name="save_rechargement_telephonie")
     */
    public function saveRechargementTelephonie(Request $request){
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $dateFacture = new DateTime('now', new DateTimeZone('UTC'));
        $dateFacture = $dateFacture->setTimezone(new DateTimeZone('Europe/Paris'));

        $recharge = $request->request->get("recharge");
        $tva = $request->request->get("tva");
        $logo = $request->request->get("img", null);
        $validity = $request->request->get("validity");

        $user = $this->getUser();

        $_credit = $this->_boutique_manager->creditBoutique($user);
        $_debit  = $this->_boutique_manager->debitBoutique($user);
        $_geste  = $this->_boutique_manager->gesteBoutique($user);
        $sold_Bout = (float)($_credit + $_geste - $_debit);
        
        $bout = $this->_bout_repository->findOneByUser($this->getUser());

        $articles = [
            "articles" => $recharge["articles"]
        ];

        $produitPhisique = $this->_produit_physique_repo->findOneByGencode($recharge["productInformations"]["productCode"]);
        
        if($produitPhisique){
            $instruction = $produitPhisique->getInstruction();
            $recharge["productInformations"]["instruction"] = $instruction;
        }

        $productInformations = [
            "productInformations" => $recharge["productInformations"]
        ];

        $voucher = [
            "voucher" => $recharge["voucher"]
        ];

        $newRecharge = new Recharge();

        $amount = $this->arrondirMontant($recharge["amount"]);

        $grille = $this->grille_tarifaire_repository->findOneBy(['gencode' => $recharge["productInformations"]["productCode"]]);
        $fraisService = $this->grille_tarifaire_repository->findOneBy(['gencode' => $recharge["productInformations"]["productCode"]]);//$this->_frais_service_repo->findOneByType("4");
        $fraisServiceBout = $this->grille_tarifaire_boutique_repository->findOneBy(['grille_tarifaire' => $grille, 'boutique' => $bout]);//$this->_frais_service_bout_repo->findOneBy(["boutique" => $bout]);

        $frais = $fraisService->getRemisePdv();

        if ($fraisServiceBout) {
            $fraisBoutique = (float)$fraisServiceBout->getCommDistrib();
        } else {
            $fraisBoutique = (float)$fraisService->getCommissionDistrib();
        }

        $pourcentageBoutique = $amount * ($fraisBoutique / 100);
        $fraisInit = 0;
        if ($fraisServiceBout = 0) {
            $fraisInit = $amount - (($amount) / ( 1 + ($tva/100)));
        }

        $debitSold = (int)(($amount - $pourcentageBoutique) / ( 1 + ($tva/100)));

        $new_solde = $sold_Bout - (int)$debitSold;

        $newRecharge->setBoutique($bout);
        $newRecharge->setArticles($articles);
        $newRecharge->setSaleRef($recharge["saleRef"]);
        $newRecharge->setInternalRef($recharge["internalRef"]);
        $newRecharge->setMontant($amount);
        $newRecharge->setTva((float)$tva);
        $newRecharge->setProcessState($recharge["processState"]);
        $newRecharge->setSaleDate($dateFacture);
        $newRecharge->setProductInformations($productInformations);
        $newRecharge->setQty($recharge["qty"]);
        $newRecharge->setVoucher($voucher);
        $newRecharge->setFrais($frais);
        $newRecharge->setFraisBoutique(($fraisServiceBout = 0) ? $fraisInit : $pourcentageBoutique);

        $this->_entity_manager->persist($newRecharge);
        $this->_entity_manager->flush();

        $_desc = "Carte ".$grille->getProduit()." de $amount €";

        $_debits = new Debit();
        $_debits->setDate($dateFacture);
        $_debits->setMontant($debitSold);
        $_debits->setBout($bout);
        $_debits->setAdmin(null);
        $_debits->setNote($_desc);
        $this->_entity_manager->persist($_debits);
        $this->_entity_manager->flush();

        try {
            $produit = [
                "operateur" => $recharge["productInformations"]['operator'],
                "image" => $logo,
                "amount" => $amount,
                "tva" => $tva,
                "validity" => $validity,
                "description" => $recharge["productInformations"]['description'],
                "productCode" => $recharge["productInformations"]['productCode']
            ];

            $recharge["articles"][0]['validityDate'] = $dateFacture;

            $infosFacture = [
                "recharge"  => $newRecharge->getId(),
                "product"  => $produit,
                "boutique"  => $bout->getNom(),
                "articles" => $recharge["articles"][0],
                "productInformations" => $recharge["productInformations"],
                "dateFacture" => $dateFacture
            ];
        } catch (\Exception $e) {
            $infosFacture = [];
        }

        return new JsonResponse([
            'solde' => $new_solde,
            'infosFacture' => $infosFacture
        ]);
    }

    /**
     * @Route("/save-rechargement-code", name="save_rechargement_code")
     */
    public function saveRechargementCode(Request $request){
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $dateFacture = new DateTime('now', new DateTimeZone('UTC'));
        $dateFacture = $dateFacture->setTimezone(new DateTimeZone('Europe/Paris'));

        $produit = $request->request->get("produit");

        $bout = $this->_bout_repository->findOneByUser($this->getUser());

        $user = $this->getUser();

        $_credit = $this->_boutique_manager->creditBoutique($user);
        $_debit  = $this->_boutique_manager->debitBoutique($user);
        $_geste  = $this->_boutique_manager->gesteBoutique($user);
        $sold_Bout = (float)($_credit + $_geste - $_debit);

        $produitPhysique = $this->_produit_physique_repo->findOneByGencode($produit["productCode"]);
        $produitPhysiqueCode = $this->_produit_code_repo->findOneBy(["produit_physique" => $produitPhysique, "status" => "pending"]);
        $produitPhysiqueCode->setStatus("SOLD");
        $this->_entity_manager->persist($produitPhysiqueCode);
        $this->_entity_manager->flush();

        $amount = $this->arrondirMontant($produit["amount"]);

        $fraisService = $this->_frais_service_repo->findOneByType("6");
        $fraisServiceBout = $this->_frais_service_bout_repo->findOneBy(["boutique" => $bout,"type" => "6"]);

        $frais = $fraisService->getPourcentage();

        if ($fraisServiceBout) {
            $fraisBoutique = (float)$fraisServiceBout->getPourcentage();
        } else {
            $fraisBoutique = (float)$fraisService->getPourcentageBoutique();
        }

        $pourcentageBoutique = $amount * ($fraisBoutique / 100);

        $debitSold = $amount - $pourcentageBoutique;

        $new_solde = $sold_Bout - $debitSold;

        $articles = [
            "articles" => [
                "pincode"      => $produitPhysique->getIsProductNew() ? "PRODHORSAPI" : "PRODCODE",
                "serialNumber" => $produitPhysiqueCode->getCode(),
                "validityDate" => $dateFacture,
                "status"       => "SOLD"
            ]
        ];

        $productInformations = [
            "productInformations" => [
                "productCode"  => $produit["productCode"],
                "codeArticle"  => $produitPhysiqueCode->getCode(),
                "description"  => $produit["description"],
                "instruction"  => $produit["instruction"],
                "validity"     => $produit["validity"],
                "operator"     => $produit["operateur"],
                "brand"        => $produit["operateur"],
                "cancellable"  => "true",
                "sellingPrice" => $amount
            ]
        ];

        $voucher = [
            'voucher' => [
                'output' => 'PDF',
                'base64Content' => ""
            ]
        ];

        $_year = ($dateFacture)->format('Y');
        $unicode = mt_rand(100, 999);
        // 2024001VNT004
        $_sale_ref = (int)$_year . "VNT" . $unicode;

        $internal_ref = $produitPhysiqueCode->getCode() . "VNT" . $unicode;

        $newRecharge = new Recharge();

        $newRecharge->setBoutique($bout);
        $newRecharge->setArticles($articles);
        $newRecharge->setSaleRef($_sale_ref);
        $newRecharge->setInternalRef($internal_ref);
        $newRecharge->setMontant($amount);
        $newRecharge->setTva($produit["tva"]);
        $newRecharge->setProcessState("SOLD");
        $newRecharge->setSaleDate($dateFacture);
        $newRecharge->setProductInformations($productInformations);
        $newRecharge->setQty(1);
        $newRecharge->setVoucher($voucher);
        $newRecharge->setFrais($frais);
        $newRecharge->setFraisBoutique($pourcentageBoutique);

        $this->_entity_manager->persist($newRecharge);
        $this->_entity_manager->flush();

        $_desc = "Réservation de $amount €";

        $_debits = new Debit();
        $_debits->setDate($dateFacture);
        $_debits->setMontant($debitSold);
        $_debits->setBout($bout);
        $_debits->setAdmin(null);
        $_debits->setNote($_desc);
        $this->_entity_manager->persist($_debits);
        $this->_entity_manager->flush();

        try {
            $infosFacture = [
                "recharge"  => $newRecharge->getId(),
                "product"  => $produit,
                "boutique"  => $bout->getNom(),
                "articles" => [
                    "pincode"      => $produitPhysique->getIsProductNew() ? "PRODHORSAPI" : "PRODCODE",
                    "serialNumber" => $produitPhysiqueCode->getCode(),
                    "validityDate" => $dateFacture,
                    "status"       => "SOLD"
                ],
                "productInformations" => [
                    "productCode"  => $produit["productCode"],
                    "codeArticle"  => $produitPhysiqueCode->getCode(),
                    "description"  => $produit["description"],
                    "operator"     => $produit["operateur"],
                    "brand"        => $produit["operateur"],
                    "cancellable"  => "true",
                    "sellingPrice" => $amount
                ],
                "dateFacture" => $dateFacture
            ];
        } catch (\Exception $e) {
            $infosFacture = [];
        }

        return new JsonResponse([
            'solde' => $new_solde,
            'infosFacture' => $infosFacture
        ]);
    }

    /**
     * @Route("/edit-rechargement-telephonie", name="edit_rechargement_telephonie")
     */
    public function editRechargementTelephonie(Request $request, RechargeRepository $rechargeRepo){
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $recharge = $request->request->get("recharge");
        $idRrecharge = $request->request->get("idRecharge");

        $articles = [
            "articles" => $recharge["articles"]
        ];

        $productInformations = [
            "productInformations" => $recharge["productInformations"],
            "terminalRef" => $recharge["terminalRef"]
        ];

        $rechargeEdit = $rechargeRepo->findOneById($idRrecharge);

        $rechargeEdit->setArticles($articles);
        $rechargeEdit->setSaleRef($recharge["saleRef"]);
        $rechargeEdit->setInternalRef($recharge["internalRef"]);
        $rechargeEdit->setMontant($recharge["amount"]);
        $rechargeEdit->setProcessState($recharge["processState"]);
        $rechargeEdit->setSaleDate(new \Datetime($recharge["saleDate"]));
        $rechargeEdit->setProductInformations($productInformations);
        $rechargeEdit->setQty($recharge["qty"]);

        $this->_entity_manager->persist($rechargeEdit);
        $this->_entity_manager->flush();

        return new JsonResponse(true);
    }

    /**
     * @Route("/verif-sold-bout-recharge", name="verif_sold_bout_recharge")
     */
    public function verifSoldeBoutRecharge(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $_bout = $this->getUser();

        $tarif = (float)$request->request->get('tarif');
        $amount = $this->arrondirMontant($tarif);

        $_credit = $this->_boutique_manager->creditBoutique($_bout);
        $_debit  = $this->_boutique_manager->debitBoutique($_bout);
        $_geste  = $this->_boutique_manager->gesteBoutique($_bout);
        $_solde  = (float)($_credit + $_geste - $_debit);

        if ($amount > $_solde) {
            return new JsonResponse([
                'solde' => false,
                'message' => 'Votre solde actuel ne permet pas de réaliser cette recharge. Veuillez recharger votre compte.',
                'infos' => "$amount > $_solde"
            ]);
        }

        return new JsonResponse([
            'solde' => $_solde,
            'message' => 'ok',
        ]);
    }

    /**
     * @Route("/liste-offre-operateur", name="liste_offre_operateur")
    */
    public function listeOffreoperateur(Request $request){

        $_off = $request->request->get('off');

        return new JsonResponse([
            'html' => $this->renderView('FrontOffice/recharge/result_liste_offre.html.twig', [
                "off" => $_off, 
            ])
        ]);
    }

    /**
     * @Route("/liste-offre-selectionner", name="liste_offre")
    */
    public function listeOffre(Request $request){

    	$_user     = $this->getUser();
    	$_boutique = $this->_bout_repository->findOneBy(['user' => $_user ]);
        $_montant  = $request->request->get('montant');
        $_nom 	   = $request->request->get('nom');

        return new JsonResponse([
            'html' => $this->renderView('FrontOffice/recharge/result_offre.html.twig', [
                "montant" => $_montant, 
				"nom_off" => $_nom
            ]),
            'nom_boutique' => $_boutique->getNom(), 
            "email" => $_user->getEmail()
        ]);
    }

    /**
     *@Route("/historique-recharge", name="hist_recharge")
     *@IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function HistRecharge()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $boutique = $this->_bout_repository->findOneBy(['user' => $this->getUser()]);
        $recharge = $this->_recharge_repo->findBy(['boutique' => $boutique]);

        return $this->render('FrontOffice/recharge/hist.html.twig', [
            'recharges' => $recharge
        ]);
    }

    /**
     *@Route("/preparation-ticket", name="preparation_ticket")
     */
    public function preparationTicket(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $infosFacture  = $request->request->get('infosFacture');
        $base64Content = $this->facturationPdfRecharge(json_encode($infosFacture));

        $voucher = [
            'voucher' => [
                'output' => 'PDF',
                'base64Content' => $base64Content
            ]
        ];

        $recharge = $this->_recharge_repo->findOneById($infosFacture['recharge']);
        $recharge->setVoucher($voucher);

        $this->_entity_manager->persist($recharge);
        $this->_entity_manager->flush();

        return new JsonResponse([
            'base64Content' => $base64Content
        ]);

    }

    public function facturationPdfRecharge($infosFacture)
    {
        $infosFacture = json_decode($infosFacture, true);
        // Génération du code-barres
        $barcodeGenerator = new BarcodeGeneratorPNG();
        $barcode = $barcodeGenerator->getBarcode($infosFacture['product']['productCode'], $barcodeGenerator::TYPE_CODE_128);
        // Encode l'image en base64 pour l'utiliser directement dans le PDF
        $barcodeBase64 = base64_encode($barcode);

        $urlInfo = parse_url($infosFacture['product']['image']);

        // Vérifier si le lien contient un schéma (http ou https)
        if (isset($urlInfo['scheme']) && ($urlInfo['scheme'] === 'http' || $urlInfo['scheme'] === 'https')) {
            $imageData = base64_encode(file_get_contents($infosFacture['product']['image']));
            $imageType = $this->getTypeImageFromUrl($infosFacture['product']['image']);
        } else {
            $imagePath = $this->kernel->getProjectDir() . '/public'. $infosFacture['product']['image'];
            // Lire le contenu de l'image et le convertir en base64
            if (file_exists($imagePath)) {
                $imageData = base64_encode(file_get_contents($imagePath));
                $imageType = mime_content_type($imagePath);
            } else {
                $imageData = null; // ou une image par défaut
                $imageType = null;
            }
        }


        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Helvetica');
        $pdfOptions->set('isHtml5ParserEnabled', true); // Assurez que HTML5 est activé
        $pdfOptions->set('isPhpEnabled', true); // Pour plus de compatibilité avec du PHP
        $pdfOptions->set('defaultMediaType', 'print');
        $pdfOptions->set('defaultPaperSize', 'a4');
        $pdfOptions->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($pdfOptions);

        $dompdf->loadHtml($this->renderView('FrontOffice/recharge/facturation.html.twig', [
            'title' => 'Facture de carte recharge',
            'infosFacture' => $infosFacture,
            'barcodeBase64' => $barcodeBase64,
            'image_base64' => $imageData,
            'image_type' => $imageType
        ]));

        $dompdf->setPaper([0, 0, 226, 780], 'portrait'); // 80 mm de large et 297 mm de long */
        //Rendre le PDF
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        // Convertit le PDF en base64
        $pdfBase64 = base64_encode($pdfOutput);

        // $response = new Response($pdfOutput);
        // $response->headers->set('Content-Type', 'application/pdf');
        // $response->headers->set('Content-Disposition', 'attachment; filename="facture_carte_recharge' . $infosFacture['product']['description'] . '.pdf"');

        return $pdfBase64;
        // return $this->render('FrontOffice/recharge/facturation.html.twig', [
        //     'infosFacture' => $infosFacture
        // ]);
    }

    public function getTypeImageFromUrl(string $url): string
    {
        // Extraire l'extension de l'URL
        $extension = pathinfo($url, PATHINFO_EXTENSION);

        // Associer l'extension au type MIME
        $mimeTypes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'svg'  => 'image/svg+xml',
            'webp' => 'image/webp',
        ];

        // Retourner le type MIME en fonction de l'extension
        return $mimeTypes[strtolower($extension)] ?? 'image/png';
    }

    public function arrondirMontant(float $montant): string
    {
        $montantArrondi = ceil($montant * 10) / 10;

        return number_format($montantArrondi, 2, '.', '');
    }
}