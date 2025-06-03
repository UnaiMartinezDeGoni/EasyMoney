<?php

namespace App\Services;

use App\Interfaces\TwitchApiRepositoryInterface;

class GetStreamAnalyticsService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $repo
    ) {}

    /**
     * Devuelve un array con todos los streams obtenidos desde el repositorio.
     * Si hay un error devuelve un array vacío.
     */
    public function listarStreams(): array
    {
        try {
            return $this->repo->getStreams();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
