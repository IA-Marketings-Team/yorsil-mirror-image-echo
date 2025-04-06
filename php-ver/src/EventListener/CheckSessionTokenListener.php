<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CheckSessionTokenListener
{
    private $security;
    private $logoutUrlGenerator;
    private $entityManager;
    private $session;

    public function __construct(Security $security, LogoutUrlGenerator $logoutUrlGenerator, EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->security = $security;
        $this->logoutUrlGenerator = $logoutUrlGenerator;
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $user = $this->security->getUser();

        if ($user && method_exists($user, 'getSessionToken')) {
            if (!$user->getSessionToken()) {
                $session = $event->getRequest()->getSession();
                $session->invalidate();

                $logoutUrl = $this->logoutUrlGenerator->getLogoutPath();
                $event->setResponse(new RedirectResponse($logoutUrl));
            } else {
                $currentSessionId = $this->session->getId();
    
                if ($user->getSessionToken() !== $currentSessionId) {
                    $user->setSessionToken($currentSessionId);
                    $this->entityManager->flush();
                }
            }
        }
    }
}
