<?php
require_once __DIR__ . '/../funcionesComunes.php'; //Para incluir el archivo de funciones comunes
header('Content-Type: application/json');

$limit = $_GET['limit'];

$access_token = $_GET['access_token'] ?? obtenerTokenTwitch();
$respuesta = getStreamsInfo($access_token);

if (!empty($respuesta['data']) && $limit > 0) {
    //Ordenar los streams por viewer_count en orden descendente
    usort($respuesta['data'], function ($a, $b) {
        return $b['viewer_count'] - $a['viewer_count'];
    });

    //Seleccionar los primeros N streams
    $top_streams = array_slice($respuesta['data'], 0, $limit);

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
} elseif($limit <= 0) {
    http_response_code(400); 
    echo json_encode(["error" => "Invalid 'limit' parameter."], JSON_PRETTY_PRINT);
}elseif (!verificarToken($access_token)) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired"], JSON_PRETTY_PRINT);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Internal server error."], JSON_PRETTY_PRINT);
}
?>

