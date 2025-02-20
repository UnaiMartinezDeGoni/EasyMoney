<?php
require_once __DIR__ . '/../funcionesComunes.php';//Para incluir el archivo de funciones comunes
header('Content-Type: application/json');

//Captura el ID de usuario de Twitch, en caso de no introducirse asigna por defecto -1
$streamer_id = $_GET['id'] ?? -1; 

/*Si el suario proporciona un access token para la consulta a la API de Twitch tendra prioridad este token
    pero en caso de no proporcionarse, por defecto se ejecuta la funcion que lo pide a Twitch implementada de 
    funcionesComunes.php -> hemos reliazado esta implemetacion como una forma de mostrar el error 401*/
$access_token = $_GET['access_token'] ?? obtenerTokenTwitch();

// Obtiene la informacion de los streamers mediante la funcion implementada de funcionesComunes.php
$respuesta = getStreamerInfo($streamer_id, $access_token);

//Imprimir el JSON formateado y enviar codigos de respuesta
if (isset($respuesta['data'][0]) && $streamer_id > 0) {
    http_response_code(200);
    echo json_encode($respuesta['data'][0], JSON_PRETTY_PRINT);  //Mostrar los datos del streamer
} elseif($streamer_id < 0){
  http_response_code(400);
  echo json_encode(["error" => "Invalid or missing 'id' parameter."],JSON_PRETTY_PRINT); 
}elseif (!verificarToken($access_token)){
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired"],JSON_PRETTY_PRINT);
}elseif(!isset($respuesta['data'][0])) {
    http_response_code(404);
    echo json_encode(["error" => "User  not found."], JSON_PRETTY_PRINT);
}else {
    http_response_code(500);
    echo json_encode(["error" => "Internal server error."], JSON_PRETTY_PRINT);
}
?>
