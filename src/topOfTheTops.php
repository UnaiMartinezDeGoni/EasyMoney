<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../funcionesComunes.php';

try {
    // 1) Conectar a la base de datos
    $mysqli = conectarMysqli();
} catch (Throwable $e) {
    error_log('[TopOfTheTops] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Internal Server Error. Please try again later."], JSON_PRETTY_PRINT);
    exit;
}

// 2) Obtener token de Twitch
$access_token = obtenerTokenTwitch();
if (!$access_token) {
    http_response_code(500);
    echo json_encode(["error" => "Internal Server Error. Please try again later."], JSON_PRETTY_PRINT);
    exit;
}

// 3) Validar parámetro 'since'
$since = isset($_GET['since']) ? filter_var($_GET['since'], FILTER_VALIDATE_INT) : 600;
if ($since === false || $since <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Bad Request. Invalid or missing parameters."], JSON_PRETTY_PRINT);
    exit;
}

// 4) Obtener datos de top videos
try {
    $topGames = obtenerTopVideosTwitch($access_token, $since);
} catch (Throwable $e) {
    error_log('[TopOfTheTops] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Internal Server Error. Please try again later."], JSON_PRETTY_PRINT);
    exit;
}

// 5) Si la función devuelve un error explícito
if (isset($topGames['error'])) {
    http_response_code(500);
    echo json_encode($topGames, JSON_PRETTY_PRINT);
    exit;
}

// 6) Si no hay datos
if (empty($topGames)) {
    http_response_code(404);
    echo json_encode(["error" => "Not Found. No data available."], JSON_PRETTY_PRINT);
    exit;
}

// 7) Devolver resultado exitoso
http_response_code(200);
echo json_encode(array_values($topGames), JSON_PRETTY_PRINT);
exit;
