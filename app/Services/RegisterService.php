<?php
namespace App\Services;

use App\Exceptions\ServerErrorException;
use App\Repositories\DBRepositories;
use Throwable;

class RegisterService
{
    private DBRepositories $dbRepo;

    public function __construct(DBRepositories $dbRepo)
    {
        $this->dbRepo = $dbRepo;
    }

    public function register(string $email): array
    {
        $apiKey = $this->generateApiKey();

        try {
            $user = $this->dbRepo->findUserByEmail($email);

            if ($user !== null) {
                $this->dbRepo->updateApiKey($email, $apiKey);
            } else {
                $this->dbRepo->insertUser($email, $apiKey);
            }

            return [
                'api_key' => $apiKey,
            ];
        } catch (Throwable $e) {
            throw new ServerErrorException();
        }
    }

    private function generateApiKey(): string
    {
        return bin2hex(random_bytes(8));
    }
}

