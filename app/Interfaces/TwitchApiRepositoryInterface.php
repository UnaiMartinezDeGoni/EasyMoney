<?php

namespace App\Interfaces;

interface TwitchApiRepositoryInterface
{
    public function getStreams(): array;

    public function getStreamerById(string $id): array;

    public function getTopGames(string $access_token, int $limit = 3): array;

    public function getVideosByGame(string $access_token, string $game_id, int $limit = 40): array;

    public function aggregateVideosByUser(array $videosResponse, string $game_id, string $game_name): array;
}
