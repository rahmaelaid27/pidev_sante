<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Form\RendezVousType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class RendezVousController extends AbstractController
{
    #[Route('/rendezvous/new', name: 'app_rendezvous_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $rendezVous = new RendezVous();
        $user = $security->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté pour prendre un rendez-vous.");
        }

        $rendezVous->setUser($user);

        $form = $this->createForm(RendezVousType::class, $rendezVous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rendezVous);
            $entityManager->flush();

            return $this->redirectToRoute('app_rendezvous_new');
        }

        return $this->render('rendezvous/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/rendezvous', name: 'app_rendezvous_list')]
    public function list(EntityManagerInterface $entityManager,TokenStorageInterface $tokenStorage): Response
    {
        $user = $tokenStorage->getToken()?->getUser();

        // Vérifier si l'utilisateur est bien connecté
        if (!$user || !is_object($user)) {
            throw $this->createAccessDeniedException("Vous devez être connecté pour voir vos rendez-vous.");
        }

        // Récupérer uniquement les rendez-vous de cet utilisateur
        $rendezVousList = $entityManager->getRepository(RendezVous::class)->findBy(['user' => $user]);

        return $this->render('rendezvous/list.html.twig', [
            'rendezVousList' => $rendezVousList,
        ]);
    }


    #[Route('/professional/rendezvous', name: 'app_professional_rendezvous')]
    public function professionalRendezVous(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        // Get the currently logged-in user
        $professional = $tokenStorage->getToken()?->getUser();

        if (!$professional || !is_object($professional)) {
            throw $this->createAccessDeniedException("You must be logged in to view your appointments.");
        }

        // Get all appointments where the logged-in user is the professional
        $rendezVousList = $entityManager->getRepository(RendezVous::class)->findBy(['professional' => $professional]);

        return $this->render('rendezvous/professional_list.html.twig', [
            'rendezVousList' => $rendezVousList,
        ]);
    }

    #[Route('/professional/rendezvous/accept/{id}', name: 'app_rendezvous_accept')]
    public function acceptRendezVous(RendezVous $rendezVous, EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $rendezVous->setStatusRdv('accepted');
        $entityManager->persist($rendezVous);
        $entityManager->flush();

        return $this->redirectToRoute('app_add_consultation', ['id' => $rendezVous->getId()]);
    }

    #[Route('/professional/rendezvous/refuse/{id}', name: 'app_rendezvous_refuse')]
    public function refuseRendezVous(RendezVous $rendezVous, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($rendezVous);
        $entityManager->flush();

        return $this->redirectToRoute('app_professional_rendezvous');
    }









}
