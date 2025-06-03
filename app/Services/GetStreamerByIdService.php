<?php

namespace App\Services;

use App\Interfaces\TwitchApiRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Throwable;

class GetStreamerByIdService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $repo
    ) {}

    public function getStreamerById(string $id): JsonResponse
    {
        try {
            $data = $this->repo->getStreamerById($id);
        } catch (Throwable) {
            return new JsonResponse(['error' => 'Internal server error.'], 500);
        }

        if (empty($data)) {
            return new JsonResponse(['error' => 'Streamer not found.'], 404);
        }

        return new JsonResponse($data, 200);
    }
}
