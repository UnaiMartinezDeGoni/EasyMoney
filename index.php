<?php
header('Content-Type: application/json');
require_once 'auth.php'; // Agrega la autenticación

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$path = substr($path, strlen('analytics/'));
$metodo = $_SERVER['REQUEST_METHOD'];

// Aplicar autenticación a todos los endpoints
verificarAutenticacion();

switch ($path) {
    case 'user':
        if ($metodo === 'GET') {
            require_once 'src/consultarStreamer.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."]);
        }
        break;

    case 'streams':
        if ($metodo === 'GET') {
            require_once 'src/consultarStreams.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."]);
        }
        break;

    case 'topsofthetops':  
        if ($metodo === 'GET') {
            require_once 'src/topsofthetops.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Recurso no encontrado o endpoint no válido."], JSON_PRETTY_PRINT);
        break;
}
?>
