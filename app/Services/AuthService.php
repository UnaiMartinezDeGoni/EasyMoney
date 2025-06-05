<?php

namespace App\Services;

use App\Interfaces\DBRepositoriesInterface;

class AuthService
{
    private DBRepositoriesInterface $repo;

    public function __construct(DBRepositoriesInterface $repo)
    {
        $this->repo = $repo;
    }

    public function validateToken(string $token): bool
    {
        return $this->repo->isValidSession($token);
    }
}
