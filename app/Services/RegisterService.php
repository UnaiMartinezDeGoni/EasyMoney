<?php
namespace App\Services;

use App\Exceptions\ServerErrorException;
use App\Interfaces\DBRepositoriesInterface;
use App\Repositories\DBRepositories;
use Throwable;

class RegisterService
{
    private DBRepositoriesInterface $dbRepo;

    public function __construct(DBRepositoriesInterface $dbRepo)
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

