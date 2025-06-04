<?php
namespace App\Services;

use App\Exceptions\InvalidApiKeyException;
use App\Exceptions\ServerErrorException;
use App\Repositories\DBRepositories;
use Throwable;

class TokenService
{
    private DBRepositories $repo;

    public function __construct(DBRepositories $repo)
    {
        $this->repo = $repo;
    }

    public function issueToken(string $email, string $apiKey): array
    {
        try {
            $user = $this->repo->findUserByEmail($email);

            if (!$user || !isset($user['api_key']) || $user['api_key'] !== $apiKey) {
                throw new InvalidApiKeyException();
            }

            $userId = (int)$user['id'];
            $activeSession = $this->repo->getActiveSession($userId);

            if ($activeSession) {
                $this->repo->refreshSession($userId, $activeSession);
                return ['token' => $activeSession];
            }

            $token   = bin2hex(random_bytes(16));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $this->repo->createSession($userId, $token, $expires);

            return ['token' => $token];
        }
        catch (InvalidApiKeyException $e) {
            throw $e;
        }
        catch (Throwable $e) {
            throw new ServerErrorException();
        }
    }
}
