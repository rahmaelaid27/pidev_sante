<?php
// src/Controller/ConsultationController.php

namespace App\Controller;

use App\Form\ConsultationType;
use App\Repository\ConsultationRepository;
use App\Repository\PrescriptionRepository;
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
        $rendezVous = $entityManager->getRepository(RendezVous::class)->find($id);

        if (!$rendezVous) {
            throw $this->createNotFoundException('No rendezvous found for id ' . $id);
        }


        $consultation = new Consultation();
        $consultation->setRendezVous($rendezVous);
        $consultation->setUser($rendezVous->getUser());
        $consultation->setProfessionnel($rendezVous->getProfessional());

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
    public function listConsultations(ConsultationRepository $consultationRepository, Request $request): Response
    {
        $user = $this->getUser();
        $sortBy = $request->query->get('sort_by', 'date_consultation'); 
        $order = $request->query->get('order', 'ASC');
        $selectedDate = $request->query->get('selected_date');

        $queryBuilder = $consultationRepository->createQueryBuilder('c')
            ->leftJoin('c.rendezVous', 'r')
            ->leftJoin('c.user', 'u')
            ->andWhere('c.professionnel = :user')
            ->setParameter('user', $user);

            if ($sortBy === 'rendezVous.status_rdv') {
                $queryBuilder->orderBy('r.status_rdv', $order); 
            } elseif ($sortBy === 'user.nom') {
                $queryBuilder->orderBy('u.nom', $order);
            } else {
                $queryBuilder->orderBy('c.' . $sortBy, $order);
            }

        if ($selectedDate) {
            $formattedDate = \DateTime::createFromFormat('m/d/Y', $selectedDate);

            if ($formattedDate) {
                $startOfDay = clone $formattedDate;
                $startOfDay->setTime(0, 0, 0);

                $endOfDay = clone $formattedDate;
                $endOfDay->setTime(23, 59, 59);


                $queryBuilder->andWhere('c.date_consultation BETWEEN :startOfDay AND :endOfDay')
                    ->setParameter('startOfDay', $startOfDay)
                    ->setParameter('endOfDay', $endOfDay);
            }
        }

        $consultations = $queryBuilder->getQuery()->getResult();


        return $this->render('consultation/list.html.twig', [
            'consultations' => $consultations,
        ]);
    }


    #[Route('/consultation/delete/{id}', name: 'app_delete_consultation')]
    public function delete(int $id, EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $consultation = $entityManager->getRepository(Consultation::class)->find($id);

        if (!$consultation) {
            $this->addFlash('error', 'Consultation not found!');
            return $this->redirectToRoute('app_dashboard');
        }

        $entityManager->remove($consultation);
        $entityManager->flush();

        $this->addFlash('success', 'Consultation deleted successfully.');
        return $this->redirectToRoute('app_dashboard');

    }

    #[Route('/consultation/edit/{id}', name: 'app_edit_consultation')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $consultation = $entityManager->getRepository(Consultation::class)->find($id);

        if (!$consultation) {
            $this->addFlash('error', 'Consultation not found!');
            return $this->redirectToRoute('app_dashboard');
        }

        $form = $this->createForm(ConsultationType::class, $consultation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Consultation updated successfully.');
            return $this->redirectToRoute('app_edit_consultation', ['id' => $consultation->getId()]);
        }

        return $this->render('consultation/edit.html.twig', [
            'form' => $form->createView(),
            'consultation' => $consultation,
        ]);
    }

    #[Route('/list/admins/consultations', name: 'admin_consultation_list')]
    public function listConsultationsAdmin(ConsultationRepository $consultationRepository): Response
    {
        $consultations = $consultationRepository->findAll();

        return $this->render('admin/consultation_list.html.twig', [
            'consultations' => $consultations
        ]);
    }

    #[Route('list/admin/consultation/{id}', name: 'admin_consultation_details')]
    public function showConsultationDetails(Consultation $consultation, PrescriptionRepository $prescriptionRepository): Response
    {
        $prescriptions = $prescriptionRepository->findBy(['consultation' => $consultation]);

        return $this->render('admin/consultation_details.html.twig', [
            'consultation' => $consultation,
            'prescriptions' => $prescriptions
        ]);
    }

    #[Route('/history', name: 'app_history')]
    public function index(ConsultationRepository $consultationRepository): Response
    {

        $professional = $this->getUser();
        $consultations = $consultationRepository->findBy(['professionnel' => $professional]);

        return $this->render('consultation/history.html.twig', [
            'consultations' => $consultations,
        ]);
    }





}
