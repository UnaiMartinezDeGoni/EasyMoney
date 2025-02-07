<?php
require_once __DIR__ . '/../funcionesComunes.php'; //Para incluir el archivo de funciones comunes
header('Content-Type: application/json');

$access_token = $_GET['access_token'] ?? obtenerTokenTwitch();

$respuesta = getStreamsInfo($access_token);

//Verificar si se encontraron streams
if (!empty($respuesta['data'])) {
    http_response_code(200);

    $streamsData = [];
	//Iteracion para obtener la lista de streams de twitch y seleccionar los atributos que nos interesan
    foreach ($respuesta['data'] as $stream) {
        $streamsData[] = [
            "tittle" => $stream['title'],
            "user_name" => $stream['user_name'],
        ];
    }
    
    echo json_encode($streamsData, JSON_PRETTY_PRINT);

} elseif (!verificarToken($access_token)){ 
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired"],JSON_PRETTY_PRINT);
}else{
    http_response_code(500);
    echo json_encode(["error" => "Internal server error."],JSON_PRETTY_PRINT);
}
?>