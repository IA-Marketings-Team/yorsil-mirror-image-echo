<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\BoutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class AuthentificationAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder,BoutRepository $boutRepository)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->boutRepository = $boutRepository;
    }

    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            throw new UsernameNotFoundException('Email could not be found.');
        }

        // Vérifier si le client boutique est désactivé
        if ($user->getRoles()[0]=='ROLE_BOUT') {
            $_boutique  = $this->boutRepository->findOneBy(['user' => $user]);
            if (!$_boutique->getIsActive()) {
                throw new CustomUserMessageAuthenticationException("Votre compte a été désactivé par l'administrateur, veuillez contacter notre support client!");
            }
        }

        // $currentSessionId = session_id();
        // if ($user->getSessionToken() && $user->getSessionToken() !== $currentSessionId) {
        //     throw new CustomUserMessageAuthenticationException('Cet utilisateur est déjà connecté sur un autre appareil.');
        // }
        
        // if (!$user->getSessionToken()) {
            // throw new CustomUserMessageAuthenticationException('Cet utilisateur est déjà connecté sur un autre appareil.');
            // return new RedirectResponse($this->urlGenerator->generate('app_logout'));
        // }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $user = $token->getUser();
        $user->setSessionToken(session_id());
        $this->entityManager->flush();

        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        // if (!$this->isGranted('ROLE_ADMIN')) 
        // {
        //     return $this->redirectToRoute('dashboard');
        // }

        // if (!$this->isGranted('ROLE_AGENT')) 
        // {
        //     return $this->redirectToRoute('home');
        // }

        return new RedirectResponse($this->urlGenerator->generate('home_back'));
        throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
