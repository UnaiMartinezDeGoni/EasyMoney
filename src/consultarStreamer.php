<?php 
require_once __DIR__ . '/../funcionesComunes.php';
require_once __DIR__ . '/../config.php'; // Incluir la conexión a la base de datos

header('Content-Type: application/json');

//Captura el ID de usuario de Twitch, en caso de no introducirse asigna por defecto -1
$streamer_id = $_GET['id'] ?? -1; 

/*Si el suario proporciona un access token para la consulta a la API de Twitch tendra prioridad este token
    pero en caso de no proporcionarse, por defecto se ejecuta la funcion que lo pide a Twitch implementada de 
    funcionesComunes.php -> hemos reliazado esta implemetacion como una forma de mostrar el error 401*/
$access_token = $_GET['access_token'] ?? obtenerTokenTwitch();

// Validación del parámetro 'id'
if ($streamer_id < 0) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid or missing 'id' parameter."], JSON_PRETTY_PRINT);
    exit;
}

// Consulta la API de Twitch para obtener información del streamer
try {
    $respuesta = getStreamerInfo($streamer_id, $access_token);
} catch (Exception $e) {
    // Error inesperado al comunicarse con Twitch
    http_response_code(500);
    echo json_encode(["error" => "Internal server error."], JSON_PRETTY_PRINT);
    exit;
}

// Manejo de error si no se encuentra al usuario
if (!isset($respuesta['data'][0])) {
    http_response_code(404);
    echo json_encode(["error" => "User not found."], JSON_PRETTY_PRINT);
    exit;
}

// Si se obtuvo correctamente la información del streamer, la devolvemos
$streamer_data = $respuesta['data'][0];
if (is_array($streamer_data)) {
    http_response_code(200);
    echo json_encode($streamer_data, JSON_PRETTY_PRINT);
    exit;
} else {
    // En caso de estructura inesperada
    http_response_code(500);
    echo json_encode(["error" => "Internal server error."], JSON_PRETTY_PRINT);
    exit;
}
?>
