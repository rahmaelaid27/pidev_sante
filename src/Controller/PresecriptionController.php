<?php
// src/Controller/ConsultationController.php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\Prescription;
use App\Form\PrescriptionType;
use App\Repository\ConsultationRepository;
use App\Repository\PrescriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PresecriptionController extends AbstractController
{

    #[Route('/consultation/{id}/add-prescription', name: 'consultation_add_prescription')]
    public function addPrescription(Consultation $consultation, Request $request, EntityManagerInterface $em)
    {
        $prescription = new Prescription();
        $form = $this->createForm(PrescriptionType::class, $prescription);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prescription->setConsultation($consultation); // Associate prescription with consultation
            $em->persist($prescription);
            $em->flush();
            $this->addFlash('success', 'Prescription added successfully!');
            return $this->redirectToRoute('consultation_add_prescription', ['id' => $consultation->getId()]);
        }

        return $this->render('presecription/add_prescription.html.twig', [
            'form' => $form->createView(),
            'consultation' => $consultation,
        ]);
    }

    #[Route('/presecriptions', name: 'presecriptions_list')]
    public function list(ConsultationRepository $consultationRepository): \Symfony\Component\HttpFoundation\Response
    {
        // Fetch all consultations with their prescriptions
        $consultations = $consultationRepository->findAll();

        return $this->render('presecription/list.html.twig', [
            'consultations' => $consultations,
        ]);

    }

    #[Route('/prescription/delete/{id}', name: 'prescription_delete')]
    public function delete(int $id, PrescriptionRepository $prescriptionRepository, EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        // Retrieve the prescription from the database
        $prescription = $prescriptionRepository->find($id);

        if ($prescription) {
            // Remove the prescription using the EntityManager
            $entityManager->remove($prescription);
            $entityManager->flush();  // Flush the changes to the database

            $this->addFlash('success', 'Prescription deleted successfully!');
        } else {
            $this->addFlash('error', 'Prescription not found!');
        }

        return $this->redirectToRoute('your_redirect_route'); // Adjust this route
    }

}
