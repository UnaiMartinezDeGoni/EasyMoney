<?php

namespace App\Services;

use App\Exceptions\InvalidApiKeyException;
use App\Exceptions\ServerErrorException;
use App\Repositories\DB_Repositories;
use Illuminate\Http\JsonResponse;
use Throwable;

class TokenService
{
    private DB_Repositories $repo;

    public function __construct(DB_Repositories $repo)
    {
        $this->repo = $repo;
    }

    public function issueToken(string $email, string $apiKey): JsonResponse
    {
        try {
            $user = $this->repo->findUserByEmail($email);

            if (! $user || ! isset($user['api_key']) || $user['api_key'] !== $apiKey) {
                throw new InvalidApiKeyException();
            }

            $activeSession = $this->repo->getActiveSession((int) $user['id']);

            if ($activeSession) {
                $this->repo->refreshSession((int) $user['id'], $activeSession);

                return response()->json(
                    ['token' => $activeSession],
                    200,
                    [],
                    JSON_PRETTY_PRINT
                );
            }

            $token   = bin2hex(random_bytes(16));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $this->repo->createSession((int) $user['id'], $token, $expires);

            return response()->json(
                ['token' => $token],
                200,
                [],
                JSON_PRETTY_PRINT
            );
        }
        catch (InvalidApiKeyException $e) {
            return response()->json(
                ['error' => 'Unauthorized. ' . $e->getMessage()],
                401,
                [],
                JSON_PRETTY_PRINT
            );
        }
        catch (Throwable $e) {
            $serverError = new ServerErrorException();
            return response()->json(
                ['error' => $serverError->getMessage()],
                500,
                [],
                JSON_PRETTY_PRINT
            );
        }
    }
}
