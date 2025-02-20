<?php
header('Content-Type: application/json');
require_once 'auth.php'; // Agrega la autenticación

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$path = substr($path, strlen('analytics/'));
$metodo = $_SERVER['REQUEST_METHOD'];

// Aplicar autenticación a todos los endpoints


switch ($path) {
    case 'user':
    	verificarAutenticacion();
        if ($metodo === 'GET') {
            require_once 'src/consultarStreamer.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."]);
        }
        break;

    case 'streams':
    	verificarAutenticacion();
        if ($metodo === 'GET') {
            require_once 'src/consultarStreams.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."]);
        }
        break;

    case 'topsofthetops':
    	verificarAutenticacion();
        if ($metodo === 'GET') {
            require_once 'src/topOfTheTops.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."]);
        }
        break;
   case 'register':
    	
        if ($metodo === 'POST') {
            require_once 'registerUser.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."]);
        }
        break; 
   case 'token':
    	
        if ($metodo === 'POST') {
            require_once 'obtenerToken.php';
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
