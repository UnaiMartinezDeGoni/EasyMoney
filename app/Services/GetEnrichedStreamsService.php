<?php
namespace App\Services;

use App\Interfaces\TwitchApiRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Throwable;

class GetEnrichedStreamsService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $twitchApiRepository
    ) {}

    public function getEnrichedStreams(int $limit): JsonResponse
    {
        try {
            $streams = $this->twitchApiRepository->getStreams();
        } catch (Throwable $e) {
            return new JsonResponse(['error' => 'Internal server error.'], 500);
        }

        if (empty($streams)) {
            return new JsonResponse(['error' => 'Internal server error.'], 500);
        }

        usort($streams, static fn($a, $b) => $b['viewer_count'] <=> $a['viewer_count']);
        $topStreams = array_slice($streams, 0, $limit);

        $formatted = [];
        foreach ($topStreams as $stream) {
            try {
                $userData = $this->twitchApiRepository->getStreamerById($stream['user_id']);
            } catch (Throwable $e) {
                return new JsonResponse(['error' => 'Internal server error.'], 500);
            }

            $formatted[] = [
                'stream_id'         => $stream['id'],
                'user_id'           => $stream['user_id'],
                'user_name'         => $stream['user_name'],
                'viewer_count'      => $stream['viewer_count'],
                'title'             => $stream['title'],
                'user_display_name' => $userData['display_name'] ?? $stream['user_name'],
                'profile_image_url' => $userData['profile_image_url'] ?? null,
            ];
        }

        return new JsonResponse($formatted, 200);
    }
}
