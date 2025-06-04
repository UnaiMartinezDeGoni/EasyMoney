<?php

namespace App\Services;

use App\Interfaces\TwitchApiRepositoryInterface;

class TopOfTheTopsService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $repo
    ) {}

    public function getTopVideos(int $since): array
    {

        return $this->repo->getTopVideos($since);
    }
}
