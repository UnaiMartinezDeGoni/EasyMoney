<?php

namespace App\Services;

use App\Interfaces\TwitchApiRepositoryInterface;
use Illuminate\Http\JsonResponse;

class GetStreamAnalyticsService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $repo
    ) {}

    public function getStreams(): JsonResponse
    {
        try {
            $streams = $this->repo->getStreams();
        } catch (\Throwable $e) {
            $streams = [];
        }

        return new JsonResponse($streams, 200);
    }
}
