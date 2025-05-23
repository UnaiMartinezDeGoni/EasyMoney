<?php

namespace App\Services;

use App\Repositories\DB_Repositories;
use App\Exceptions\InvalidApiKeyException;

class TokenService
{
    private DB_Repositories $repo;

    public function __construct(DB_Repositories $repo)
    {
        $this->repo = $repo;
    }

    public function issueToken(string $email, string $apiKey): string
    {
        $user = $this->repo->findUserByEmail($email);
        if (! $user || $user['api_key'] !== $apiKey) {
            throw new InvalidApiKeyException('API access token is invalid.');
        }

        $active = $this->repo->getActiveSession((int)$user['id']);
        if ($active) {
            $this->repo->refreshSession((int)$user['id'], $active);
            return $active;
        }

        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->repo->createSession((int)$user['id'], $token, $expires);
        return $token;
    }
}
