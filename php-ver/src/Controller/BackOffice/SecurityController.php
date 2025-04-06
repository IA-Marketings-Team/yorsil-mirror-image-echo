<?php

namespace App\Controller\BackOffice;

use App\Repository\SlideRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\Utils\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\Request;

class SecurityController extends AbstractController
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, SlideRepository $slideRepository): Response
    {
        if ($this->getUser()) {

            //  return $this->redirectToRoute('home');

            if($this->isGranted('ROLE_ADMIN')){
                return $this->redirectToRoute('tableau_de_bord');
            } else if($this->isGranted('ROLE_PERC')){
                return $this->redirectToRoute('dashboard_percepteur');
            } else if ($this->isGranted('ROLE_BOUT')) {
                $_slug = Util::slug($this->getUser()->getNom());
                return $this->redirectToRoute('home_office');
            }
            
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('BackOffice/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'sliders' => $slideRepository->findAll()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
