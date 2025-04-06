<?php

namespace App\Controller\BackOffice;

use App\Entity\User;
use App\Entity\Credit;
use App\Entity\Depot;
use App\Entity\Percept;
use App\Entity\Seuilpercepteur;
use App\Repository\BoutRepository;
use App\Service\UploadFileService;
use App\Form\BackOffice\PerceptType;
use App\Repository\PerceptRepository;
use App\Repository\CreditRepository;
use App\Repository\DepotRepository;
use App\Repository\SeuilpercepteurRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Metier\ServiceMetierPercepteur;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PercepteurController extends AbstractController
{
    private $_entity_manager;
    private $_percepteur_manager;
    private $_bout_repo;
    private $_uploadFileService;
    private $creditRepository;
    private $perceptRepository;
    private $depotRepository;

    public function __construct(
        EntityManagerInterface $_entity_manager,
        ServiceMetierPercepteur $_percepteur_manager,
        BoutRepository $_bout_repo,
        UploadFileService $_uploadFileService,
        PerceptRepository $perceptRepository,
        CreditRepository $creditRepository,
        SeuilpercepteurRepository $seuilpercepteurRepository,
        DepotRepository $depotRepository
    )
    {
        $this->_entity_manager     = $_entity_manager;
        $this->_percepteur_manager = $_percepteur_manager;
        $this->_bout_repo          = $_bout_repo;
        $this->_uploadFileService  = $_uploadFileService;
        $this->creditRepository    = $creditRepository;
        $this->perceptRepository   = $perceptRepository;
        $this->depotRepository     = $depotRepository;
        $this->seuilpercepteurRepository = $seuilpercepteurRepository;
    }

    /**
     * @Route("/mon-tableau-de-bord", name="dashboard_percepteur")
     * @IsGranted("ROLE_PERC", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function dashboardPercepteur(PerceptRepository $perceptRepository): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_percepteur  = $this->perceptRepository->findOneBy(['compte' => $this->getUser()->getId()]);
        $_depot = $this->depotRepository->sumDepot($_percepteur->getId());
        $_rechargement = $this->creditRepository->sumRechargement($_percepteur->getId());
        $_solde = (float)($_rechargement-$_depot);
        $_seuil = $this->seuilpercepteurRepository->findOneBy(['percepteur' => $_percepteur]);
        $_montant_seuil = ($_seuil) ? $_seuil->getMontant() : '500';
   
        return $this->render('BackOffice/percepteur/dashboard.html.twig',[
            'solde' => $_solde,
            'rechargement' => $_rechargement,
            'depot' => $_depot,
            'seuil' => $_montant_seuil
        ]);
    }

    /**
     * @Route("/liste-des-percepteur", name="percepteur")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function index(PerceptRepository $perceptRepository): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('BackOffice/percepteur/index.html.twig', [
            'percepts' => $perceptRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ajout-de-percepteur", name="new_percept")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     * @param UserPasswordHasherInterface $encoder
     */
    public function Percepteur(Request $request, UserPasswordHasherInterface $encoder, EntityManagerInterface $manager, UserRepository $userRepo)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $percept = new Percept();
        $form = $this->createForm(PerceptType::class, $percept);
        $form->handleRequest($request);

        $user = new User();

        $nom = $request->request->get('percept')['nom'] ?? null;
        $prenom = $request->request->get('percept')['prenom'] ?? null;
        $numero = $request->request->get('percept')['tele'] ?? null;
        $email = $request->request->get('percept')['email'] ?? null;
        $mdp = $request->request->get('percept')['password'] ?? null;
        $confMdp = $request->request->get('percept')['confirm_password'] ?? null;
        $createur = $this->getUser();

        
        if($form->isSubmitted())
        {
            if (!empty($nom) && !empty($email) && !empty($mdp) && !empty($confMdp)) {

                $userEmail = $userRepo->findByEmail($email);

                if ($userEmail) {
                    $this->addFlash(
                        'warning',
                        "L'addresse Email ".$email." existe déjà !"
                    );

                    return $this->render('BackOffice/percepteur/percept.html.twig', [
                        'form' => $form->createView()
                    ]);
                }

                $user->setNom($nom);
                $user->setPrenom($prenom);
                $user->setTel($numero);
                $user->setEmail($email);
                $user->setRoles(['ROLE_PERC']);

                if ($mdp === $confMdp) {
                    $user->setPassword($encoder->hashPassword($user, $mdp));
                } else {
                    $this->addFlash(
                        'warning',
                        "Les mots de passe doivent être identiques."
                    );

                    return $this->redirectToRoute('percepteur');
                }

                $user->setCreateur($createur->getNom());
                $user->setIsActif(true);

                $manager->persist($user);

                $percept->setCreateur($createur->getNom());
                $percept->setCompte($user);
                $manager->persist($percept);

                $manager->flush();

                $this->addFlash(
                    'success',
                    "Le percepteur {$percept->getPrenom()} a été bien enregistrée"
                );

                return $this->redirectToRoute('percepteur');
            }
        }

        return $this->render('BackOffice/percepteur/percept.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/{id}/modification-de-percepteur", name="edit_percept")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function PercepteurEdit(Request $request,EntityManagerInterface $manager, Percept $percept, UserRepository $userRepo)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $nom = $request->request->get('percept')['nom'] ?? null;
        $prenom = $request->request->get('percept')['prenom'] ?? null;
        $numero = $request->request->get('percept')['tele'] ?? null;
        $email = $request->request->get('percept')['email'] ?? null;
        $createur = $this->getUser();

        $user = $percept->getCompte();
        
        $form = $this->createForm(PerceptType::class, $percept);
        $form->handleRequest($request);


        if($form->isSubmitted())
            {
                $userEmail = $userRepo->findOneByEmail($email);

                if ($userEmail && $userEmail->getId() !== $user->getId()) {
                    $this->addFlash(
                        'warning',
                        "L'addresse Email " . $email . " existe déjà !"
                    );

                    return $this->render('BackOffice/percepteur/modifi.html.twig', [
                        'form'       => $form->createView(),
                        'percepteur' => $percept
                    ]);
                } else if($user->getEmail() !== $email) {
                    $user->setEmail($email);
                }

                $user->setNom($nom);
                $user->setPrenom($prenom);
                $user->setTel($numero);

                $user->setCreateur($createur->getNom());

                $manager->persist($user);

                $manager->persist($percept);
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Le percepteur a été modifié avec succès"
                );

                return $this->redirectToRoute('percepteur');
            }

        return $this->render('BackOffice/percepteur/modifi.html.twig',[
            'form'       => $form->createView(),
            'percepteur' => $percept
        ]);
    }

    /**
     * @Route("/{id}/supprimer-percepeteur", name="suppr_perecpt")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function SupprPercept(EntityManagerInterface $manager, Percept $percept)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        if(count($percept->getBouts()) > 0){
            $this->addFlash(
                'warning',"Vous ne pouvez pas supprimer l'agent {$percept->getPrenom()} !"
            );
        } else{
            $manager->remove($percept);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le percepteur {$percept->getNom()} a été supprimée avec succès"
            );
        }

        return $this->redirectToRoute('percepteur');
    }


    /**
     * @Route("liste-des-percepteurs", name="perc_ajax")
     */
    public function ListeAjaxPerc(Request $request){
        $_page           = $request->query->get('start');
        $_nb_max_page    = $request->query->get('length');
        $_search         = $request->query->get('search')['value'];
        $_order_by       = $request->query->get('order_by');

        $_liste = $this->_percepteur_manager->listePercepteurs($_page, $_nb_max_page, $_search, $_order_by);

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
     * @Route("/rechargement-du-compte", name="rechargement_du_compte")
     * @IsGranted("ROLE_PERC", message="Vous ne pouvez pas accéder sur cette url, sera réservé aux Percepteurs!")
     */
    public function rechargementDuCompte(Request $request){
        $boutiques = $this->_bout_repo->findAll();

        if ($request->isMethod('POST')) {
            $_montant = (float)$request->request->get('montant');
            $_id = $request->request->get('boutique');

            $_boutique      = $this->_bout_repo->findOneBy(['id' => $_id]);
            $_percepteur    = $this->perceptRepository->findOneBy(['compte' => $this->getUser()->getId()]);
            $_last_recharge = $this->creditRepository->findOneByType('1');
            $_value_type    = 'ESP';

            if ($_last_recharge) {
                $_last_number = (int)substr($_last_recharge->getRef(), 4);
                $_new_number  = $_last_number + 1;
            } else {
                $_new_number = 1;
            }
            $new_code = $_value_type . str_pad($_new_number, 3, '0', STR_PAD_LEFT);

            $_credit = new Credit();
            $_credit->setDate(new \Datetime());
            $_credit->setMontant((float)$_montant);
            $_credit->setBout($_boutique);
            // $_credit->setAdmin($_admin);
            $_credit->setIsvalid(true);
            $_credit->setType('1');
            $_credit->setRef($new_code);
            $_credit->setPercept($_percepteur);

            $this->_entity_manager->persist($_credit);
            $this->_entity_manager->flush();

            //$_message = "La boutique {$_boutique->getNom()} a été rechargée avec succès";

            $this->addFlash('success', 'Rechargement effectué avec succès');

            return $this->redirectToRoute('rechargement_du_compte');
        }
        return $this->render('BackOffice/percepteur/rechargement.html.twig', [
            'boutiques' => $boutiques
        ]);
    }

    /**
     * @Route("/modifier-rechargement/{id}", name="rechargement_edit")
     * @IsGranted("ROLE_PERC", message="Vous ne pouvez pas accéder sur cette url, sera réservé aux Percepteurs!")
     */
    public function rechargementEdit(Request $request, Credit $_credit){
        $boutiques = $this->_bout_repo->findAll();

        if ($request->isMethod('POST')) {
            $_montant = (float)$request->request->get('montant');
            $_id = $request->request->get('boutique');

            $_boutique   = $this->_bout_repo->findOneBy(['id' => $_id]);
            $_percepteur = $this->perceptRepository->findOneBy(['compte' => $this->getUser()->getId()]);

            $_credit->setDate(new \Datetime());
            $_credit->setMontant((float)$_montant);
            $_credit->setBout($_boutique);
            // $_credit->setAdmin($_admin);
            $_credit->setType('1');
            $_credit->setPercept($_percepteur);

            $this->_entity_manager->persist($_credit);
            $this->_entity_manager->flush();

            //$_message = "La boutique {$_boutique->getNom()} a été rechargée avec succès";

            $this->addFlash('success', 'Rechargement modifier avec succès');

            return $this->redirectToRoute('journal_rechargement_percepteur');
        }
        return $this->render('BackOffice/percepteur/edit-rechargement.html.twig', [
            'rechargement' => $_credit,
            'boutiques'    => $boutiques
        ]);
    }

    /**
     * @Route("/faire-un-depot", name="faire_un_depot")
     * @IsGranted("ROLE_PERC", message="Vous ne pouvez pas accéder sur cette url, sera réservé aux Percepteurs!")
     */
    public function faireUnDepot(Request $request){
        
        if ($request->isMethod('POST')) {
            try {
                $montant = (float)$request->request->get('montant');
                $preuve  = $request->files->get('preuve');
                $note    = $request->request->get('note', null);
    
                $depot = new Depot();
    
                $_percepteur = $this->perceptRepository->findOneBy(['compte' => $this->getUser()->getId()]);
    
                $depot->setDate(new \Datetime());
                $depot->setMontant($montant);
                $depot->setPercepteur($_percepteur);
                $depot->setNote($note);
                // $depot->setIsvalid(false);
                $depot->setIsarchive(false);
    
                if ($preuve) {
                    $preuve_file = $this->_uploadFileService->saveFile($preuve, "percepteur_depot", "/uploads/images/percepteur/depot/");
                    if ($preuve_file) $depot->setFile($preuve_file);
                }

                $this->_entity_manager->persist($depot);
                $this->_entity_manager->flush();
    
                $this->addFlash('success', 'Dépot effectué avec succès');
            } catch (\Exception $_exc) {
                $this->addFlash('error', $_exc->getMessage());
            }
        }
        return $this->render('BackOffice/percepteur/depot.html.twig');
    }

    /**
     * @Route("/modifier-depot/{id}", name="modifier_un_depot")
     * @IsGranted("ROLE_PERC", message="Vous ne pouvez pas accéder sur cette url, sera réservé aux Percepteurs!")
     */
    public function modifierUnDepot(Request $request, Depot $depot){
        
        if ($request->isMethod('POST')) {
            try {
                $montant = (float)$request->request->get('montant');
                $preuve  = $request->files->get('preuve', null);
                $note    = $request->request->get('note', null);
    
                $_percepteur = $this->perceptRepository->findOneBy(['compte' => $this->getUser()->getId()]);
    
                $depot->setDate(new \Datetime());
                $depot->setMontant($montant);
                $depot->setPercepteur($_percepteur);
                $depot->setNote($note);
                $depot->setIsvalid(false);
                $depot->setIsarchive(false);
    
                if ($preuve) {
                    $preuve_file = $this->_uploadFileService->saveFile($preuve, "percepteur_depot", "/uploads/images/percepteur/depot/");
                    if ($preuve_file) $depot->setFile($preuve_file);
                }

                $this->_entity_manager->persist($depot);
                $this->_entity_manager->flush();

                $this->addFlash('success', 'Dépot modifier avec succès');
                
                return $this->redirectToRoute('journal_depot_percepteur');
            } catch (\Exception $_exc) {
                $this->addFlash('error', $_exc->getMessage());
            }
        }
        return $this->render('BackOffice/percepteur/edit-depot.html.twig', [
            "depot" => $depot
        ]);
    }

    /**
     * @Route("/journal/rechargement", name="journal_rechargement_percepteur")
     * @IsGranted("ROLE_PERC", message="Vous ne pouvez pas accéder sur cette url, sera réservé aux Percepteurs!")
     */
    public function journalRechargementPercepteur(Request $request){
        $_percepteur  = $this->perceptRepository->findOneBy(['compte' => $this->getUser()->getId()]);
        $rechargement = $this->creditRepository->findByPercept($_percepteur);
        return $this->render('BackOffice/percepteur/journal/rechargement.html.twig', [
            "rechargements" => $rechargement
        ]);
    }

    /**
     * @Route("/journal/depot", name="journal_depot_percepteur")
     * @IsGranted("ROLE_PERC", message="Vous ne pouvez pas accéder sur cette url, sera réservé aux Percepteurs!")
     */
    public function journalDepotPercepteur(Request $request){
        $_percepteur  = $this->perceptRepository->findOneBy(['compte' => $this->getUser()->getId()]);
        $depot = $this->depotRepository->findByPercepteur($_percepteur);

        return $this->render('BackOffice/percepteur/journal/depot.html.twig', [
            "depots" => $depot
        ]);
    }

    /**
     * @Route("/ajout-seuil-percepteur", name="ajout_seuil_percepteur", methods="GET|POST")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutSeuilPercepteur(Request $request)
    {

        $_percepteur = $this->perceptRepository->findOneBy(['id' => $request->request->get('id')]);
        $_admin      = $this->getUser();
        $_seuil_percept = $this->seuilpercepteurRepository->findOneBy(['percepteur' => $_percepteur]);
        $_message = "";
        if ($_seuil_percept) {
            $_seuil_percept->setDate(new \Datetime());
            $_seuil_percept->setMontant($request->request->get('montant'));
            $_seuil_percept->setAdmin($_admin);

            $this->_entity_manager->persist($_seuil_percept);
            $this->_entity_manager->flush();

            $_message = "Le percepteur {$_percepteur->getNom()} a été modifié d'une seuil de {$request->request->get('montant')} €";
        }else {
            $_seuil = new Seuilpercepteur();
            $_seuil->setDate(new \Datetime());
            $_seuil->setMontant($request->request->get('montant'));
            $_seuil->setPercepteur($_percepteur);
            $_seuil->setAdmin($_admin);

            $this->_entity_manager->persist($_seuil);
            $this->_entity_manager->flush();

            $_message = "Le percepteur {$_percepteur->getNom()} a été ajouté d'une seuil de {$request->request->get('montant')} €";
        }
        
        return new JsonResponse([
            'status'  => true,
            'message'    => $_message
        ]);
    }
    
}
