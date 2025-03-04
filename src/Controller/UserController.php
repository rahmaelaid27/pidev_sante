<?php

namespace App\Controller;

use App\Entity\IdUser;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{   
    #[Route('/ajouter-user', name: 'ajouter_user')]
    public function ajouterUtilisateur(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new IdUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification si l'email existe déjà
            $existingUser = $entityManager->getRepository(IdUser::class)->findOneBy(['email' => $user->getEmail()]);

            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
            } else {
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Utilisateur ajouté avec succès.');
                
                return $this->redirectToRoute('ajouter_user');
            }
        }

        // Récupération de la liste des utilisateurs
        $users = $entityManager->getRepository(IdUser::class)->findAll();

        return $this->render('user/add.html.twig', [
            'form' => $form->createView(),
            'users' => $users,
        ]);
    }

    #[Route('/utilisateur/modifier/{id}', name: 'modifier_user')]
    public function modifierUtilisateur(Request $request, EntityManagerInterface $entityManager, IdUser $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification si l'email est déjà utilisé par un autre utilisateur
            $existingUser = $entityManager->getRepository(IdUser::class)->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser && $existingUser->getId() !== $user->getId()) {
                $this->addFlash('error', 'Cet email est déjà utilisé par un autre utilisateur.');
            } else {
                $entityManager->flush();
                $this->addFlash('success', 'Utilisateur modifié avec succès.');

                return $this->redirectToRoute('ajouter_user');
            }
        }

        return $this->render('user/add.html.twig', [
            'form' => $form->createView(),
            'users' => $entityManager->getRepository(IdUser::class)->findAll(),
        ]);
    }

    #[Route('/utilisateur/supprimer/{id}', name: 'supprimer_user')]
    public function supprimerUtilisateur(EntityManagerInterface $entityManager, IdUser $user): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('ajouter_user');
    }
}
