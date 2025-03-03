<?php

namespace App\Controller;

use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/professional/stats')]
class StatsController extends AbstractController
{
    #[Route('/', name: 'professional_stats')]
    public function index(AvisRepository $avisRepository): Response
    {
        $user = $this->getUser();

        // Récupérer les avis du professionnel connecté
        $avisList = $avisRepository->findBy(['professional' => $user]);

        // Calcul du nombre total d'avis et de la moyenne des notes
        $totalAvis = count($avisList);
        $averageRating = $totalAvis > 0 ? array_sum(array_map(fn($avis) => $avis->getNote(), $avisList)) / $totalAvis : 0;

        return $this->render('avis/stats.html.twig', [
            'totalAvis' => $totalAvis,
            'averageRating' => number_format($averageRating, 1),
        ]);
    }

    #[Route('/data', name: 'stats_data')]
    public function getStatsData(AvisRepository $avisRepository): JsonResponse
    {
        $user = $this->getUser();
        $avisList = $avisRepository->findBy(['professional' => $user]);

        $ratings = array_fill(1, 5, 0);

        foreach ($avisList as $avis) {
            $ratings[$avis->getNote()]++;
        }

        return new JsonResponse([
            'labels' => array_keys($ratings),
            'data' => array_values($ratings),
        ]);
    }
}
