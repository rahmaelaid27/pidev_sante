<?php

namespace App\Controller;

use App\Services\AiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class GeminiController extends AbstractController
{
    private AiService $aiService;

    public function __construct(AiService $AiService)
    {
        $this->aiService = $AiService;
    }

    #[Route('/ai', name: 'ai', methods: ['POST'])]
    public function generateDescription(Request $request, LoggerInterface $logger): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $logger->info('Request data: ' . print_r($data, true));  // Log request data for debugging

        $specs = $data['specs'] ?? '';
        $ask = $data['ask'] ?? '';

        if (empty($ask)) {
            return new JsonResponse(['error' => 'Mots-clés requis.'], 400);
        }

        if (empty($specs)) {
            return new JsonResponse(['error' => 'Specialites requis.'], 400);
        }

        $specsString = implode(', ', $specs);
        try {
            $generatedText = $this->aiService->traiterDemande("a propos de ce cas : $ask , choisisez l'un de ces specialites qui peut gerer le cas ?: $specsString ( juste la specialite )");
        } catch (\Exception $e) {
            $logger->error('Error in AiService: ' . $e->getMessage());  // Log errors from AiService
            return new JsonResponse(['error' => 'An error occurred while processing the request.'], 500);
        }

        return new JsonResponse(['message' => $generatedText]);
    }

}
