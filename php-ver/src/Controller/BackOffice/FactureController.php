<?php 

namespace App\Controller\BackOffice;

use App\Entity\Flixbus;
use App\Entity\Recharge;
use App\Entity\RechargeFlexi;
use App\Entity\Transfert;
use ZipArchive;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\BoutRepository;
use App\Repository\CreditRepository;
use App\Repository\GrilleTarifaireRepository;
use App\Repository\FlixbusRepository;
use App\Repository\TransfertRepository;
use App\Repository\RechargeRepository;
use App\Repository\RechargeflexiRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\KernelInterface;
use Knp\Snappy\Pdf;
use App\Service\Metier\PdfService;
use App\Service\Utils\TypeFraisName;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FactureController extends AbstractController
{

    private $kernel;

    public function __construct(KernelInterface $kernel,
        PdfService $pdfService,
        BoutRepository $boutRepository,
        FlixbusRepository $_flix_repo,
        TransfertRepository $_transfertRepo,
        RechargeRepository $rechargeRepository,
        UserRepository $userRepository,
        RechargeflexiRepository $rechargeFlexiRepository,
        CreditRepository $creditRepository,
        GrilleTarifaireRepository $grilleTarifaireRepository)
    {
        $this->kernel = $kernel;
        $this->pdfService = $pdfService;
        $this->boutRepository     = $boutRepository;
        $this->creditRepository   = $creditRepository;
        $this->_flix_repo         = $_flix_repo;
        $this->_transfertRepo     = $_transfertRepo;
        $this->rechargeRepository = $rechargeRepository;
        $this->rechargeFlexiRepository = $rechargeFlexiRepository;
        $this->userRepository     = $userRepository;
        $this->grilleTarifaireRepository = $grilleTarifaireRepository;
    }

    public function generateInvoice(String $_debut,String $_fin)
    {
        // Configurer DOMPDF avec des options
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Helvetica');
        $pdfOptions->set('isRemoteEnabled', true); // Activer l'accès aux fichiers locaux
        $dompdf = new Dompdf($pdfOptions);

        // Récupérer le chemin du logo
        $imagePath = $this->kernel->getProjectDir() . '/public/modernize/images/logos/fahana.svg';
        // Lire le contenu de l'image et le convertir en base64
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageType = mime_content_type($imagePath);

        //Boutique
        $_boutique = $this->boutRepository->findOneBy(['user' => $this->getUser()]);

        //Rechargement 
        $_date_debut = new \DateTime($_debut.' 00:00:00');
        $_date_fin   = new \DateTime($_fin.' 23:59:59'); 
   
        $_rechargements = $this->creditRepository->findByMultipleValues($_date_debut,$_date_fin,$_boutique->getId());
        $_rechargements_array = array_map(function($_rechargement) {
            $grilleTarifaire = $this->grilleTarifaireRepository->findOneBy(['produit' => $_rechargement->getDescription()]);
            return [
                'id' => $_rechargement->getId(),
                'date' => $_rechargement->getDate(),
                'montant' => $_rechargement->getMontant(),
                'type' => $_rechargement->getType(),
                'ref' => $_rechargement->getRef(),
                'grilleTarifaire' => $grilleTarifaire
            ];
        }, $_rechargements);

        $_factures = $this->pdfService->dispatcherFacturesParMontant($_rechargements_array,$_boutique->getCode());
        if($_factures){
            if(count($_factures) > 1){
                // Créer un nouvel objet Zip
                $zip = new ZipArchive();
                $zipFile = 'factures.zip';
                $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

                foreach ($_factures as $facture) {
                    $dompdf = new Dompdf();
                    $html = $this->renderView('facture/rechargement.html.twig', [
                        'title' => 'Facture de rechargement',
                        'image_base64' => $imageData,
                        'image_type' => $imageType,
                        'client' => $_boutique->getCode(),
                        'debut' => $_date_debut,
                        'fin' => $_date_fin,
                        'factures' => $facture
                    ]);

                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();

                    // Sauvegarder chaque PDF temporairement en mémoire
                    $pdfOutput = $dompdf->output();
                    $ref = $facture['ref'];

                    // Ajouter le PDF au fichier ZIP
                    $zip->addFromString($ref . '.pdf', $pdfOutput);
                }

                // Fermer l'archive ZIP
                $zip->close();

                // Télécharger le fichier ZIP
                return new Response(file_get_contents($zipFile), 200, [
                    'Content-Type' => 'application/zip',
                    'Content-Disposition' => 'attachment; filename="factures.zip"',
                ]);

                // Optionnel : Supprimer le fichier zip après téléchargement
                unlink($zipFile);
            }
            // Charger le HTML dans DOMPDF
            $dompdf->loadHtml($this->renderView('facture/rechargement.html.twig', [
                'title' => 'Facture de rechargement',
                'image_base64' => $imageData,
                'image_type' => $imageType,
                'client'     => $_boutique->getCode(),
                'debut' => $_date_debut,
                'fin' => $_date_fin,
                'factures' => $_factures[0]
            ]));

            $ref = $_factures[0]['ref'];

            //Définir le format du PDF (A4)
            $dompdf->setPaper('A4', 'portrait');

            //Rendre le PDF
            $dompdf->render();

            return new Response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$ref.'"',
            ]);
        }
        return new JsonResponse([
            'response' => null 
        ]); 
    }

    /**
     * @Route("/detail-du-facture", name="facture")
    */
    public function generatePdf()
    {
        return $this->render('facture/index.html.twig');
    }

    /**
     * @Route("/generate-facture", name="generate_facture")
     * @IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
    */
    public function generateFacture(PdfService $pdfService,Request $request): Response
    {

        $_debut = $request->request->get('debut');
        $_fin = $request->request->get('fin');

        // Générer le PDF
        return $this->generateInvoice($_debut,$_fin);
    }

    /**
     * @Route("/facture-services", name="facture_services")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
    */
    public function facturationServices(Request $request)
    {

        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_id = $request->get('boutique');
        $_facturation = $request->get('mode');

        $_boutique     = $this->boutRepository->findOneBy(['id' => $_id]);
        $_reservation  = $this->_flix_repo->findByMonthsBefore($_boutique->getId());
        $_recharge     = $this->rechargeRepository->findByMonthsBefore($_boutique->getId());
        $_transfert    = $this->_transfertRepo->findByMonthsBefore($_boutique->getId());
        $_diaspo       = $this->rechargeFlexiRepository->findByMonthsBefore($_boutique->getUser()->getId());

        // Combine toutes les entités en un seul tableau
        $entities = array_merge($_reservation, $_recharge, $_transfert, $_diaspo);

        // Formate les données pour la facturation
        $billingItems = $this->pdfService->formatForBilling($entities);

        $imagePath = $this->kernel->getProjectDir() . '/public/modernize/images/logos/fahana.svg';
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageType = mime_content_type($imagePath);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Helvetica');
        $pdfOptions->set('isRemoteEnabled', true); // Activer l'accès aux fichiers locaux
        $dompdf = new Dompdf($pdfOptions);

        $dompdf->loadHtml($this->renderView('facture/services.html.twig', [
            'title' => 'Facture de services',
            'billingItems' => $billingItems['items'],
            'client'     => $_boutique,
            'image_base64' => $imageData,
            'image_type' => $imageType,
            'total' => $billingItems['total'] ,
            'user' => $_boutique->getUser(),
            'facturation' => $_facturation
        ]));

        $dompdf->setPaper('A4', 'portrait');

        //Rendre le PDF
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        $response = new Response($pdfOutput);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="facture_boutique_' . $_boutique->getCode() . '.pdf"');

        return $response;
    }

    /**
     * @Route("/facture-services-boutique", name="facture_services_boutique")
     * @IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserver au boutique!")
    */
    public function facturationServicesBoutique(Request $request)
    {

        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_debut      = $request->request->get('date_debut');
        $_fin        = $request->request->get('date_fin');
        $_date_debut = new \DateTime($_debut.' 00:00:00');
        $_date_fin   = new \DateTime($_fin.' 23:59:59'); 

        $_type = $request->request->get('type');
        $_boutique     = $this->boutRepository->findOneBy(['user' => $this->getUser()]);
        $_reservation  = ($_type == TypeFraisName::TYPE_FLIXBUS) ? $this->_flix_repo->findByMultipleValues($_date_debut,$_date_fin,$_boutique->getId()) : [];
        $_recharge     = ($_type == TypeFraisName::TYPE_ALEDA) ? $this->rechargeRepository->findByMultipleValues($_date_debut,$_date_fin,$_boutique->getId()) : [];
        $_transfert    = ($_type == TypeFraisName::TYPE_RELOADLY) ?$this->_transfertRepo->findByMultipleValues($_date_debut,$_date_fin,$_boutique->getId()) : [];
        $_diaspo       = ($_type == TypeFraisName::TYPE_RELOADLY) ?$this->rechargeFlexiRepository->findByMultipleValues($_date_debut,$_date_fin,$this->getUser()->getId()) : [];

        $_type_b = ($_type == TypeFraisName::TYPE_FLIXBUS) ? 'Billeterie' : null; 
        $_type_r = ($_type == TypeFraisName::TYPE_ALEDA) ? 'Recharge' : null;
        $_type_t = ($_type == TypeFraisName::TYPE_RELOADLY) ? 'Transfert de crédit' : null;

        $_type_facture = $this->pdfService->getTypeService($_type_b,$_type_r,$_type_t);

        // Combine toutes les entités en un seul tableau
        $entities = array_merge($_reservation, $_recharge, $_transfert, $_diaspo);

        // Formate les données pour la facturation
        $billingItems = $this->pdfService->formatForBilling($entities);

        $imagePath = $this->kernel->getProjectDir() . '/public/modernize/images/logos/fahana.svg';
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageType = mime_content_type($imagePath);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Helvetica');
        $pdfOptions->set('isRemoteEnabled', true); // Activer l'accès aux fichiers locaux
        $dompdf = new Dompdf($pdfOptions);

        $dompdf->loadHtml($this->renderView('facture/services_boutiques.html.twig', [
            'title' => 'Facture de services',
            'billingItems' => $billingItems['items'],
            'client'     => $_boutique,
            'image_base64' => $imageData,
            'image_type' => $imageType,
            'total' => $billingItems['total'],
            'user' => $_boutique->getUser(),
            'debut' => $_date_debut,
            'fin' => $_date_fin,
            'type' => $_type_facture,
            'is_ref' => $_type,
            'is_ttc' => $_boutique->getFacturation()
        ]));

        $dompdf->setPaper('A4', 'portrait');

        //Rendre le PDF
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        $response = new Response($pdfOutput);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="facture_boutique_' . $_boutique->getCode() . '.pdf"');

        return $response;
    }
}
