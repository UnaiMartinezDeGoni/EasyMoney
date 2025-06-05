<?php

namespace App\Repositories;

use App\Interfaces\DBRepositoriesInterface;
use mysqli;
use RuntimeException;

class DBRepositories implements DBRepositoriesInterface
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

    public function isValidSession(string $token): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM sessions WHERE token = ? AND expires_at > NOW()");
        if (! $stmt) {
            throw new RuntimeException('Error al preparar SELECT sesión: ' . $this->db->error);
        }

        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->num_rows > 0;
    }

    public function getRecentTopVideos(string $since_datetime): array
    {
        $resultado = [];
        $stmt = $this->db->prepare(
            "SELECT
                game_id, user_name, total_videos, total_views,
                most_viewed_title, most_viewed_views, most_viewed_duration,
                most_viewed_created_at, updated_at
             FROM top_videos
             WHERE updated_at > ?
             ORDER BY most_viewed_views DESC
             LIMIT 120"
        );
        if (! $stmt) {
            throw new RuntimeException('Error preparando SELECT top_videos: ' . $this->db->error);
        }

        $stmt->bind_param('s', $since_datetime);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $resultado[] = $row;
        }
        $stmt->close();

        return $resultado;
    }

    public function existsTopGame(string $game_id): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM top_games WHERE game_id = ?");
        if (! $stmt) {
            throw new RuntimeException('Error preparando SELECT top_games: ' . $this->db->error);
        }
        $stmt->bind_param('s', $game_id);
        $stmt->execute();
        $exists = (bool) $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $exists;
    }

    public function insertTopGame(string $game_id, string $game_name): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO top_games (game_id, game_name, updated_at) VALUES (?, ?, NOW())"
        );
        if (! $stmt) {
            throw new RuntimeException('Error preparando INSERT top_games: ' . $this->db->error);
        }
        $stmt->bind_param('ss', $game_id, $game_name);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function upsertTopVideo(array $videoData): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO top_videos (
                game_id, user_name, total_videos, total_views,
                most_viewed_title, most_viewed_views, most_viewed_duration,
                most_viewed_created_at, updated_at
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE
                total_videos = VALUES(total_videos),
                total_views  = VALUES(total_views),
                most_viewed_title      = VALUES(most_viewed_title),
                most_viewed_views      = VALUES(most_viewed_views),
                most_viewed_duration   = VALUES(most_viewed_duration),
                most_viewed_created_at = VALUES(most_viewed_created_at),
                updated_at = NOW()"
        );
        if (! $stmt) {
            throw new RuntimeException('Error preparando INSERT/UPDATE top_videos: ' . $this->db->error);
        }

        $stmt->bind_param(
            'ssiisiss',
            $videoData['game_id'],
            $videoData['user_name'],
            $videoData['total_videos'],
            $videoData['total_views'],
            $videoData['most_viewed_title'],
            $videoData['most_viewed_views'],
            $videoData['most_viewed_duration'],
            $videoData['most_viewed_created_at']
        );
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
