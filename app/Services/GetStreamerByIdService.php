<?php

namespace App\Services;

use App\Interfaces\TwitchApiRepositoryInterface;
use Illuminate\Http\JsonResponse;

class GetStreamerByIdService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $repo
    ) {}

    public function getStreamerById(string $id): JsonResponse
    {
        try {
            $streamer = $this->repo->getStreamerById($id);
        } catch (\Throwable $e) {
            return new JsonResponse(
                ['error' => 'Internal server error.'],
                500
            );
        }

        if (empty($streamer)) {
            return new JsonResponse(
                ['error' => 'Streamer not found.'],
                404
            );
        }

        return new JsonResponse(
            $streamer,
            200
        );
    }
}
