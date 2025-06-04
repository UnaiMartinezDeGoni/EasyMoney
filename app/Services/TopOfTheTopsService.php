<?php
namespace App\Services;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Helpers\FuncionesComunes;

class TopOfTheTopsService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $repo
    ) {}

    /**
     * Obtiene y transforma los top videos.
     *
     * @param int $since
     * @return JsonResponse
     */
    public function getTopVideos(int $since): JsonResponse
    {
        $videos = $this->repo->getTopVideos($since);

        if (class_exists(FuncionesComunes::class)) {
            $videos = FuncionesComunes::enrichTopVideos($videos);
        }

        if (empty($videos)) {
            return new JsonResponse(["error" => "Not Found. No data available."], 404);
        }

        return new JsonResponse(array_values($videos), 200);
    }
}
