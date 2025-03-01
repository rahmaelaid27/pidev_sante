<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use App\Entity\IdUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GoogleAuthenticator extends AbstractAuthenticator
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;
    private Security $security;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        Security $security,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->clientRegistry   = $clientRegistry;
        $this->entityManager    = $entityManager;
        $this->router           = $router;
        $this->security         = $security;
        $this->passwordHasher   = $passwordHasher;
    }

    public function supports(Request $request): bool
    {
        return 'connect_google_check' === $request->attributes->get('_route');
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        // Récupération de l'access token via getAccessToken() (sans utiliser fetchAccessToken())
        $accessToken = $client->getAccessToken();

        /** @var GoogleUser $googleUser */
        $googleUser = $client->fetchUserFromToken($accessToken);
        $email = $googleUser->getEmail();

        return new SelfValidatingPassport(new UserBadge($email, function () use ($email, $googleUser) {
            $user = $this->entityManager->getRepository(IdUser::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $user = new IdUser();
                $user->setEmail($email);
                $user->setNameUser($googleUser->getName());
                $user->setRole('patient'); // Par défaut, le nouveau compte est "patient"

                // Générer un mot de passe aléatoire et le hacher pour éviter qu'il soit null
                $randomPassword = bin2hex(random_bytes(10));
                $hashedPassword = $this->passwordHasher->hashPassword($user, $randomPassword);
                $user->setPassword($hashedPassword);

                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }

            return $user;
        }));
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?RedirectResponse
    {
        return new RedirectResponse($this->router->generate('front_login'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('front_login'));
    }
}
