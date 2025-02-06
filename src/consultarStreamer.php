<?php
require_once __DIR__ . '/../funcionesComunes.php';
header('Content-Type: application/json');

$access_token = obtenerTokenTwitch();
$response = getStreamsInfo($access_token); // Llamada a la función que obtiene los streams en vivo

// Imprimir el JSON formateado
if (!empty($response['data'])) {
    http_response_code(200);
    echo json_encode($response['data'], JSON_PRETTY_PRINT);  // Mostrar los streams en vivo
} elseif (!verificarToken($access_token)) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired"], JSON_PRETTY_PRINT);
} else {
    http_response_code(404);
    echo json_encode(["error" => "No live streams found."], JSON_PRETTY_PRINT);
}
?>
