<?php

namespace App\Services;

use App\Exceptions\ServerErrorException;
use App\Repositories\DB_Repositories;
use Illuminate\Http\JsonResponse;
use Throwable;

class UserRegisterByEmailService
{
    private DB_Repositories $dbRepo;

    public function __construct(DB_Repositories $dbRepo)
    {
        $this->dbRepo = $dbRepo;
    }


    public function register(string $email): JsonResponse
    {
        $apiKey = $this->generateApiKey();

        try {
            $user = $this->dbRepo->findUserByEmail($email);

            if ($user !== null) {
                $this->dbRepo->updateApiKey($email, $apiKey);
            } else {
                $this->dbRepo->insertUser($email, $apiKey);
            }

            return response()->json([
                'api_key' => $apiKey,
            ], 200, [], JSON_PRETTY_PRINT);

        } catch (Throwable $e) {
            // Captura errores de BD u otros
            $serverError = new ServerErrorException();
            return response()->json([
                'error' => $serverError->getMessage(),
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }


    private function generateApiKey(): string
    {
        return bin2hex(random_bytes(8));
    }
}
