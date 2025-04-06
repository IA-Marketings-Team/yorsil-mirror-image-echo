<?php

namespace App\Controller\BackOffice;

use Swift;
use App\Entity\Bout;
use App\Entity\User;
use App\Entity\Debit;
use App\Entity\Seuil;
use Twig\Environment;
use App\Entity\Credit;
use App\Entity\Percept;
use App\Entity\PasswordUpdate;
use App\Entity\Gestecommercial;
use App\Entity\SeuilBilleterie;
use App\Form\BackOffice\BoutType;
use App\Repository\BoutRepository;
use App\Repository\UserRepository;
use App\Service\UploadFileService;
use App\Form\BackOffice\ProfilType;
use App\Form\BackOffice\AssigneType;
use App\Repository\PerceptRepository;
use App\Repository\CreditRepository;
use Symfony\Component\Form\FormError;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use SebastianBergmann\Environment\Console;
use App\Service\Metier\ServiceMetierBoutique;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Metier\ServiceMetierPercepteur;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BoutiqueController extends AbstractController
{

    private $_entity_manager;
    private $_boutique_manager;

    public function __construct(
            EntityManagerInterface $_entity_manager,
            ServiceMetierBoutique $_boutique_manager,
            UploadFileService $uploadFileService,
            BoutRepository $boutRepository,
            CreditRepository $creditRepository
    )
    {
        $this->_entity_manager   = $_entity_manager;
        $this->_boutique_manager = $_boutique_manager;
        $this->uploadFileService = $uploadFileService;
        $this->boutRepository    = $boutRepository;
        $this->creditRepository  = $creditRepository;
    }

	/**
     * @Route("/liste-des-boutiques", name="boutique")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function index(BoutRepository $boutRepository, ServiceMetierPercepteur $_percepteur_manager){
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('BackOffice/boutique/index.html.twig', [
            'bouts'       => $boutRepository->findAll(),
            'percepteurs' => $_percepteur_manager->listePercepteur()
        ]);
    }

    /**
     * @Route("/liste-boutiques", name="liste_boutique")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function listeBoutique(BoutRepository $boutRepository): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('BackOffice/bout/boutique.html.twig', [
            'bouts' => $boutRepository->findAll(),
        ]);
    }

    public function envoieMail($nom,$mdp,$email,\Swift_Mailer $mailer){

        $message = (new \Swift_Message('Inscription sur  yorsil'))
                    ->setFrom('serviceclient@yorsil.com')
                    ->setTo($email);
                    
        $logo = $message->embed(\Swift_Image::fromPath('modernize/images/logos/favicon.svg'));
        $inscriptionImg = $message->embed(\Swift_Image::fromPath('modernize/images/logos/inscription.png'));
        $message->setBody(
            $this->renderView(
                // templates/emails/registration.html.twig
                'mail/confirmation.html.twig',
                [
                    'mdp' => $mdp,
                    'nom' => $nom,
                    'logo' => $logo,
                    'inscriptionImg' => $inscriptionImg,
                ]
            )
        );

        $message->setContentType("text/html");
        $_result = $mailer->send($message);

        $_headers = $message->getHeaders();
        $_headers->addIdHeader('Message-ID', uniqid() . "@domain.com");
        $_headers->addTextHeader('MIME-Version', '1.0');
        $_headers->addTextHeader('X-Mailer', 'PHP v' . phpversion());
        $_headers->addParameterizedHeader('Content-type', 'text/html', ['charset' => 'utf-8']);
        
        if ($_result)
            return true;

        return false;
    }

    public function envoieMailMdp($nom,$mdp,$email,\Swift_Mailer $mailer){

        $message = (new \Swift_Message('Modification mot de passe sur  yorsil'))
                    ->setFrom('serviceclient@yorsil.com')
                    ->setTo($email);
        
        $logo = $message->embed(\Swift_Image::fromPath('modernize/images/logos/favicon.svg'));
        $mailImg = $message->embed(\Swift_Image::fromPath('modernize/images/logos/mail.png'));
        $message->setBody(
            $this->renderView(
                // templates/emails/registration.html.twig
                'mail/recuperation.html.twig',
                [
                    'mdp' => $mdp,
                    'nom' => $nom,
                    'logo' => $logo,
                    'mailImg' => $mailImg,
                ]
                )
        );
        $message->setContentType("text/html");
        $_result = $mailer->send($message);

        $_headers = $message->getHeaders();
        $_headers->addIdHeader('Message-ID', uniqid() . "@domain.com");
        $_headers->addTextHeader('MIME-Version', '1.0');
        $_headers->addTextHeader('X-Mailer', 'PHP v' . phpversion());
        $_headers->addParameterizedHeader('Content-type', 'text/html', ['charset' => 'utf-8']);
    
        
        if ($_result)
            return true;

        return false;
    }

    /**
     * @Route("/ajouter-nouveau-boutique", name="bout_nouv")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     * @param UserPasswordHasherInterface $encoder
     */
    public function Boutique(Request $request,EntityManagerInterface $manager, UserPasswordHasherInterface $encoder, \Swift_Mailer $mailer)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $bout = new Bout();
        $form = $this->createForm(BoutType::class, $bout);

        $form->handleRequest($request);

        /* user */

        $user = new User();

        $name = $request->get('name_user');
        $email = $request->get('email_user');
        $mdp = $request->get('mdp');
        $confMdp = $request->get('conf_mdp');
        $createur = $this->getUser();

        if($form->isSubmitted() && $form->isValid())
        {

            if (!empty($name) && !empty($email) && !empty($mdp) && !empty($confMdp)) {
                $user->setNom($name);
                $user->setEmail($email);
                $user->setRoles(['ROLE_BOUT']);

                if ($mdp === $confMdp){
                    $user->setPassword($encoder->hashPassword($user,$mdp));
                }

                $user->setCreateur($createur->getNom());
                $user->setIsActif(true);

                $manager->persist($user);
                $manager->flush();

                $bout->setUser($user);

                $this->envoieMail($name,$mdp,$email,$mailer);

            }

            // Activation boutique
            $dateCrétion = new \DateTime();
            $bout->setDateCreation($dateCrétion);
            $bout->setIsActive(true);

            // Incrementation de code client
            $_year = (new \DateTime())->format('Y');
            $_last_boutique = $this->boutRepository->findOneByYear((int)$_year);

            if ($_last_boutique) {
                $_last_number = (int)substr($_last_boutique->getCode(), 4);
                $_new_number  = $_last_number + 1;
            } else {
                $_new_number = 1;
            }

            $new_code = $_year . str_pad($_new_number, 3, '0', STR_PAD_LEFT);
            $bout->setCode($new_code);
            $bout->setIsCgv(false);

            $this->uploadFileService->uploadFile($form, $bout, "preuveCabis", "upload_images_cabis_directory");
            $this->uploadFileService->uploadFile($form, $bout, "pieceIdentity", "upload_images_piece_identity");
            
            $manager->persist($bout);
            $manager->flush();

            $this->addFlash(
                'success',
                "La boutique {$bout->getNom()} a été bien enregistrée"
            );

            return $this->redirectToRoute('boutique');
        }

        return $this-> render('BackOffice/boutique/bout.html.twig',[
            'form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/modification-de-boutique", name="bout_edit")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function BoutEdit(Request $request,UserPasswordHasherInterface $encoder, EntityManagerInterface $manager, Bout $bout, \Swift_Mailer $mailer)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(BoutType::class, $bout);
        $form->handleRequest($request);

        if($bout->getUser()){
            $user = $bout->getUser();
            $is_create = 0;
        } else {
            $user = new User();
            $is_create = 1;
        }

        $name = $request->get('name_user');
        $email = $request->get('email_user');
        $prenom = $request->get('firstName_user');
        $tel = $request->get('tel_user');

        $newMdp = $request->get('new_mdp');
        $confMdp = $request->get('conf_new_mdp');

        $createur = $this->getUser();

        if($form->isSubmitted() && $form->isValid())
        {

            if ($user->getId()) {
                $is_connected = $request->get('if_connecter');
                ($is_connected) ? $user->setIsActif(true) : $user->setIsActif(false) ;
      
            }
           
            if (!empty($name) && !empty($prenom) && !empty($tel) && !empty($email)) {

                $user->setNom($name);
                $user->setPrenom($prenom);
                $user->setTel($tel);
                $user->setEmail($email);
                $user->setRoles(['ROLE_BOUT']);
            }

            if (!empty($newMdp)) {

                if ($newMdp === $confMdp){
                    $user->setPassword($encoder->hashPassword($user,$newMdp));
                } else {
                    $this->addFlash(
                        'warning',
                        "Le mot de passe que vous avez tapé n'est pas identique"
                    );
                    return $this->redirectToRoute('bout_edit', [
                        'id' => $bout->getId(),
                    ]);
                }

                $user->setCreateur($createur->getNom());
                $bout->setUser($user);

                $manager->persist($user);
                $manager->flush();

                ($is_create == 1) ? $this->envoieMail($user->getNom(),$newMdp,$user->getEmail(),$mailer) : $this->envoieMailMdp($user->getNom(),$newMdp,$user->getEmail(),$mailer);


                $this->addFlash(
                    'success',
                    "La boutique a été modifiée avec succès"
                );
                return $this->redirectToRoute('boutique');

            }

            $this->uploadFileService->updateFileUploaded($form, $bout, "preuveCabis", "upload_images_cabis_directory");
            $this->uploadFileService->updateFileUploaded($form, $bout, "pieceIdentity", "upload_images_piece_identity");

            $manager->persist($bout);
            $manager->flush();

            $this->addFlash(
                'success',
                "La boutique a été modifiée avec succès"
            );
            return $this->redirectToRoute('boutique');
        }

        return $this-> render('BackOffice/boutique/edit.html.twig',[
            'form'=> $form->createView(),
            'boutique'=> $bout
        ]);
    }

     /**
     * @Route("/{id}/Supprimer-un-boutique", name="bout_suppr")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function delete(EntityManagerInterface $manager,Bout $bout)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

            $manager->remove($bout);
            $manager->flush();
            $this->addFlash(
                'success',
                "La boutique {$bout->getNom()} a été supprimée avec succès"
            );
        
        return $this->redirectToRoute('boutique');
    }

    /**
    * @Route("/liste-boutique-ajax", name="list_ajax_boutique")
    * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
    */
    public function listeAjaxBoutique(Request $request){
        $_page           = $request->query->get('start');
        $_nb_max_page    = $request->query->get('length');
        $_search         = $request->query->get('search')['value'];
        $_order_by       = $request->query->get('order_by');

        $_liste = $this->_boutique_manager->listeBoutiques($_page, $_nb_max_page, $_search, $_order_by);

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
     * @Route("/ajout-credit", name="ajout_credit", methods="GET|POST")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutCredit(Request $request,BoutRepository $_boutique_repository ,PerceptRepository $_percepteur_repository)
    {

        $_is_perc    = $request->request->get('is_perc');
        $_note       = $request->request->get('note');
        $_boutique   = $_boutique_repository->findOneBy(['id' => $request->request->get('id')]);
        $_percepteur = $_percepteur_repository->findOneBy(['id' => $request->request->get('perc')]);
        $_admin      = $this->getUser();
        $_type       = $request->request->get('type');
        
        $_last_recharge = $this->creditRepository->findOneByType($_type);
        $_value_type    = ($_type == '1') ? 'ESP' : 'VIR';

        if ($_last_recharge) {
            $_last_number = (int)substr($_last_recharge->getRef(), 4);
            $_new_number  = $_last_number + 1;
        } else {
            $_new_number = 1;
        }
        $new_code = $_value_type . str_pad($_new_number, 3, '0', STR_PAD_LEFT);

        $_credit = new Credit();
        $_credit->setDate(new \Datetime());
        $_credit->setMontant((float)$request->request->get('montant'));
        $_credit->setBout($_boutique);
        $_credit->setAdmin($_admin);
        $_credit->setType($_type);
        $_credit->setRef($new_code);
        $_credit->setIsvalid(true);
        ($_note) ? $_credit->setNote($_note) : '';
        ($_is_perc) ? $_credit->setPercept($_percepteur) : '';

        $this->_entity_manager->persist($_credit);
        $this->_entity_manager->flush();

        $_message = "La boutique {$_boutique->getNom()} a été rechargée avec succès";

        return new JsonResponse([
            'status'  => true,
            'message'    => $_message
        ]);
    }
    
    /**
     * @Route("/ajout-debit", name="ajout_debit", methods="GET|POST")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutDebit(Request $request,BoutRepository $_boutique_repository)
    {
        $_note       = $request->request->get('note');
        $_boutique   = $_boutique_repository->findOneBy(['id' => $request->request->get('id')]);
        $_admin      = $this->getUser();

        $_debit = new Debit();
        $_debit->setDate(new \Datetime());
        $_debit->setMontant($request->request->get('montant'));
        $_debit->setBout($_boutique);
        $_debit->setAdmin($_admin);
        ($_note) ? $_debit->setNote($_note) : '';

        $this->_entity_manager->persist($_debit);
        $this->_entity_manager->flush();

        $_message = "La boutique {$_boutique->getNom()} a été débitée avec succès";

        return new JsonResponse([
            'status'  => true,
            'message'    => $_message
        ]);
    }

    /**
     * @Route("/{id}/assignation", name="bout_assigne")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     * @param UserPasswordHasherInterface $encoder
     */
    public function assignation(Request $request,EntityManagerInterface $manager, Bout $bout)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(AssigneType::class, $bout);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($bout);
            $manager->flush();

            $this->addFlash(
                'success',
                "La boutique {$bout->getNom()} a été bien assigné"
            );

            return $this->redirectToRoute('boutique');
        }

        return $this-> render('BackOffice/boutique/assigner.html.twig',[
            'form'=> $form->createView(),
            'boutique'=> $bout
        ]);
    }

    /**
     * @Route("/{id}/caissier", name="bout_caissier")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function caissier(Bout $bout, Request $request, UserPasswordHasherInterface $encoder, EntityManagerInterface $manager)
    {
        $user = $bout->getUser();

        if(!$user){
            $this->addFlash(
                'warning',
                "aucun caissier"
            );
            return $this->redirectToRoute('boutique');
        }
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        $newMdp = $request->get('new_mdp');
        $confMdp = $request->get('conf_new_mdp');

        $status = $request->get('actif_caissier');
        

        if($status){
            $status = true;
        } else {
            $status = null;
        }
        if($form->isSubmitted()){

            if (!empty($newMdp)) {

                if ($newMdp === $confMdp){
                    $user->setPassword($encoder->hashPassword($user,$newMdp));

                    $this->uploadFileService->uploadFile($form, $user, "picture", "upload_images_users_directory");

                    $user->setIsActif($status);

                    $this->addFlash(
                        'success',
                        "Le caissier a été modifiée avec succès"
                    );
                    
                    $manager->persist($user);
                    $manager->flush();

                    $_message = "Le caissier a été modifiée avec succès";
                    
                    return new JsonResponse([
                        'status'  => true,
                        'message'    => $_message,
                        'table' => $this->renderView('BackOffice/caissier/liste.html.twig', [
                            'id' => $bout->getId(),
                            'boutique'=> $bout,
                        ]),
                    ]);

                    return $this->redirectToRoute('bout_caissier', [
                        'id' => $bout->getId(),
                        'boutique'=> $bout,
                    ]);

                } else {

                    $this->addFlash(
                        'warning',
                        "Le mot de passe que vous avez tapé n'est pas identique"
                    );

                    return $this->redirectToRoute('bout_caissier', [
                        'id' => $bout->getId(),
                        'boutique'=> $bout,
                    ]);
                }
            }

            $this->uploadFileService->uploadFile($form, $user, "picture", "upload_images_users_directory");
            
            $user->setIsActif($status);
            
            $this->addFlash(
                'success',
                "Le caissier a été modifiée avec succès"
            );

            $manager->persist($user);
            $manager->flush();

            $_message = "Le caissier a été modifiée avec succès";

            return new JsonResponse([
                'status'  => true,
                'message'    => $_message,
                'table' => $this->renderView('BackOffice/caissier/liste.html.twig', [
                    'id' => $bout->getId(),
                    'boutique'=> $bout,
                ]),
            ]);

            return $this->redirectToRoute('bout_caissier', [
                'id' => $bout->getId(),
                'boutique'=> $bout,
            ]);

        }
        return $this-> render('BackOffice/caissier/index.html.twig',[
            'boutique'=> $bout,
            'form'=> $form->createView(),
        ]);
    }

    /**
     * @Route("/ajout-geste", name="ajout_geste", methods="GET|POST")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutGeste(Request $request,BoutRepository $_boutique_repository)
    {
        $_note       = $request->request->get('note');
        $_boutique   = $_boutique_repository->findOneBy(['id' => $request->request->get('id')]);
        $_admin      = $this->getUser();

        $_geste = new Gestecommercial();
        $_geste->setDate(new \Datetime());
        $_geste->setMontant($request->request->get('montant'));
        $_geste->setBout($_boutique);
        $_geste->setAdmin($_admin);
        ($_note) ? $_geste->setMotif($_note) : '';

        $this->_entity_manager->persist($_geste);
        $this->_entity_manager->flush();

        $_message = "La boutique {$_boutique->getNom()} a effectué  un geste commerciale";

        return new JsonResponse([
            'status'  => true,
            'message'    => $_message
        ]);
    }

    /**
     * @Route("/ajout-seuil", name="ajout_seuil", methods="GET|POST")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutSeuil(Request $request,BoutRepository $_boutique_repository)
    {
        $_boutique   = $_boutique_repository->findOneBy(['id' => $request->request->get('id')]);
        $_admin      = $this->getUser();

        $_seuil = new Seuil();
        $_seuil->setDate(new \Datetime());
        $_seuil->setMontant($request->request->get('montant'));
        $_seuil->setBout($_boutique);
        $_seuil->setAdmin($_admin);

        $this->_entity_manager->persist($_seuil);
        $this->_entity_manager->flush();

        $_message = "La boutique {$_boutique->getNom()} a été augmenté d'une seuil de {$request->request->get('montant')} ";

        return new JsonResponse([
            'status'  => true,
            'message'    => $_message
        ]);
    }

    /**
     * @Route("/is-active", name="is_active", methods="GET|POST")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function isActivationBoutique(Request $request)
    {
        $_boutique = $this->boutRepository->findOneBy(['id' => $request->request->get('id')]);
        $_admin    = $this->getUser();

        ($request->request->get('status')=='1') ? $_boutique->setIsActive(1) : $_boutique->setIsActive(0);

        $this->_entity_manager->persist($_boutique);
        $this->_entity_manager->flush();

        $_is_active = ($request->request->get('status')=='1') ? "activé" : "désactivé";

        $_message = "La boutique {$_boutique->getNom()} a été {$_is_active}";

        return new JsonResponse([
            'status'  => true,
            'message'    => $_message
        ]);
    }

     /**
     * @Route("/ajout-seuil-billeterie", name="ajout_seuil_billeterie", methods="GET|POST")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function ajoutSeuilBilleterie(Request $request)
    {
        $_boutique   = $this->boutRepository->findOneBy(['id' => $request->request->get('id')]);
        $_admin      = $this->getUser();

        $_seuil = new SeuilBilleterie();
        $_seuil->setDate(new \Datetime());
        $_seuil->setMontant($request->request->get('montant'));
        $_seuil->setBout($_boutique);
        $_seuil->setUsers($_admin);
        $_seuil->setValeur(-(float)$request->request->get('montant'));

        $this->_entity_manager->persist($_seuil);
        $this->_entity_manager->flush();

        $_message = "La boutique {$_boutique->getNom()} a été ajouté d'une seuil de billeterie de {$request->request->get('montant')} ";

        return new JsonResponse([
            'status'  => true,
            'message'    => $_message
        ]);
    }

     /**
     * @Route("/import-fichier-liste-boutique", name="import_fichier_boutique")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function impoertFichierBoutique(Request $request)
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

                $this->saveToDatabase($col1, $col2, $col3, $col4);
            }

            $this->addFlash('success', 'Fichier importé avec succès !');
            
            return $this->redirectToRoute('boutique');
                // Sauveg
        }

        $this->addFlash('danger', 'Fichier non importé !');

        return $this->redirectToRoute('boutique');
    }

    private function saveToDatabase(string $nom = null, string $email = null, string $tel = null, string $siren = null): void
    {
        $dateCrétion = new \DateTime();
        $rec = $this->boutRepository->findOneBy(['nom' => $nom]);
        if (!$rec) {
            $boutique = new Bout();
            $boutique->setNom($nom);
            $boutique->setNumMobile($tel);
            $boutique->setEmail($email);
            $boutique->setSiren($siren);
            $boutique->setDateCreation($dateCrétion);
            $boutique->setFacturation('2');

            // Activation boutique
            $boutique->setIsActive(true);

            $new_code = $this->generateCodeBoutique();
            $boutique->setCode($new_code);
            $boutique->setIsCgv(false);

            $this->_entity_manager->persist($boutique);
            $this->_entity_manager->flush();
        }
    }

    public function generateCodeBoutique() {
        $anneeActuelle = (new \DateTime())->format('Y');
        $_last_boutique = $this->boutRepository->findOneBy([], ['id' => 'DESC']);
        
        if ($_last_boutique) {
            $dernierChiffres = (int)substr($_last_boutique->getCode(), -3);
            $anneeCode = substr($_last_boutique->getCode(), 0, 4);

            if ($anneeActuelle != $anneeCode) {
                $nouveauxChiffres = 1;
            } else {
                $nouveauxChiffres = (int) $dernierChiffres + 1;
            }
        } else {
            $nouveauxChiffres = 1;
        }

        $new_code = $anneeActuelle . str_pad($nouveauxChiffres, 3, '0', STR_PAD_LEFT);

        return $new_code;
    }
}
