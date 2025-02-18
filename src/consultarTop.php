<?php
header('Content-Type: application/json');
require_once '../config.php';
require_once '../funcionesComunes.php';

global $mysqli;

$since = isset($_GET['since']) ? (int)$_GET['since'] : 600; // Por defecto, 10 minutos
$last_update_limit = date('Y-m-d H:i:s', time() - $since);

// Verificar si hay datos recientes en caché
$query = "SELECT * FROM top_videos WHERE updated_at > ? ORDER BY most_viewed_views DESC LIMIT 120";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $last_update_limit);
$stmt->execute();
$result = $stmt->get_result();

$topGames = [];  // Asegúrate de inicializar la variable como un array

while ($row = $result->fetch_assoc()) {
    $topGames[$row['game_id']][] = $row;
}
$stmt->close();

  // Asegúrate de inicializar la variable antes de usarla

if (count($topGames) < 3) {
    // Obtener token de Twitch
    $access_token = obtenerTokenTwitch();

    // Obtener los 3 juegos más populares
    $top_games_url = "https://api.twitch.tv/helix/games/top?first=3";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $top_games_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Client-ID: $client_id",
        "Authorization: Bearer $access_token"
    ]);
    $games_response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    // Inicializa nuevamente $topGames por si no tenía datos
    $topGames = [];  // Inicializa nuevamente $topGames por si no tenía datos

    foreach ($games_response['data'] as $game) {
        $game_id = $game['id'];
        $game_name = $game['name'];
        
        // Obtener los 40 videos más vistos del juego
        $videos_url = "https://api.twitch.tv/helix/videos?game_id=$game_id&sort=views&first=40";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $videos_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Client-ID: $client_id",
            "Authorization: Bearer $access_token"
        ]);
        $videos_response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $videos_by_user = [];

        foreach ($videos_response['data'] as $video) {
            $user = $video['user_name'];
            if (!isset($videos_by_user[$user])) {
                $videos_by_user[$user] = [
                    'game_id' => $game_id,
                    'game_name' => $game_name,
                    'user_name' => $user,
                    'total_videos' => 0,
                    'total_views' => 0,
                    'most_viewed_title' => $video['title'],
                    'most_viewed_views' => $video['view_count'],
                    'most_viewed_duration' => $video['duration'],
                    'most_viewed_created_at' => $video['created_at']
                ];
            }
            $videos_by_user[$user]['total_videos']++;
            $videos_by_user[$user]['total_views'] += $video['view_count'];

            if ($video['view_count'] > $videos_by_user[$user]['most_viewed_views']) {
                $videos_by_user[$user]['most_viewed_title'] = $video['title'];
                $videos_by_user[$user]['most_viewed_views'] = $video['view_count'];
                $videos_by_user[$user]['most_viewed_duration'] = $video['duration'];
                $videos_by_user[$user]['most_viewed_created_at'] = $video['created_at'];
            }
        }
    
    // Agregar los datos de cada juego y sus videos al array $topGames
    /*foreach ($videos_by_user as $video) {
        $stmt = $mysqli->prepare("INSERT INTO top_videos (game_id, user_name, total_videos, total_views, most_viewed_title, most_viewed_views, most_viewed_duration, most_viewed_created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE total_videos=?, total_views=?, most_viewed_title=?, most_viewed_views=?, most_viewed_duration=?, most_viewed_created_at=?, updated_at=NOW()");
        $stmt->bind_param("ssiisissississ", $video['game_id'], $video['user_name'], $video['total_videos'], $video['total_views'], $video['most_viewed_title'], $video['most_viewed_views'], $video['most_viewed_duration'], $video['most_viewed_created_at'], $video['total_videos'], $video['total_views'], $video['most_viewed_title'], $video['most_viewed_views'], $video['most_viewed_duration'], $video['most_viewed_created_at']);
        $stmt->execute();
        $stmt->close();
        
        $topGames[$video['game_id']][] = $video;
    }*/
    }
}

echo json_encode(array_values($topGames), JSON_PRETTY_PRINT);
http_response_code(200);
