<?php

namespace App\Services;

use App\Interfaces\TwitchApiRepositoryInterface;
use App\Exceptions\ServerErrorException;

class GetEnrichedStreamsService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $twitchApiRepository
    ) {}

    /**
     * Obtiene y enriquece streams; devuelve un array crudo de streams o lanza excepciones.
     *
     * @param int $limit
     * @return array<int, array<string, mixed>>
     * @throws ServerErrorException Si ocurre cualquier fallo interno (API, repositorio, etc.).
     */
    public function getEnrichedStreams(int $limit): array
    {
        try {
            $streams = $this->twitchApiRepository->getStreams();
        } catch (\Throwable) {
            throw new ServerErrorException();
        }

        if (empty($streams)) {
            throw new ServerErrorException();
        }

        usort(
            $streams,
            static fn($a, $b) => $b['viewer_count'] <=> $a['viewer_count']
        );
        $topStreams = array_slice($streams, 0, $limit);

        $formatted = [];
        foreach ($topStreams as $stream) {
            try {
                $userData = $this->twitchApiRepository->getStreamerById($stream['user_id']);
            } catch (\Throwable) {
                throw new ServerErrorException();
            }

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

        return $formatted;
    }
}
