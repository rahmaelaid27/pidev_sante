<?php
// src/Controller/ConsultationController.php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\Prescription;
use App\Form\PrescriptionType;
use App\Repository\ConsultationRepository;
use App\Repository\PrescriptionRepository;
use App\Repository\ReponseRepository;
use App\Service\MailerService;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PresecriptionController extends AbstractController
{

    #[Route('/consultation/{id}/add-prescription', name: 'consultation_add_prescription')]
    public function addPrescription(
        Consultation $consultation,
        Request $request,
        EntityManagerInterface $em,
        MailerService $mailerService,
        PdfService $pdf,
    )
    {
        $prescription = new Prescription();
        $form = $this->createForm(PrescriptionType::class, $prescription);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prescription->setConsultation($consultation);// Associate prescription with consultation
            $prescription->setCreatedAt(new \DateTime());
            $em->persist($prescription);
            $em->flush();

//          mailing
            $professional = $this->getUser()->getNom();
            $patientEmail = $consultation->getUser()->getEmail();
            $patientNom = $consultation->getUser()->getNom();
            $subject = "New prescription from: ".$professional;
            $description = $prescription->getDescription();

            $html = $this->renderView('presecription/pdf.html.twig', [
                'patientName' => $patientNom, // Nom du patient
                'professionalName' => $professional, // Nom du médecin
                'prescriptionDescription' => $description, // Description de la prescription
                'date' => $prescription->getCreatedAt()->format('d-m-Y') // Date de création de la prescription
            ]);
            $pdfBinary = $pdf->generateBinaryPDF($html);
            $content = "
                <p>Bonjour <strong>{$patientNom}</strong>,</p>
                <p>Votre medecin, <strong>{$professional}</strong>, vous a prescrit une nouvelle ordonnance.</p>
                <p><strong>Détails de la prescription :</strong></p>
                <blockquote style='border-left: 4px solid #007bff; padding-left: 10px; color: #333;'>
                    {$description}
                </blockquote>
                <p>Pour toute question, n'hesitez pas a contacter votre medecin.</p>
                <p>Cordialement,</p>
                <p><em>L'equipe Mediplus</em></p>
            ";
            $mailerService->sendEmailWithAttachment(
                $patientEmail,
                $subject,
                $content,
                $pdfBinary,
                'prescription.pdf'
            );

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

        $prescription = $prescriptionRepository->find($id);

        if ($prescription) {
            $entityManager->remove($prescription);

            $entityManager->flush();


            $this->addFlash('success', 'Prescription deleted successfully!');
        } else {

            $this->addFlash('error', 'Prescription not found!');
        }

        // Redirect back to the consultations list or any other page
        return $this->redirectToRoute('consultations_list');
    }

}
