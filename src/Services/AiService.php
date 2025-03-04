<?php

namespace App\Services;

use Gemini\Client;

class AiService
{
    private Client $geminiClient;

    public function __construct(Client $geminiClient)
    {
        $this->geminiClient = $geminiClient;
    }

    public function traiterDemande(string $message): string
    {
        $result = $this->geminiClient->geminiFlash()->generateContent($message);
        return $result->text();
    }
}