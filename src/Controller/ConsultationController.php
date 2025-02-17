<?php
// src/Controller/ConsultationController.php

namespace App\Controller;

use App\Form\ConsultationType;
use App\Repository\ConsultationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Consultation;
use App\Entity\RendezVous;

class ConsultationController extends AbstractController
{
    #[Route('/consultation/{id}/create', name: 'app_add_consultation')]
    public function addConsultation(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Retrieve the RendezVous using the provided ID
        $rendezVous = $entityManager->getRepository(RendezVous::class)->find($id);

        if (!$rendezVous) {
            throw $this->createNotFoundException('No rendezvous found for id ' . $id);
        }

        // Create a new consultation and set the user and professionnel from the RendezVous
        $consultation = new Consultation();
        $consultation->setRendezVous($rendezVous);
        $consultation->setUser($rendezVous->getUser()); // Assuming the "user" field is associated with RendezVous
        $consultation->setProfessionnel($rendezVous->getProfessional()); // Assuming the "professionnel" field is associated with RendezVous

        // Handle form submission
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($consultation);
            $entityManager->flush();

            $this->addFlash('success', 'Consultation created successfully!');
            return $this->redirectToRoute('app_professional_rendezvous'); // Redirect after saving
        }

        return $this->render('consultation/add.html.twig', [
            'form' => $form->createView(),
            'rendezVous' => $rendezVous,
        ]);
    }

    #[Route('/professional/consultations', name: 'app_professional_consultations')]
    public function listConsultations(ConsultationRepository $consultationRepository): Response
    {
        // Fetch consultations related to the logged-in professional
        $user = $this->getUser(); // Assuming the professional is logged in
        $consultations = $consultationRepository->findBy(['professionnel' => $user]); // Assuming 'professional' field in Consultation entity

        return $this->render('consultation/list.html.twig', [
            'consultations' => $consultations,
        ]);
    }


    #[Route('/consultation/delete/{id}', name: 'app_delete_consultation')]
    public function delete(int $id, EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        // Retrieve the consultation object by its ID
        $consultation = $entityManager->getRepository(Consultation::class)->find($id);

        if (!$consultation) {
            // If no consultation is found, flash a message and redirect to the consultations page
            $this->addFlash('error', 'Consultation not found!');
            return $this->redirectToRoute('app_dashboard');
        }

        // Remove the consultation
        $entityManager->remove($consultation);
        $entityManager->flush();

        // Flash success message and redirect
        $this->addFlash('success', 'Consultation deleted successfully.');
        return $this->redirectToRoute('app_dashboard');

    }

    #[Route('/consultation/edit/{id}', name: 'app_edit_consultation')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the consultation object by its ID
        $consultation = $entityManager->getRepository(Consultation::class)->find($id);

        if (!$consultation) {
            // If no consultation is found, flash a message and redirect
            $this->addFlash('error', 'Consultation not found!');
            return $this->redirectToRoute('app_dashboard');
        }

        // Create and handle the form
        $form = $this->createForm(ConsultationType::class, $consultation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If the form is submitted and valid, persist the changes
            $entityManager->flush();

            // Flash success message and redirect back to the same page with updated data
            $this->addFlash('success', 'Consultation updated successfully.');
            return $this->redirectToRoute('app_edit_consultation', ['id' => $consultation->getId()]);
        }

        return $this->render('consultation/edit.html.twig', [
            'form' => $form->createView(),
            'consultation' => $consultation,
        ]);
    }






}
