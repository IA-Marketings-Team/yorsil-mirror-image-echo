<?php

namespace App\Controller\FrontOffice;

use App\Entity\PasswordUpdate;
use App\Form\BackOffice\ProfilType;
use App\Form\BackOffice\PswUpdateType;
use App\Repository\BoutRepository;
use App\Service\UploadFileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BoutController extends AbstractController
{
    private $boutRepo;
    private $entityManager;
    private $uploadFileService;

    public function __construct(
        BoutRepository $boutRepo,
        EntityManagerInterface $entityManager,
        UploadFileService $uploadFileService)
    {
        $this->boutRepo          = $boutRepo;
        $this->entityManager     = $entityManager;
        $this->uploadFileService = $uploadFileService;
    }

    /**
     * @Route("/modifier-mon-profile", name="edit_my_profil")
     */
    public function EditeMyProfile(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->uploadFileService->uploadFile($form, $user, "picture", "upload_images_users_directory");

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                "Les données ont été modifiés avec succès!"
            );

            return $this->redirectToRoute('home_office');
        }

        return $this->render('FrontOffice/boutique/editProfile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier-mon-mot-de-passe", name="edit_my_password")
     */
    public function ModifierMDP(Request $request, UserPasswordEncoderInterface $encoder)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        $passwordUpdate = new PasswordUpdate();
        $form = $this->createForm(PswUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())) {

                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe  actuel"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setPassword($hash);

                $this->entityManager->persist($user);
                $this->entityManager->Flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié!"
                );

                return $this->redirectToRoute('home_office');
            }
        }

        return $this->render('FrontOffice/boutique/editPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
