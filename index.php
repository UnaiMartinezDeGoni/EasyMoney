<?php
header('Content-Type: application/json');

//Obtener la ruta solicitada y limpiar correctamente la URL
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$path = substr($path, strlen('index.php/analytics/'));


//Obtener metodo de la solicitud
$metodo = $_SERVER['REQUEST_METHOD'];

//Detectar el recurso en la URL
switch ($path) {
    case 'user':  //Caso de uso: Consultar info del streamer
        if ($metodo === 'GET') {
            //"Llamar" a el archivo consultarStreamer
            require_once 'src/consultarStreamer.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."]);
        }
        break;

    case 'streams':  //Caso de uso: Consultar streams en vivo
        if ($metodo === 'GET') {
          
            //"Llamar" a el archivo consultarStreams
            require_once 'src/consultarStreams.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."]);
        }
        break;

    case 'streams/enriched':  //Caso de uso: Consultar streams enriquecidos
        if ($metodo === 'GET') {
            //"Llamar" a el archivo consultarEnriquecidos
            require_once 'src/consultarEnriquecidos.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."]);
        }
        break;

    default: //Información por defecto
        http_response_code(404);
        echo json_encode(["error" => "Recurso no encontrado o endpoint no valido."],JSON_PRETTY_PRINT);
        break;
}
?>
