<?php
namespace App\Services;

use App\Interfaces\TwitchApiRepositoryInterface;

class GetStreamAnalyticsService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $repo
    ) {}
    
    public function listarStreams(): array
    {
        try {
            return $this->repo->getStreams();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
