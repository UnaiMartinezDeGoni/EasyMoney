<?php

namespace App\Repositories;

class DB_Repositories
{
    protected $mysqli;
    public function __construct(\mysqli $mysqli = null)
    {
        $this->mysqli = $mysqli ?: conectarMysqli();
    }

    public function findUserByEmail(string $email): ?array
    {
        $stmt = $this->mysqli->prepare("SELECT api_key FROM users WHERE email = ?");
        if (!$stmt) {
            throw new \RuntimeException("Failed to prepare SELECT statement: " . $this->mysqli->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    public function updateApiKey(string $email, string $apiKey): bool
    {
        $stmt = $this->mysqli->prepare("UPDATE users SET api_key = ? WHERE email = ?");
        if (!$stmt) {
            throw new \RuntimeException("Failed to prepare UPDATE statement: " . $this->mysqli->error);
        }
        $stmt->bind_param("ss", $apiKey, $email);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function insertUser(string $email, string $apiKey): bool
    {
        $stmt = $this->mysqli->prepare("INSERT INTO users (email, api_key) VALUES (?, ?)");
        if (!$stmt) {
            throw new \RuntimeException("Failed to prepare INSERT statement: " . $this->mysqli->error);
        }
        $stmt->bind_param("ss", $email, $apiKey);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }


}

