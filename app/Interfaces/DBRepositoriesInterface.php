<?php

namespace App\Interfaces;

interface DBRepositoriesInterface
{
    public function findUserByEmail(string $email): ?array;

    public function createSession(int $userId, string $token, string $expiresAt): bool;

    public function refreshSession(int $userId, string $token): bool;

    public function getActiveSession(int $userId): ?string;

    public function insertUser(string $email, string $apiKey): bool;

    public function updateApiKey(string $email, string $apiKey): bool;

    public function getRecentTopVideos(string $since_datetime): array;

    public function existsTopGame(string $game_id): bool;

    public function insertTopGame(string $game_id, string $game_name): bool;

    public function upsertTopVideo(array $videoData): bool;
}
