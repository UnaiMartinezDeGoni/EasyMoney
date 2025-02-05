<?php
require_once __DIR__ . '/../funcionesComunes.php';
header('Content-Type: application/json');

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10; // Número de streams a devolver, por defecto 10

$access_token = obtenerTokenTwitch();
$response = getStreamsInfo($access_token);

if (!empty($response['data'])) {
    // Ordenar los streams por viewer_count en orden descendente
    usort($response['data'], function ($a, $b) {
        return $b['viewer_count'] - $a['viewer_count'];
    });
    
    // Seleccionar los primeros N streams
    $top_streams = array_slice($response['data'], 0, $limit);
    
    $enrichedStreams = [];
    
    foreach ($top_streams as $stream) {
        // Obtener información del usuario
        $user_info = getStreamerInfo($stream['user_id'], $access_token);
        $user_data = $user_info['data'][0] ?? [];
        
        // Construir datos enriquecidos
        $enrichedStreams[] = [
            "title" => $stream['title'],
            "user_name" => $stream['user_name'],
            "viewer_count" => $stream['viewer_count'],
            "display_name" => $user_data['display_name'] ?? $stream['user_name'],
            "profile_image_url" => $user_data['profile_image_url'] ?? null
        ];
    }
    
    http_response_code(200);
    echo json_encode($enrichedStreams, JSON_PRETTY_PRINT);
} elseif (!verificarToken($access_token)) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired"], JSON_PRETTY_PRINT);
} else {
    http_response_code(404);
    echo json_encode(["error" => "No se encontraron streams en vivo."], JSON_PRETTY_PRINT);
}
?>
