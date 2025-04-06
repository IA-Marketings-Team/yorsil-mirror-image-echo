<?php

namespace App\Security;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    private $entityManager;
    private $router;
    private $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    public function onLogoutSuccess(Request $request): Response
    {
        // Récupère l'utilisateur actuellement authentifié
        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;

        if ($user && $user instanceof \App\Entity\User) {
            // Efface le token de session de l'utilisateur dans la base de données
            $user->setSessionToken(null);
            $this->entityManager->flush();
        }

        // Invalide la session Symfony
        $request->getSession()->invalidate();

        // Redirige vers la page de connexion
        return new RedirectResponse($this->router->generate('app_login'));
    }
}
