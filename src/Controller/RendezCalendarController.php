<?php

namespace App\Controller;

use App\Repository\RendezRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RendezCalendarController extends AbstractController
{
    #[Route('/rendez/calendar', name: 'rendez_calendar', methods: ['GET'])]
    public function index(RendezRepository $rendezRepository): Response
    {
        // 1) Make sure the patient is logged in
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Please log in first');
        }

        // 2) Fetch all rendezvous for this logged-in patient
        $rendezs = $rendezRepository->findBy(['user' => $user]);

        // 3) Render the template and pass the rendezvous
// RendezCalendarController.php
        return $this->render('admin/rendez/calendar.html.twig', [
            'rendezs' => $rendezs
        ]);

    }
}
