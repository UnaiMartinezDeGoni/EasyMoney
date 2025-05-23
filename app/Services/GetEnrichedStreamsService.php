<?php
declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;
use RuntimeException;
use Illuminate\Http\JsonResponse;
use App\Infrastructure\TokenManager;
use App\Interfaces\TwitchApiRepositoryInterface;

class GetEnrichedStreamsService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $twitchApiRepository,
        private readonly TokenManager $tokenManager
    ) {
    }

    /**
     * Devuelve los streams más populares junto con información del usuario.
     *
     * @param int|null $limit Número de streams a devolver (>0)
     *
     * @return JsonResponse
     */
    public function getEnrichedStreams(?int $limit): JsonResponse
    {
        // 1) Validación de parámetro
        if (!is_int($limit) || $limit <= 0) {
            return new JsonResponse(['error' => "Invalid 'limit' parameter."], 400);
        }

        // 2) Token válido
        $token = $this->tokenManager->getToken();
        if (empty($token)) {
            return new JsonResponse(['error' => 'Unauthorized. Could not obtain a valid Twitch token.'], 401);
        }

        // 3) Obtener lista de streams
        try {
            $streamsResponse = $this->twitchApiRepository->getStreamsInfo($token);
            // Suponemos que el repositorio devuelve un array con JSON puro en la
            // primera posición, como en el ejemplo GetTopOfTopsService
            $streams = json_decode($streamsResponse[0], true)['data'] ?? [];
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Internal server error.'], 500);
        }

        if (empty($streams)) {
            return new JsonResponse(['error' => 'Internal server error.'], 500);
        }

        // 4) Seleccionar por viewers
        usort($streams, static fn($a, $b) => $b['viewer_count'] <=> $a['viewer_count']);
        $topStreams = array_slice($streams, 0, $limit);

        // 5) Enriquecer
        $formatted = [];
        foreach ($topStreams as $stream) {
            $userInfoResp = $this->twitchApiRepository->getStreamerInfo($stream['user_id'], $token);
            $userData     = json_decode($userInfoResp[0], true)['data'][0] ?? [];

            $formatted[] = [
                'stream_id'         => $stream['id'],
                'user_id'           => $stream['user_id'],
                'user_name'         => $stream['user_name'],
                'viewer_count'      => $stream['viewer_count'],
                'title'             => $stream['title'],
                'user_display_name' => $userData['display_name']   ?? $stream['user_name'],
                'profile_image_url' => $userData['profile_image_url'] ?? null,
            ];
        }

        return new JsonResponse($formatted, 200);
    }
}
