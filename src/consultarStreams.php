<?php
require_once __DIR__ . '/../funcionesComunes.php';
header('Content-Type: application/json');

$streamer_id = $_GET['id'] ?? null; // ID de usuario de Twitch

$access_token = obtenerTokenTwitch();

$response = getStreamsInfo($access_token);

// Verificar si se encontraron streams
if (!empty($response['data'])) {
    http_response_code(200);

    $streamsData = [];

    foreach ($response['data'] as $stream) {
        $streamsData[] = [
            "tittle" => $stream['title'],
            "user_name" => $stream['user_name'],
        ];
    }
    echo json_encode($streamsData, JSON_PRETTY_PRINT);

} elseif (!verificarToken($access_token)){
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired"],JSON_PRETTY_PRINT);
}elseif (empty($response['data'])) {
    http_response_code(404);
    echo json_encode(["error" => "No se encontraron streams en vivo."],JSON_PRETTY_PRINT);
}else{
    http_response_code(500);
    echo json_encode(["error" => "Internal server error."],JSON_PRETTY_PRINT);
}
?>