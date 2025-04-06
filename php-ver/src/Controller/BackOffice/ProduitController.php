<?php

namespace App\Controller\BackOffice;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\Stock;
use App\Form\ExcelUploadType;
use App\Entity\ProduitPhysique;
use App\Entity\ProduitPhysiqueCode;
use App\Form\BackOffice\CategorieType;
use App\Form\BackOffice\ProduitType;
use App\Form\BackOffice\ProduitPhysiqueType;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Repository\ProduitPhysiqueRepository;
use App\Repository\ProduitPhysiqueCodeRepository;
use App\Service\UploadFileService;
use App\Service\Metier\ServiceMetierProduit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{

    public function __construct(
        EntityManagerInterface $_entity_manager,
        CategorieRepository $categorieRepository,
        UploadFileService $uploadFileService,
        ServiceMetierProduit $_produit_manager,
        ProduitRepository $produitRepository,
        ProduitPhysiqueRepository $produitPhysiqueRepository,
        ProduitPhysiqueCodeRepository $produitPhysiqueCodeRepository
    )
    {
        $this->_entity_manager = $_entity_manager;
        $this->_uploadFileService = $uploadFileService;
        $this->categorie_repository = $categorieRepository;
        $this->_produit_manager   = $_produit_manager;
        $this->_produit_repository = $produitRepository;
        $this->_produit_physique_repository = $produitPhysiqueRepository;
        $this->_produit_physique_code_repository = $produitPhysiqueCodeRepository;
    }

    /**
     * @Route("/liste-des-produits", name="produit")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function index(): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('BackOffice/produit/index.html.twig');
    }

    /**
    * @Route("/liste-produit", name="list_ajax_produit")
    * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
    */
    public function listeAjaxProduit(Request $request){

        $_page           = $request->query->get('start');
        $_nb_max_page    = $request->query->get('length');
        $_search         = $request->query->get('search')['value'];
        $_order_by       = $request->query->get('order_by');

        $_liste = $this->_produit_manager->listeProduits($_page, $_nb_max_page, $_search, $_order_by);

        return new JsonResponse([
            'recordsTotal'    => $_liste[1],
            'recordsFiltered' => $_liste[1],
            'data'            => array_map(function ($val) {
                $_response = array_values($val);
                return $_response;
            }, $_liste[0])
        ]);
    }

    /**
     * @Route("/ajout-produit", name="new_produit")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutProduit(Request $request): Response
    {   
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $fichier = $form->get('images')->getData();
            $_slug   = $this->_produit_manager->getSlugProduit($produit);
            $produit->setSlug($_slug);

            if ($fichier) {
                $_produit_file = $this->_uploadFileService->saveFile($fichier, 'produit_photo', '/uploads/images/produits/');
                if ($_produit_file) $produit->setFichier($_produit_file);
            }

            $_is_promotion   =  $request->request->get('is_promotion');
            ($_is_promotion) ? $produit->setIsPromo(1) : $produit->setIsPromo(0);

            $this->_entity_manager->persist($produit);
            $this->_entity_manager->flush();
            
            $this->addFlash(
                'success',
                "Insertion effectuée avec succès"
            );
            return $this->redirectToRoute('produit');
        }

        return $this->render('BackOffice/produit/ajout.html.twig', [
            'produit' => $produit,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ajout-stock", name="ajout_stock")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutStock(Request $request)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
    
        $produit = $this->_produit_repository->findOneBy(['id' => $request->request->get('produit_id')],[]);

        $stock = new Stock();
        $stock->setQte($request->request->get('qte'));
        $stock->setDateEntree(new \DateTime());
        $stock->setProduit($produit);    

        $this->_entity_manager->persist($stock);
        $this->_entity_manager->flush();

        $this->addFlash(
            'success',
            "Ajout de quantité effectué avec succès"
        );
        return $this->redirectToRoute('produit');
    }

    /**
     * @Route("/{id}/modification-produit", name="edit_produit")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function modificationProduit(Request $request, Produit $produit)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $edit_produits = $this->createForm(ProduitType::class, $produit);
        $edit_produits->handleRequest($request);

        if($edit_produits->isSubmitted() && $edit_produits->isValid()){
            $produit_img = $edit_produits->get('images')->getData();

            if ($produit_img) {
                $file_produit_edit = $this->_uploadFileService->saveFile($produit_img, 'produit_photo', '/uploads/images/produits/', $produit->getFichier());
                if ($file_produit_edit) $produit->setFichier($file_produit_edit);
            }

            $_is_promotion   =  $request->request->get('is_promotion');
            ($_is_promotion) ? $produit->setIsPromo(1) : $produit->setIsPromo(0);

            $this->_entity_manager->persist($produit);
            $this->_entity_manager->flush();
            
            $this->addFlash(
                'success',
                "Modification effectuée avec succès"
            );
            return $this->redirectToRoute('produit');
        }
        
        return $this->render('BackOffice/produit/modification.html.twig',[
            'produit' => $produit,
            'edit_form' => $edit_produits->createView(),
            'is_promotion' => ($produit->getIsPromo()) ? 1 : 0 
        ]);
    }

    /** 
     * @Route("/{id}/suppression-produit", name="remove_produit")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function suppresssionProduit(Produit $produit)
    {
        // $panier = $session->get('panier', []);
        
        // if (!empty($panier[$id])) {
        //     unset($panier[$id]);
        // }

        // $session->set('panier', $panier);
    
        $this->_entity_manager->remove($produit);
        $this->_entity_manager->flush();
        $this->addFlash(
            'success',
            "Suppression effectuée avec succès"
        );

        return $this->redirectToRoute('produit');
    }

    /**
     * @Route("/liste-des-produits-virtuels", name="produit_physique")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function indexProduitPhysique(): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_produits = $this->_produit_physique_repository->findAll();

        return $this->render('BackOffice/produit/produit_physique/index.html.twig',[
            'produits' => $_produits
        ]);
    }

    /**
     * @Route("/ajout-produit-virtuel", name="new_produit_physique")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutProduitPhysique(Request $request): Response
    {   
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $produit = new ProduitPhysique();
        $form = $this->createForm(ProduitPhysiqueType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $_is_visible     = $request->request->get('is_visible');
            $_is_product_new = $request->request->get('is_product_new');

            ($_is_visible) ? $produit->setIsVisible(1) : $produit->setIsVisible(0);
            ($_is_product_new) ? $produit->setIsProductNew(1) : $produit->setIsProductNew(0);

            $this->_entity_manager->persist($produit);
            $this->_entity_manager->flush();
            
            $this->addFlash(
                'success',
                "Insertion effectuée avec succès"
            );
            return $this->redirectToRoute('produit_physique');
        }

        return $this->render('BackOffice/produit/produit_physique/ajout.html.twig', [
            'produit' => $produit,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/modification-produit-virtuel", name="edit_produit_physique")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function modificationProduitPhysique(Request $request, ProduitPhysique $produit)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $edit_produits = $this->createForm(ProduitPhysiqueType::class, $produit);
        $edit_produits->handleRequest($request);

        if($edit_produits->isSubmitted() && $edit_produits->isValid()){

            $_is_visible     = $request->request->get('is_visible');
            $_is_product_new = $request->request->get('is_product_new');

            ($_is_visible) ? $produit->setIsVisible(1) : $produit->setIsVisible(0);
            ($_is_product_new) ? $produit->setIsProductNew(1) : $produit->setIsProductNew(0);
            
            $this->_entity_manager->persist($produit);
            $this->_entity_manager->flush();
            
            $this->addFlash(
                'success',
                "Modification effectuée avec succès"
            );
             return $this->redirectToRoute('produit_physique');
        }
        
        return $this->render('BackOffice/produit/produit_physique/modification.html.twig',[
            'produit' => $produit,
            'edit_form' => $edit_produits->createView()
        ]);
    }

    /**
     * @Route("/ajout-code-pin", name="save_code_pin")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutCodePin(Request $request): Response
    {   
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $codes   = $request->request->get('codes');
        $id      = $request->request->get('produit');
        $produit = $this->_produit_physique_repository->findOneBy(['id' => $id]);

        // Tu peux ensuite séparer les codes en tableau avec explode() pour les traiter individuellement
        $codeList = explode(PHP_EOL, trim($codes));

        
        for ($i=0; $i < count($codeList) ; $i++) { 
            $_code = new ProduitPhysiqueCode();
            $_code->setStatus('pending');
            $_code->setCode($codeList[$i]);
            $_code->setProduitPhysique($produit);

            $this->_entity_manager->persist($_code);
            $this->_entity_manager->flush();
        }        
        
        $this->addFlash(
                'success',
                "Insertion effectuée avec succès"
            );

        return $this->redirectToRoute('liste_code',['id' =>  $id]);
    }

    /**
     * @Route("/{id}/liste-code", name="liste_code")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function listeCode(Request $request,String $id): Response
    {   
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_produit = $this->_produit_physique_repository->findOneBy(['id' => $id]);
        $_codes   = $this->_produit_physique_code_repository->findBy(['produit_physique' => $_produit]);

        return $this->render('BackOffice/produit/produit_physique/liste_code.html.twig', [
            'codes' => $_codes,
            'produit' => $_produit
        ]);
    }

    /**
     * @Route("/liste-gencode-operateur", name="liste_gencode_operateur")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function listeGencodeOperateur(Request $request): Response
    {   
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ExcelUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('excelFile')->getData();
dd($file);
        }

        return $this->render('BackOffice/produit/produit_physique/liste_gencode.html.twig',[
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/export-gencode-operateur", name="export_gencode_operateur")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function importGencodeOperateur(Request $request): Response
    {
        $file = $request->files->get('exportExcel');
dd($file,$request->request->all());
            // Lire et traiter le fichier Excel
            if ($file) {
                $spreadsheet = IOFactory::load($file->getPathname());
                $sheetData = $spreadsheet->getActiveSheet();

                $highestColumn = $sheetData->getHighestColumn();
                $highestRow = $sheetData->getHighestRow();
                // Loop through each row and column to build the array
                $data = [];
                //dd($highestColumn,$highestRow);    
                for ($row = 2; $row <= $highestRow; $row++) {
                    $rowData = [];
                    for ($col = 'A'; $col <= $highestColumn; $col++) {
                        $cellValue = $sheetData->getCell($col . $row)->getValue();
                        $rowData[] = $cellValue;

                    }

                    $_nom_boutique = $rowData[1];
                    $_boutique = $this->_boutique_repository->findBoutiqueByName($_nom_boutique);

                    $admin  = $this->getUser();
                    $nom    = $admin->getNom();
                    $prenom = $admin->getPrenom();
                    $fullname = $nom." ".$prenom;
                    if ($_boutique) {
                        $_planning = new Planning();
                        $date   = new \DateTime();
                        $_planning->setBout($_boutique);
                        $_planning->setDate($date);
                        $_planning->setMontant((string)$rowData[6]);
                        ($rowData[7] != null) ? $_planning->setAdmin($rowData[7]) : $_planning->setAdmin($fullname);
                        $_planning->setObserv($rowData[9]);
                        $_planning->setCreateur($rowData[8]);

                        // Persister l'entité
                        $this->_entity_manager->persist($_planning);
                    }
                   
                }
            }
        return $this->redirectToRoute('liste_gencode_operateur');
    }

}