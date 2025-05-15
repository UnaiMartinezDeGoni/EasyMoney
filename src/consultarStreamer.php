<?php
require_once __DIR__ . '/../funcionesComunes.php';
header('Content-Type: application/json');

// Captura el ID de usuario de Twitch
$streamer_id = isset($_GET['id']) ? (int) $_GET['id'] : -1;

// Validación inicial del parámetro 'id'
if ($streamer_id < 1) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid or missing 'id' parameter."], JSON_PRETTY_PRINT);
    exit;
}

// Obtener o generar token de acceso
$access_token = $_GET['access_token'] ?? obtenerTokenTwitch();
if (empty($access_token)) {
    http_response_code(401);
    echo json_encode(["error" => "Could not obtain a valid access token."], JSON_PRETTY_PRINT);
    exit;
}

// Llamada a la API de Twitch
$respuesta = getStreamerInfo($streamer_id, $access_token);

// Si Twitch devuelve un error estructurado (status+message)
if (isset($respuesta['status']) && isset($respuesta['message'])) {
    $status = (int) $respuesta['status'];
    // Si es unauthorized
    if ($status === 401) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized: {$respuesta['message']}"], JSON_PRETTY_PRINT);
        exit;
    }
    // Para otros errores de Twitch, tratarlos como 'Not found' en este contexto
    http_response_code(404);
    echo json_encode(["error" => "User not found."], JSON_PRETTY_PRINT);
    exit;
}

// Validar que 'data' exista y sea array
if (!is_array($respuesta) || !array_key_exists('data', $respuesta) || !is_array($respuesta['data'])) {
    http_response_code(500);
    echo json_encode(["error" => "Internal server error."], JSON_PRETTY_PRINT);
    exit;
}

// Si 'data' está vacío, devolvemos 404
if (empty($respuesta['data'])) {
    http_response_code(404);
    echo json_encode(["error" => "User not found."], JSON_PRETTY_PRINT);
    exit;
}

// Devolvemos el primer elemento de 'data'
http_response_code(200);
echo json_encode($respuesta['data'][0], JSON_PRETTY_PRINT);
exit;
