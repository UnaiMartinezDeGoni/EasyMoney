<?php
// src/Helpers/FuncionesComunes.php

namespace App\Helpers;

use RuntimeException;
use mysqli;

class FuncionesComunes
{
    /**
     * Conecta a la base de datos y devuelve un objeto mysqli
     *
     * @throws RuntimeException si falla la conexión
     */
    public static function conectarMysqli(): mysqli
    {
        $host     = getenv('DB_HOST');
        $port     = getenv('DB_PORT') ?: 3306;
        $db       = getenv('DB_DATABASE');
        $user     = getenv('DB_USERNAME');
        $pass     = getenv('DB_PASSWORD');

        $mysqli = new mysqli($host, $user, $pass, $db, $port);
        if ($mysqli->connect_error) {
            throw new RuntimeException('MySQL connection error: ' . $mysqli->connect_error);
        }
        $mysqli->set_charset('utf8mb4');
        return $mysqli;
    }

    /**
     * Cualquier otra función común que uses…
     */
    public static function enrichStreams(array $streams): array
    {
        // ejemplo de enriquecimiento
        return array_map(fn($s) => [
            'id'           => $s['id'],
            'viewer_count' => $s['viewer_count'],
            'url'          => 'https://twitch.tv/' . ($s['user_login'] ?? '')
        ], $streams);
    }
}
