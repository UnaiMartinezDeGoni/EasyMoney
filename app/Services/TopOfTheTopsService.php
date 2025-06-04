<?php
namespace App\Services;

use App\Interfaces\TwitchApiRepositoryInterface;
use App\Helpers\FuncionesComunes;
use Throwable;

class TopOfTheTopsService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $repo
    ) {}

    public function getTopVideos(int $since): array
    {
        try {
            $videos = $this->repo->getTopVideos($since);
        } catch (Throwable $e) {
            // Se podría lanzar una excepción de tipo ServerErrorException si se dispone de una.
            throw new \RuntimeException('Error fetching top videos.');
        }
        
        if (class_exists(FuncionesComunes::class)) {
            $videos = FuncionesComunes::enrichTopVideos($videos);
        }
        
        if (empty($videos)) {
            throw new \RuntimeException("Not Found. No data available.");
        }

        return array_values($videos);
    }
}
