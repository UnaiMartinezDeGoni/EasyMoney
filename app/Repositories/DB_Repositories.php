<?php

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
        $stmt = $this->db->prepare('SELECT id, api_key FROM users WHERE email = ?');
        if (! $stmt) {
            throw new RuntimeException('Error al preparar SELECT: ' . $this->db->error);
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();

        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $user ?: null;
    }

    public function createSession(int $userId, string $token, string $expiresAt): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO sessions (user_id, token, expires_at) VALUES (?, ?, ?)'
        );
        if (! $stmt) {
            throw new RuntimeException('Error al preparar INSERT sesión: ' . $this->db->error);
        }

        $stmt->bind_param('iss', $userId, $token, $expiresAt);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function refreshSession(int $userId, string $token): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE sessions SET expires_at = NOW() WHERE user_id = ? AND token = ?'
        );
        if (! $stmt) {
            throw new RuntimeException('Error al preparar UPDATE sesión: ' . $this->db->error);
        }

        $stmt->bind_param('is', $userId, $token);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function getActiveSession(int $userId): ?string
    {
        $stmt = $this->db->prepare(
            'SELECT token FROM sessions WHERE user_id = ? AND expires_at > NOW()'
        );
        if (! $stmt) {
            throw new RuntimeException('Error al preparar SELECT sesión: ' . $this->db->error);
        }

        $stmt->bind_param('i', $userId);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row['token'] ?? null;
    }
}
