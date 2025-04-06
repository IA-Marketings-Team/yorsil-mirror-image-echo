<?php

namespace App\Controller\BackOffice;

use App\Entity\GrilleTarifaire;
use App\Entity\GrilleTarifaireBoutique;
use App\Form\BackOffice\CategorieType;
use App\Repository\BoutRepository;
use App\Repository\GrilleTarifaireRepository;
use App\Repository\GrilleTarifaireBoutiqueRepository;
use App\Repository\ProduitRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GrilleTarifaireController extends AbstractController
{

    public function __construct(
        EntityManagerInterface $_entity_manager,
        BoutRepository $boutRepository,
        GrilleTarifaireRepository $grilletarifaireRepository,
        GrilleTarifaireBoutiqueRepository $grilletarifaireBoutiqueRepository,
        ProduitRepository $produitRepository
    )
    {
        $this->_entity_manager = $_entity_manager;
        $this->boutRepository = $boutRepository;
        $this->grille_tarifaire_repository = $grilletarifaireRepository;
        $this->grille_tarifaire_boutique_repository = $grilletarifaireBoutiqueRepository;
        $this->produit_repository = $produitRepository;
    }

    /**
     * @Route("/liste-des-grilles-tarifaires", name="grilles_tarifaires")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function index(): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('BackOffice/grille/index.html.twig', [
            'grilles' => $this->grille_tarifaire_repository->findAll(),
        ]);
    }

    /**
     * @Route("/import-fichier-liste-grille-tarifaire", name="import_fichier_grille_tarifaire")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function impoertFichierGrilleTarifaire(Request $request)
    {
    
        //$_boutique   = $this->boutRepository->findOneBy(['id' => $request->request->get('id')]);
        $_admin      = $this->getUser();

        $file = $request->files->get('fileInput');
        if ($file) {
            // Charger le fichier avec PhpSpreadsheet
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Traiter chaque ligne
            foreach ($rows as $key => $row) {
                if ($key === 0) {
                    // Ignorer la première ligne si elle contient des en-têtes
                    continue;
                }

                // Exemple : supposez que votre table a 4 colonnes
                $col1 = $row[0];
                $col2 = $row[1];
                $col3 = $row[2];
                $col4 = $row[3];
                $col5 = $row[4];
                $col6 = isset($row[5]) ? (float) $row[5] : null;
                $col7 = isset($row[6]) ? (float) $row[6] : null;
                $col8 = isset($row[7]) ? (float) $row[7] : null;
                $col9 = isset($row[8]) ? (float) $row[8] : null;
                $col10 = isset($row[9]) ? (float) $row[9] : null;
                $col11 = isset($row[10]) ? (float) $row[10] : null;
                $col12 = $row[11];

                //dd($col1, $col2, $col3, $col4,$col5, $col6, $col7, $col8,$col9, $col10,$col11,$col12);
                $this->saveToDatabase($col1, $col2, $col3, $col4,$col5, $col6, $col7, $col8,$col9, $col10,$col11);
            }

            $this->addFlash('success', 'Fichier importé avec succès !');
            
            return $this->redirectToRoute('grilles_tarifaires');
                // Sauveg
        }

        $this->addFlash('danger', 'Fichier non importé !');

        return $this->redirectToRoute('grilles_tarifaires');
    }

    private function saveToDatabase(string $famille = null, string $prod = null, string $annulable = null, string $ref = null, int $gencode = null,float $tva = null,float $_prix_pdv = null, float $_prix_yorsil = null,float $_remise = null,float $_prix_ttc = null,float $_pourcentage_yorsil = null): void
    {
        $rec = $this->grille_tarifaire_repository->findOneBy(['gencode' => $gencode]);
        if (!$rec) {
            $commission_distrib = 1;
            // if ($_pourcentage_yorsil) {
            //     if (is_numeric($_pourcentage_yorsil)) {
            //         Calculer la commission (40% de targetJK, mais ne pas dépasser targetJK)
            //         $target = $_pourcentage_yorsil * 100;
            //         $commission_distrib = min($target * 0.4, $target);
            //     }
            // }

            $grille = new GrilleTarifaire();
            $grille->setFamille($famille );
            $grille->setProduit($prod);
            ($annulable == 'OUI') ? $grille->setAnnulable(true) : $grille->setAnnulable(false);
            $grille->setRef($ref);
            $grille->setGencode($gencode);
            $grille->setTva(($tva*100));
            $grille->setPrixPdv($_prix_pdv);
            $grille->setPrixYorsil($_prix_yorsil);
            $grille->setRemisePdv(($_remise*100));
            $grille->setPrixPublicTtc($_prix_ttc);
            $grille->setPourcentageYorsil(($_pourcentage_yorsil*100));
            $grille->setCommissionDistrib($commission_distrib);
            $grille->setDateAjout(new \DateTime());

            $this->_entity_manager->persist($grille);
            $this->_entity_manager->flush();
        }
    }

    /**
     * @Route("/modif-grille-tarifaire", name="modif_grille_tarifaire")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function modifGrilleTarifaire(Request $request)
    {

        $grille = $this->grille_tarifaire_repository->findAll();
        for ($i=0; $i < count($grille); $i++) { 
            $grille[$i]->setCommissionDistrib(1);
            $this->_entity_manager->persist($grille[$i]);
            $this->_entity_manager->flush();
        }

        $this->addFlash('success', 'Modification du commission du distributeur réussi avec succès !');
            
        return $this->redirectToRoute('grilles_tarifaires');

    }

    /**
     * @Route("/liste-des-grilles-tarifaires-boutiques", name="grilles_tarifaires_boutiques")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function grillesTarifairesBoutiques(): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $grilles = $this->grille_tarifaire_repository->findAll();

        return $this->render('BackOffice/grille/grille_tarifaire_boutique.html.twig', [
            'grilles_tarifaire_boutiques' => $this->grille_tarifaire_boutique_repository->findAll(),
            'grilles' => $grilles,
            'boutiques' => $this->boutRepository->findAll()
        ]);
    }

     /**
     * @Route("/ajout-grille-tarifaire-boutique", name="ajout_grille_tarifaire_boutique")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutGrilleTarifaireBoutique(Request $request)
    {
        $_id_bout = $request->request->get('boutique');
        $_id_prod = $request->request->get('produit');
        $_distrib = $request->request->get('commission_distrib');
        $_yorsil = $request->request->get('commission_yorsil');

        $_boutique     = $this->boutRepository->findOneBy(['id' => $_id_bout]);
        $_grille_tarif = $this->grille_tarifaire_repository->findOneBy(['id' => $_id_prod]);

        $_grille_rec = $this->grille_tarifaire_boutique_repository->findOneBy(['grille_tarifaire' => $_grille_tarif, 'boutique' => $_boutique]);
        if ($_grille_rec) {
            $_grille_rec->setDateAjout(new \DateTime());
            $_grille_rec->setCommYorsil(($_yorsil-$_distrib));
            $_grille_rec->setCommDistrib($_distrib);
            $this->_entity_manager->persist($grille);
            $this->_entity_manager->flush();

            $this->addFlash('success', 'Modification de grille tarifaire boutique effectué avec succès !');

            return $this->redirectToRoute('grilles_tarifaires_boutiques'); 
        }
        $grille = new GrilleTarifaireBoutique(); 
        $grille->setGrilleTarifaire($_grille_tarif);
        $grille->setBoutique($_boutique);
        $grille->setDateAjout(new \DateTime());
        $grille->setCommYorsil((float)($_yorsil-$_distrib));
        $grille->setCommDistrib((float)($_distrib));

        $this->_entity_manager->persist($grille);
        $this->_entity_manager->flush();

        $this->addFlash('success', 'Ajout  de grille tarifaire boutiqueeffectué avec succès !');

        return $this->redirectToRoute('grilles_tarifaires_boutiques');   
    }

}