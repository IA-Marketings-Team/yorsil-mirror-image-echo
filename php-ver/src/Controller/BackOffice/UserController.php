<?php

namespace App\Controller\BackOffice;

use App\Entity\User;
use App\Entity\PasswordUpdate;
use App\Form\BackOffice\UserType;
use App\Form\BackOffice\AdminType;
use App\Repository\UserRepository;
use App\Service\UploadFileService;
use App\Form\BackOffice\ProfilType;
use Symfony\Component\Form\FormError;
use App\Form\BackOffice\AdminEditType;
use App\Form\BackOffice\PswUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    public function __construct(UploadFileService $uploadFileService)
    {
        $this->uploadFileService = $uploadFileService;
    }

    /**
     * @Route("/liste-des-administrateurs", name="liste_admin")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réservé au Super-administrateur!")
     */
    public function adminList(UserRepository $userRepository): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('BackOffice/admin/list_admin.html.twig', [
            'users' => $userRepository->findAdmin(),
        ]);
    }

    /**
     * @Route("/liste-des-super-administrateurs", name="liste_super_admin")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réservé au Super-administrateur!")
     */
    public function superAdminList(UserRepository $userRepository): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('BackOffice/admin/list_super_admin.html.twig', [
            'users' => $userRepository->findSuperAdmin(),
        ]);
    }

    /**
     * @Route("/ajouter-un-administrateur", name="ajout_admin")
     * @IsGranted("ROLE_SUPER_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réservé au Super-administrateur!")
     */
    public function AjoutAdmin(Request $request,EntityManagerInterface $manager,UserPasswordEncoderInterface $encoder, UserRepository $userRepository)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $user = new User();
        $form = $this->createForm(AdminType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $roles = ['ROLE_ADMIN'];
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setRoles($roles);
            $user->setPassword($hash);
            $this->uploadFileService->uploadFile($form, $user, "picture", "upload_images_users_directory");
            
            $user_rec = $userRepository->findAdmin();
            $agent_id  = ($user_rec) ? count($user_rec)+1 : 1;
            $_num_shop = "AD".str_pad($agent_id, 4, "0", STR_PAD_LEFT);
            $user->setNumCom($_num_shop);
            $user->setIsActif(true);
            
            $manager->persist($user);
            $manager->flush();
            
            $this->addFlash(
                'success',
                "L’administrateur {$user->getNom()} a été bien enregistré"
            );
            return $this->redirectToRoute('liste_admin');
        }

        return $this->render('BackOffice/admin/ajout_admin.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/modification", name="edit_admin")
     * @IsGranted("ROLE_SUPER_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réservé au Super-administrateur!")
     */
    public function editAdmin(Request $request, EntityManagerInterface $manager,User $user, UserPasswordEncoderInterface $encoder)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(AdminEditType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $password = $form->get('Password')->getData();
            $confirmPassword = $form->get('Confir_pwd')->getData();

            if (!empty($password) && !empty($confirmPassword)) {
                if($password !== $confirmPassword) {
                    $this->addFlash(
                        'warning',
                        "Vous n'avez pas tapé le même mot de passe !"
                    );
                    
                    return $this->render('BackOffice/admin/edit.html.twig', [
                        'edit_form' => $form->createView(),
                        'user' => $user
                    ]);
                } else {
                    $hash = $encoder->encodePassword($user, $password);
                    $user->setPassword($hash);
                }
            }

            $this->uploadFileService->uploadFile($form, $user, "picture", "upload_images_users_directory");
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                "Les données ont été modifiés avec succès!"
            );

            return $this->redirectToRoute('liste_admin');

        }

        return $this->render('BackOffice/admin/edit.html.twig',[
            'edit_form' => $form->createView(),
            'user' => $user
        ]);
    }

    /** 
     * @Route("/{id}/delete-admin", name="admin_delete")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function deleteAdmin(EntityManagerInterface $manager,User $user)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        
        $manager->remove($user);
        $manager->flush();
        
         $this->addFlash(
             'success',"L'administrateur {$user->getNom()} a bien été supprimer !"
         );
        
        return $this->redirectToRoute('liste_admin');
    }


    /**
     * @Route("/modification-de-profile", name="profil_edit")
     */
    public function EditeUserProfile(Request $request, EntityManagerInterface $manager)
    {
            if (!$this->getUser())
            {
                return $this->redirectToRoute('app_login');
            }

        $user = $this->getUser();

        $form = $this->createForm(ProfilType::class, $user); 
        $form->handleRequest($request);


        if($form->isSubmitted())
        {
            $this->uploadFileService->uploadFile($form, $user, "picture", "upload_images_users_directory");

            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                "Les données ont été modifiés avec succès!"
            );
            
            return $this->redirectToRoute('profil_edit');
        }

        return $this->render('BackOffice/user/profil.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier-mot-de-passe", name="password")
     */
    public function ModifierMDP(Request $request,UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
            if (!$this->getUser())
            {
                return $this->redirectToRoute('app_login');
            }

        $user = $this->getUser();

        $passwordUpdate = new PasswordUpdate();
        $form = $this->createForm(PswUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())){

                    $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe  actuel"));

            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setPassword($hash);
                
                $manager->persist($user);
                $manager->Flush();
 
                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié!"
                );

                return $this->redirectToRoute('profil_edit');

            }    


        }

        return $this->render('BackOffice/user/pwd.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    public function envoieMailForgot($id,$email,\Swift_Mailer $mailer){

        $message = (new \Swift_Message('Mot de passe oublié sur  yorsil'))
                    ->setFrom('serviceclient@yorsil.com')
                    ->setTo($email);

        $logo = $message->embed(\Swift_Image::fromPath('modernize/images/logos/favicon.svg'));
        $mailImg = $message->embed(\Swift_Image::fromPath('modernize/images/logos/mail.png'));
        $message->setBody(
            $this->renderView(
                // templates/emails/registration.html.twig
                'mail/reset_password.html.twig',
                [
                    'id' => $id,
                    'logo' => $logo,
                    'mailImg' => $mailImg
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
     * @Route("/forgot_password", name="forgot_password",methods={"post","get"})
     * @param Request $request
     * @param UserRepository $repository
     * @return Response
     */
    public function forgotPassword(Request $request, UserRepository $repository,\Swift_Mailer $mailer): Response
    {

        if ($request->getMethod() === 'POST'){
            $email = $request->get('email');
            $user = $repository->findOneBy(['email' => $email]);
            if($user){
                $user = $user->getId();
                $this->get('session')->set('user_session',$user);
                $this->envoieMailForgot($user,$email,$mailer);
                $this->addFlash('success',"Un email est envoyé à votre compte email pour changer mot de passe oublié");
                return $this->redirectToRoute('app_login');
            }else{
                $this->addFlash('error',"L'email n'existe pas");
                return $this->redirectToRoute('forgot_password');
            }
        }
        return $this->render('BackOffice/security/forgot.html.twig');
    }

    /**
     * @Route("/{id}/reset-password%tmpkey=81fc8390-cd95-4bae-a9eb-3cfe64fbec36", name="change_password")
     * @param Request $request
     * @param UserPasswordHasherInterface $encoder
     * @param EntityManagerInterface $manager
     * @param UserRepository $repository
     * @return Response
     */
    public function changePassword(Request $request,UserPasswordHasherInterface $encoder, EntityManagerInterface $manager, UserRepository $repository, User $user): Response
    {

        $id_user = $this->get('session')->get('user_session');
        if($user->getId()==$id_user){
            if(!is_null($id_user)){
                if ($request->getMethod() === 'POST'){
                    $new_password = $request->get('new_password');
                    $confirm_new_password = $request->get('confirm_new_password');
                    if ($new_password === $confirm_new_password){
                        $user = $repository->find($id_user);
                        if($user){
                            $user->setPassword($encoder->hashPassword($user,$new_password));
                            $manager->persist($user);
                            $manager->flush();
                            $this->addFlash('success','Mot de passe changé avec succès');
                            $this->get('session')->remove('user_session');
                            return $this->redirectToRoute('app_login');
                        }else{
                            $this->addFlash('error',"L'utilisateur n'existe pas");
                        }
                    }
                }
                return $this->render('BackOffice/security/change_password.html.twig');
            }else{
                $this->addFlash('error',"Jeton de securité non valide");
                return $this->redirectToRoute('forgot_password');
            }
        }else{
            $this->addFlash('error',"Jeton de securité non valide");
            return $this->redirectToRoute('forgot_password');
        }
    }

        /**
     * @Route("/liste-des-utilisateurs", name="liste_user")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réservé au Administrateur!")
     */
    public function userList(UserRepository $userRepository): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('BackOffice/user/list_user.html.twig', [
            'users' => $userRepository->findUser(),
        ]);
    }


    /**
     * @Route("/ajouter-un-utilisateur", name="ajout_user")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réservé au Administrateur!")
     */
    public function utilisateur(Request $request,EntityManagerInterface $manager,UserPasswordEncoderInterface $encoder, UserRepository $userRepository)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $roles = ['ROLE_USER'];
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setRoles($roles);
            $user->setPassword($hash);
            $this->uploadFileService->uploadFile($form, $user, "picture", "upload_images_users_directory");
            
            $user_user = $userRepository->findUser();
            $agent_id  = ($user_user) ? count($user_user)+1 : 1;
            $_num_shop = "US".str_pad($agent_id, 4, "0", STR_PAD_LEFT);
            $user->setNumCom($_num_shop);
            
            $manager->persist($user);
            $manager->flush();
            
            $this->addFlash(
                'success',
                "L’utilisateur {$user->getNom()} a été bien enregistré"
            );
            return $this->redirectToRoute('liste_user');
        }

        return $this->render('BackOffice/user/ajout_user.html.twig',[
            'form' => $form->createView()
        ]);
    }


}




