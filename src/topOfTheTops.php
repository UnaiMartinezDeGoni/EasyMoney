<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';
require_once __DIR__ .'/../funcionesComunes.php';

global $mysqli;

$access_token = obtenerTokenTwitch();
if (!$access_token) {
    echo json_encode(["error" => "Internal Server Error. Please try again later."]);
    http_response_code(500);
    exit;
}

$since = isset($_GET['since']) ? (int)$_GET['since'] : 600;
if ($since <= 0) {
    echo json_encode(["error" => "Bad Request. Invalid or missing parameters."]);
    http_response_code(400);
    exit;
}

$topGames = obtenerTopVideosTwitch($mysqli, $access_token, $since);

if (isset($topGames["error"])) {
    echo json_encode($topGames);
    http_response_code(500);
    exit;
}

if (empty($topGames)) {
    echo json_encode(["error" => "Not Found. No data available."]);
    http_response_code(404);
    exit;
}

echo json_encode(array_values($topGames), JSON_PRETTY_PRINT);
http_response_code(200);
?>