<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';    // Archivo necesario para la conexión a la base de datos
require_once __DIR__ .'/../funcionesComunes.php';

global $mysqli;

$access_token = obtenerTokenTwitch(); //Obtiene el token para acceder a la API de Twitch
if (!$access_token) {   //Caso de que se produzca un error en la obtencion del token
    http_response_code(500);
    echo json_encode(["error" => "Internal Server Error. Please try again later."], JSON_PRETTY_PRINT);
    exit;
}

//En el caso de que se le proporcione obtiene del usuario el valor de la variable since
$since = isset($_GET['since']) ? (int)$_GET['since'] : 600; //En el caso de no proporcionarse le da un valor por defecto de 600 segundo (10 minutos) 
if ($since <= 0 || !is_numeric($since)) { //Caso en el que los parametros sean incorrec
    http_response_code(400);
    echo json_encode(["error" => "Bad Request. Invalid or missing parameters."], JSON_PRETTY_PRINT);
    exit;
}

//Obtiene los datos sobe los top videos de Twitch sobre el top 3 de juesgos con la funcion implementada de funcionesComunes.php
$topGames = obtenerTopVideosTwitch($mysqli, $access_token, $since);

//Si se prduce un error devuelve un error 500
if (isset($topGames["error"])) {
    http_response_code(500);
    echo json_encode($topGames);
    exit;
}

//Si la variable que debe recoger la informacion del top ($topGames) resulta vacia, devuelve un error 404
if (empty($topGames)) {
    http_response_code(404);
    echo json_encode(["error" => "Not Found. No data available."], JSON_PRETTY_PRINT);
    exit;
}

//Devuelve la informacion del top solicitada
http_response_code(200);
echo json_encode(array_values($topGames), JSON_PRETTY_PRINT);
?>