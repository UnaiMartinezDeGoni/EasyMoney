<?php

namespace App\Services;

use App\Interfaces\TwitchApiRepositoryInterface;
use Illuminate\Http\JsonResponse;

class GetStreamAnalyticsService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $repo
    ) {}

    /**
     * Obtiene y devuelve los streams sin parámetros adicionales.
     */
    public function getStreams(): JsonResponse
    {
        try {
            $streams = $this->repo->getStreams();  // <-- Llama siempre sin args
        } catch (\Throwable $e) {
            $streams = [];                        // <-- Captura cualquier excepción
        }

        return new JsonResponse($streams, 200);
    }
}
