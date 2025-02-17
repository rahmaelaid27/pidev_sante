<?php

namespace App\Controller\Front;

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
            // Vérification de l'unicité de l'email
            $existingUser = $entityManager->getRepository(IdUser::class)->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->render('front/security/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            // Vérification du rôle
            $validRoles = ['patient', 'professionnel'];
            if (!in_array($user->getRole(), $validRoles)) {
                $this->addFlash('error', 'Rôle invalide. Veuillez choisir "patient" ou "professionnel".');
                return $this->render('front/security/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            // Hachage du mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            if (!$plainPassword) {
                $this->addFlash('error', 'Le mot de passe est obligatoire.');
                return $this->render('front/security/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            // Sauvegarde en base de données
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
    public function login(): Response
    {
        return $this->render('front/security/login.html.twig');
    }

    #[Route('/forgot-password', name: 'front_forgot_password')]
    public function forgotPassword(
        Request $request,
        IdUserRepository $userRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {
                // Générer un token de réinitialisation avec une date d'expiration
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $user->setResetTokenExpiresAt(new \DateTime('+1 hour')); // Expiration dans 1 heure
                $entityManager->persist($user);
                $entityManager->flush();

                // Envoi de l'email de réinitialisation
                $emailMessage = (new Email())
                    ->from('no-reply@mediplus.com')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->html("<p>Pour réinitialiser votre mot de passe, cliquez sur le lien ci-dessous :</p>
                            <a href='http://127.0.0.1:8000/front/reset-password/$token'>Réinitialiser mon mot de passe</a>");

                $mailer->send($emailMessage);

                $this->addFlash('success', 'Un email de réinitialisation a été envoyé.');
                return $this->redirectToRoute('front_login');
            } else {
                $this->addFlash('error', 'Aucun compte trouvé avec cet email.');
            }
        }

        return $this->render('front/security/forgot_password.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'front_reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        IdUserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user || $user->getResetTokenExpiresAt() < new \DateTime()) {
            $this->addFlash('error', 'Token invalide ou expiré.');
            return $this->redirectToRoute('front_forgot_password');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');
            if (!$newPassword) {
                $this->addFlash('error', 'Veuillez entrer un mot de passe.');
                return $this->render('front/security/reset_password.html.twig', ['token' => $token]);
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès. Connectez-vous maintenant !');
            return $this->redirectToRoute('front_login');
        }

        return $this->render('front/security/reset_password.html.twig', ['token' => $token]);
    }
}
