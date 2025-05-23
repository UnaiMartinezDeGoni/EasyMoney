<?php
// app/Services/TopOfTheTopsService.php

namespace App\Services;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Interfaces\TwitchApiRepositoryInterface; // Asumiendo que el repositorio de la API ya está definido
use App\Helpers\FuncionesComunes;

class TopOfTheTopsService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $repo
    ) {}

    /**
     * Obtiene y transforma los top videos
     *
     * @param int $since
     * @return JsonResponse
     */
    public function getTopVideos(int $since): JsonResponse
    {
        // Se obtiene la información a través del repositorio.
        $videos = $this->repo->getTopVideos($since);

        // Por ejemplo, se podría enriquecer la información usando Helpers:
        if (class_exists(FuncionesComunes::class)) {
            $videos = FuncionesComunes::enrichTopVideos($videos);
        }

        // Si no hay datos, devolvemos un 404 con mensaje de error.
        if (empty($videos)) {
            return new JsonResponse(["error" => "Not Found. No data available."], 404);
        }

        return new JsonResponse(array_values($videos), 200);
    }
}
