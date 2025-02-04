<?php
require_once __DIR__ . '/../funcionesComunes.php';
header('Content-Type: application/json');

$streamer_id = $_GET['id'] ?? null; // ID de usuario de Twitch

$access_token = obtenerTokenTwitch();
//$access_token = getValidAccessToken($access_token = null)

$response = getStreamerInfo($streamer_id, $access_token);

// Imprimir el JSON formateado
if (isset($response['data'][0])) {
    http_response_code(200);
    echo json_encode($response['data'][0], JSON_PRETTY_PRINT);  // Mostrar los datos del streamer
} elseif (!verificarToken($access_token)){
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired"],JSON_PRETTY_PRINT);
}else {
    http_response_code(404);
    echo json_encode(["error" => "User  not found."], JSON_PRETTY_PRINT);
}
?>
