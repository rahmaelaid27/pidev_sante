<?php

namespace App\Controller\Front;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use App\Entity\IdUser;
use App\Form\RegistrationFormType;
use App\Repository\IdUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mime\Address;

#[Route('/front')]
class SecurityController extends AbstractController
{
    #[Route('/register', name: 'front_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new IdUser();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($entityManager->getRepository(IdUser::class)->findOneBy(['email' => $user->getEmail()])) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->render('front/security/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            $plainPassword = $form->get('plainPassword')->getData();
            if (!$plainPassword) {
                $this->addFlash('error', 'Le mot de passe est obligatoire.');
                return $this->render('front/security/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription réussie ! Connectez-vous maintenant.');
            return $this->redirectToRoute('front_login');
        }

        return $this->render('front/security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'front_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('front/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/forgot-password', name: 'front_forgot_password')]
    public function forgotPassword(
        Request $request,
        IdUserRepository $userRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        if ($request->isMethod('POST')) {
            $email = trim($request->request->get('email'));

            if (empty($email)) {
                $this->addFlash('error', 'Veuillez entrer une adresse email.');
                return $this->redirectToRoute('front_forgot_password');
            }

            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'Aucun compte associé à cet email.');
                return $this->redirectToRoute('front_forgot_password');
            }

            // Générer un code de vérification à 6 chiffres
            $verificationCode = random_int(100000, 999999);

            // Stocker le code et la date d'expiration (10 minutes)
            $user->setResetToken($verificationCode);
            $user->setResetTokenExpiresAt(new \DateTime('+10 minutes'));

            $entityManager->persist($user);
            $entityManager->flush();

            // Création de l'e-mail (l'email est envoyé à l'adresse saisie par l'utilisateur)
            $emailMessage = (new Email())
                ->from(new Address('jmalyessine114@gmail.com', 'MediPlus Support'))
                ->to($user->getEmail())
                ->subject('Code de vérification - Réinitialisation du mot de passe')
                ->html("<p>Bonjour,</p>
                        <p>Voici votre code de vérification : <strong>{$verificationCode}</strong></p>
                        <p>Ce code est valable 10 minutes.</p>");

            try {
                $mailer->send($emailMessage);
                $this->addFlash('success', 'Un code de vérification a été envoyé à votre email.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            }

            return $this->redirectToRoute('front_verify_code', ['email' => $email]);
        }

        return $this->render('front/security/forgot_password.html.twig');
    }

    #[Route('/verify-code', name: 'front_verify_code')]
    public function verifyCode(
        Request $request,
        IdUserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $code = $request->request->get('code');
            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user || $user->getResetToken() !== $code || $user->getResetTokenExpiresAt() < new \DateTime()) {
                $this->addFlash('error', 'Code invalide ou expiré.');
                return $this->redirectToRoute('front_verify_code', ['email' => $email]);
            }

            return $this->redirectToRoute('front_reset_password', ['email' => $email]);
        }

        return $this->render('front/security/verify_code.html.twig');
    }

    #[Route('/reset-password', name: 'front_reset_password')]
    public function resetPassword(
        Request $request,
        IdUserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'Utilisateur introuvable.');
                return $this->redirectToRoute('front_forgot_password');
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe réinitialisé.');
            return $this->redirectToRoute('front_login');
        }

        return $this->render('front/security/reset_password.html.twig');
    }

    #[Route('/connect/google', name: 'connect_google_start')]
    public function connectGoogle(ClientRegistry $clientRegistry)
    {
        return $clientRegistry->getClient('google')->redirect(['email', 'profile']);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectGoogleCheck()
    {
        return $this->redirectToRoute('front_redirect');
    }

    #[Route('/redirect', name: 'front_redirect')]
    public function redirectAfterLogin(): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('front_login');

        return $this->redirectToRoute(
            in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_PROFESSIONNEL', $user->getRoles()) ? 'ajouter_user' : 'patient_dashboard'
        );
    }
}
