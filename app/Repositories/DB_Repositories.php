<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;
use RuntimeException;

class DB_Repositories
{
    private mysqli $db;


    public function __construct()
    {
        $host     = getenv('DB_HOST')     ?: '127.0.0.1';
        $port     = (int)(getenv('DB_PORT')     ?: 3306);
        $database = getenv('DB_DATABASE') ?: 'forge';
        $username = getenv('DB_USERNAME') ?: 'forge';
        $password = getenv('DB_PASSWORD') ?: '';

        $this->db = new mysqli($host, $username, $password, $database, $port);
        if ($this->db->connect_error) {
            throw new RuntimeException(
                sprintf(
                    'MySQL connection error (%d): %s',
                    $this->db->connect_errno,
                    $this->db->connect_error
                )
            );
        }

        $this->db->set_charset('utf8mb4');
    }


    public function findUserByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT api_key FROM users WHERE email = ?');
        if (! $stmt) {
            throw new RuntimeException('Error al preparar SELECT: ' . $this->db->error);
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();

        $stmt->close();
        return $user ?: null;
    }


    public function insertUser(string $email, string $apiKey): bool
    {
        $stmt = $this->db->prepare('INSERT INTO users (email, api_key) VALUES (?, ?)');
        if (! $stmt) {
            throw new RuntimeException('Error al preparar INSERT: ' . $this->db->error);
        }

        $stmt->bind_param('ss', $email, $apiKey);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function updateApiKey(string $email, string $apiKey): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET api_key = ? WHERE email = ?');
        if (! $stmt) {
            throw new RuntimeException('Error al preparar UPDATE: ' . $this->db->error);
        }

        $stmt->bind_param('ss', $apiKey, $email);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }
}
