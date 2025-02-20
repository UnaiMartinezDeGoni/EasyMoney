<?php
header('Content-Type: application/json');
require_once 'auth.php'; // Agrega la funcion de autenticación

//Obtener la ruta solicitada y limpiar correctamente la URL
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$path = substr($path, strlen('analytics/'));

//Obtener metodo de la solicitud
$metodo = $_SERVER['REQUEST_METHOD'];

// Aplicar autenticación a todos los casos de uso que son de metodo get
switch ($path) {
    case 'user':    //Caso de uso: Consultar info del streamer
        if ($metodo === 'GET') {
            verificarAutenticacion();
            //"Llama" al archivo correspondiente de la carpeta src
            require_once 'src/consultarStreamer.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."], JSON_PRETTY_PRINT);
        }
        break;

    case 'streams': //Caso de uso: Consultar streams en vivo
        if ($metodo === 'GET') {
            verificarAutenticacion();
            //"Llama" al archivo correspondiente de la carpeta src
            require_once 'src/consultarStreams.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."], JSON_PRETTY_PRINT);
        }
        break;

    case 'streams/enriched':  //Caso de uso: Consultar streams enriquecidos
        if ($metodo === 'GET') {
            verificarAutenticacion();
            //"Llama" al archivo correspondiente de la carpeta src
            require_once 'src/consultarEnriquecidos.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."], JSON_PRETTY_PRINT);
        }
        break;

    case 'topsofthetops':   //Caso de uso: Información sobre los 40 videos más visualizados de cada uno de los tres juegos más populares en Twitch
        if ($metodo === 'GET') {
            verificarAutenticacion();
            //"Llama" al archivo correspondiente de la carpeta src
            require_once 'src/topOfTheTops.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."], JSON_PRETTY_PRINT);
        }
        break;

   case 'register': //Caso de uso: Solicitar Api Key para el registro
        if ($metodo === 'POST') {
            //"Llama" al archivo correspondiente de la carpeta src
            require_once 'src/registerUser.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."], JSON_PRETTY_PRINT);
        }
        break; 

   case 'token':    //Caso de uso: Solicitar token de acceso a la api    
        if ($metodo === 'POST') {
            //"Llama" al archivo correspondiente de la carpeta src
            require_once 'src/obtenerToken.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."], JSON_PRETTY_PRINT);
        }
        break;

    default:    //Información por defecto
        http_response_code(404);
        echo json_encode(["error" => "Recurso no encontrado o endpoint no valido."], JSON_PRETTY_PRINT);
        break;
}
?>
