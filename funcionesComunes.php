<?php
header('Content-Type: application/json');

// Credenciales Twitch
define('TWITCH_CLIENT_ID',     '7gkz3aw164wmtbo5u1m1n4cihpy8de');
define('TWITCH_CLIENT_SECRET', 'o99k9rok7tlprgf3bmzkqkdtqw6tan');

/**
 * Devuelve una instancia de mysqli conectada según las variables de entorno.
 *
 * @return \mysqli
 * @throws \RuntimeException si la conexión falla.
 */
function conectarMysqli(): mysqli
{
    // Leer de Config Vars (Heroku) o de .env local
    $host     = getenv('DB_HOST') ?: '127.0.0.1';
    $port     = (int)(getenv('DB_PORT') ?: 3306);
    $database = getenv('DB_DATABASE') ?: 'forge';
    $username = getenv('DB_USERNAME') ?: 'forge';
    $password = getenv('DB_PASSWORD') ?: '';

    $mysqli = new mysqli($host, $username, $password, $database, $port);
    if ($mysqli->connect_error) {
        throw new RuntimeException("MySQL connection error: " . $mysqli->connect_error);
    }
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}

/**
 * Obtiene un token de acceso válido de Twitch (client credentials).
 *
 * @return string|null
 */
function obtenerTokenTwitch(): ?string {
    $url = "https://id.twitch.tv/oauth2/token";
    $post_fields = [
        'client_id'     => TWITCH_CLIENT_ID,
        'client_secret' => TWITCH_CLIENT_SECRET,
        'grant_type'    => 'client_credentials'
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($post_fields),
        CURLOPT_RETURNTRANSFER => true,
    ]);

    $respuesta = curl_exec($ch);
    curl_close($ch);

    $datos = json_decode($respuesta, true);
    return $datos['access_token'] ?? null;
}

/**
 * Verifica si un token de acceso sigue siendo válido.
 *
 * @param string $access_token
 * @return bool
 */
function verificarToken(string $access_token): bool {
    $url = "https://id.twitch.tv/oauth2/validate";
    $headers = ["Authorization: OAuth $access_token"];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $respuesta = curl_exec($ch);
    curl_close($ch);

    $datos = json_decode($respuesta, true);
    return isset($datos['client_id']);
}

/**
 * Obtiene información de un streamer por su ID de Twitch.
 *
 * @param int $streamer_id
 * @param string $access_token
 * @return array|null
 */
function getStreamerInfo(int $streamer_id, string $access_token): ?array {
    $url = "https://api.twitch.tv/helix/users?id={$streamer_id}";

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            "Client-ID: " . TWITCH_CLIENT_ID,
            "Authorization: Bearer $access_token"
        ],
    ]);

    $respuesta = curl_exec($ch);
    curl_close($ch);
    return json_decode($respuesta, true);
}

/**
 * Obtiene información de streams activos.
 *
 * @param string $access_token
 * @return array|null
 */
function getStreamsInfo(string $access_token): ?array {
    $url = "https://api.twitch.tv/helix/streams";

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            "Client-ID: " . TWITCH_CLIENT_ID,
            "Authorization: Bearer $access_token"
        ],
    ]);

    $respuesta = curl_exec($ch);
    curl_close($ch);
    return json_decode($respuesta, true);
}

/**
 * Genera una API key aleatoria.
 *
 * @return string
 */
function generateApiKey(): string {
    return bin2hex(random_bytes(8));
}

/**
 * Genera un token de API con expiración.
 *
 * @return array{token:string,expires_at:string}
 */
function generateApiToken(): array {
    $token = bin2hex(random_bytes(8));
    $expires_at = date('Y-m-d H:i:s', strtotime('+3 days'));
    return ['token' => $token, 'expires_at' => $expires_at];
}

/**
 * Obtiene y persiste top videos por juego y usuario.
 *
 * @param string $access_token
 * @param int $since    segundos de antigüedad mínima para refrescar
 * @return array
 */
function obtenerTopVideosTwitch(string $access_token, int $since = 600): array {
    try {
        $mysqli = conectarMysqli();
    } catch (RuntimeException $e) {
        return ['error' => $e->getMessage()];
    }

    $last_update_limit = date('Y-m-d H:i:s', time() - $since);
    $topVideos = [];

    // Leer de BD
    $stmt = $mysqli->prepare(
        "SELECT * FROM top_videos WHERE updated_at > ? ORDER BY most_viewed_views DESC LIMIT 120"
    );
    $stmt->bind_param('s', $last_update_limit);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $topVideos[] = $row;
    }
    $stmt->close();

    if (count($topVideos) >= 3) {
        return $topVideos;
    }

    // Obtener top 3 juegos
    $ch = curl_init('https://api.twitch.tv/helix/games/top?first=3');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            "Client-ID: " . TWITCH_CLIENT_ID,
            "Authorization: Bearer $access_token"
        ]
    ]);
    $games_response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    foreach ($games_response['data'] as $game) {
        $game_id   = $game['id'];
        $game_name = $game['name'];

        // Persistir top_games si no existe
        $stmt = $mysqli->prepare("SELECT 1 FROM top_games WHERE game_id = ?");
        $stmt->bind_param('s', $game_id);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$exists) {
            $stmt = $mysqli->prepare(
                "INSERT INTO top_games (game_id, game_name, updated_at) VALUES (?, ?, NOW())"
            );
            $stmt->bind_param('ss', $game_id, $game_name);
            if (!$stmt->execute()) {
                return ['error' => 'Error al insertar en top_games', 'detalle' => $stmt->error];
            }
            $stmt->close();
        }

        // Obtener videos y agrupar por usuario
        $ch = curl_init("https://api.twitch.tv/helix/videos?game_id={$game_id}&sort=views&first=40");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                "Client-ID: " . TWITCH_CLIENT_ID,
                "Authorization: Bearer $access_token"
            ]
        ]);
        $videos_response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $byUser = [];
        foreach ($videos_response['data'] as $video) {
            $user = $video['user_name'];
            if (!isset($byUser[$user])) {
                $byUser[$user] = [
                    'game_id'                => $game_id,
                    'game_name'              => $game_name,
                    'user_name'              => $user,
                    'total_videos'           => 0,
                    'total_views'            => 0,
                    'most_viewed_title'      => $video['title'],
                    'most_viewed_views'      => $video['view_count'],
                    'most_viewed_duration'   => $video['duration'],
                    'most_viewed_created_at' => $video['created_at']
                ];
            }
            $byUser[$user]['total_videos']++;
            $byUser[$user]['total_views']  += $video['view_count'];

            if ($video['view_count'] > $byUser[$user]['most_viewed_views']) {
                $byUser[$user] = array_merge($byUser[$user], [
                    'most_viewed_title'      => $video['title'],
                    'most_viewed_views'      => $video['view_count'],
                    'most_viewed_duration'   => $video['duration'],
                    'most_viewed_created_at' => $video['created_at']
                ]);
            }
        }

        // Persistir y añadir al array plano
        foreach ($byUser as $video) {
            $video['most_viewed_created_at'] = date('Y-m-d H:i:s', strtotime($video['most_viewed_created_at']));

            $stmt = $mysqli->prepare(
                "INSERT INTO top_videos (
                    game_id, user_name, total_videos, total_views,
                    most_viewed_title, most_viewed_views, most_viewed_duration,
                    most_viewed_created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                    total_videos = ?,
                    total_views = ?,
                    most_viewed_title = ?,
                    most_viewed_views = ?,
                    most_viewed_duration = ?,
                    most_viewed_created_at = ?,
                    updated_at = NOW()"
            );

            $stmt->bind_param(
                'ssiisissississ',
                $video['game_id'], $video['user_name'], $video['total_videos'], $video['total_views'],
                $video['most_viewed_title'], $video['most_viewed_views'], $video['most_viewed_duration'], $video['most_viewed_created_at'],
                $video['total_videos'], $video['total_views'], $video['most_viewed_title'], $video['most_viewed_views'],
                $video['most_viewed_duration'], $video['most_viewed_created_at']
            );
            if (!$stmt->execute()) {
                return ['error' => 'Error al insertar en top_videos', 'detalle' => $stmt->error];
            }
            $stmt->close();

            $topVideos[] = $video;
        }
    }

    return $topVideos;
}
