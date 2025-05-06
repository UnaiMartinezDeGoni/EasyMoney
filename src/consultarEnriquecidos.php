<?php
require_once __DIR__ . '/../funcionesComunes.php';
header('Content-Type: application/json');

$limit = $_GET['limit'] ?? null;
$access_token = $_GET['access_token'] ?? obtenerTokenTwitch();

// verifica si el token es válido
if (!verificarToken($access_token)) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired"], JSON_PRETTY_PRINT);
    exit;
}

// valida el parámetro 'limit'
if (!is_numeric($limit) || $limit <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid 'limit' parameter."], JSON_PRETTY_PRINT);
    exit;
}

$respuesta = getStreamsInfo($access_token);

if (!empty($respuesta['data'])) {
    usort($respuesta['data'], function ($a, $b) {
        return $b['viewer_count'] - $a['viewer_count'];
    });

    $top_streams = array_slice($respuesta['data'], 0, $limit);
    $formattedStreams = [];

    foreach ($top_streams as $stream) {
        $user_info = getStreamerInfo($stream['user_id'], $access_token);
        $user_data = $user_info['data'][0] ?? [];

        $formattedStreams[] = [
            "stream_id" => $stream['id'],
            "user_id" => $stream['user_id'],
            "user_name" => $stream['user_name'],
            "viewer_count" => $stream['viewer_count'],
            "title" => $stream['title'],
            "user_display_name" => $user_data['display_name'] ?? $stream['user_name'],
            "profile_image_url" => $user_data['profile_image_url'] ?? null
        ];
    }

    http_response_code(200);
    echo json_encode($formattedStreams, JSON_PRETTY_PRINT);
    exit;
} else {
    http_response_code(500);
    echo json_encode(["error" => "Internal server error."], JSON_PRETTY_PRINT);
}
?>
