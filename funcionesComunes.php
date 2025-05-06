<?php
header('Content-Type: application/json');

$client_id     = "7gkz3aw164wmtbo5u1m1n4cihpy8de";
$client_secret = "o99k9rok7tlprgf3bmzkqkdtqw6tan";

function obtenerTokenTwitch() {
    global $client_id, $client_secret;

    $url = "https://id.twitch.tv/oauth2/token";
    $post_fields = [
        'client_id'     => $client_id,
        'client_secret' => $client_secret,
        'grant_type'    => 'client_credentials'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $respuesta = curl_exec($ch);
    curl_close($ch);

    $datos = json_decode($respuesta, true);
    return $datos['access_token'] ?? null;
}

function verificarToken($access_token) {
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

function getStreamerInfo($streamer_id, $access_token) {
    global $client_id;
    $url = "https://api.twitch.tv/helix/users?id={$streamer_id}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Client-ID: $client_id",
        "Authorization: Bearer $access_token"
    ]);

    $respuesta = curl_exec($ch);
    curl_close($ch);

    return json_decode($respuesta, true);
}

function getStreamsInfo($access_token) {
    global $client_id;
    $url = "https://api.twitch.tv/helix/streams";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Client-ID: $client_id",
        "Authorization: Bearer $access_token"
    ]);

    $respuesta = curl_exec($ch);
    curl_close($ch);

    return json_decode($respuesta, true);
}

function generateApiKey() {
    return bin2hex(random_bytes(8));
}

function generateApiToken() {
    $token = bin2hex(random_bytes(8));
    $expires_at = date('Y-m-d H:i:s', strtotime('+3 days'));
    return ['token' => $token, 'expires_at' => $expires_at];
}

// Función optimizada: devuelve un array plano de videos y persiste en BD
function obtenerTopVideosTwitch($mysqli, $access_token, $since = 600) {
    global $client_id;

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
            "Client-ID: $client_id",
            "Authorization: Bearer $access_token"
        ]
    ]);
    $games_response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    foreach ($games_response['data'] as $game) {
        $game_id   = $game['id'];
        $game_name = $game['name'];

        // Asegurar top_games
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
                "Client-ID: $client_id",
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
                    'game_id'                  => $game_id,
                    'game_name'                => $game_name,
                    'user_name'                => $user,
                    'total_videos'             => 0,
                    'total_views'              => 0,
                    'most_viewed_title'        => $video['title'],
                    'most_viewed_views'        => $video['view_count'],
                    'most_viewed_duration'     => $video['duration'],
                    'most_viewed_created_at'   => $video['created_at']
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
?>
