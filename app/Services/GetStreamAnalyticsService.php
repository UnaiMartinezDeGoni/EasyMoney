<?php

namespace App\Services;

use App\Interfaces\TwitchApiRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Helpers\FuncionesComunes;

class GetStreamAnalyticsService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $repo
    ) {}

    /**
     * Obtiene, transforma y devuelve los streams
     *
     * @param int $limit
     * @return JsonResponse
     */
    public function getStreams(int $limit): JsonResponse
    {
        $streams = $this->repo->getStreams($limit);

        // Usa tus funciones comunes para filtrar/enriquecer
        if (class_exists(FuncionesComunes::class)) {
            $streams = FuncionesComunes::enrichStreams($streams);
        }

        return new JsonResponse($streams, 200);
    }
}
