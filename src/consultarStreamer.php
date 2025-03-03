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

// Obtiene la informacion de los streamers mediante la funcion implementada de funcionesComunes.php
$streamer_id = $_GET['id'] ?? -1;
$access_token = $_GET['access_token'] ?? obtenerTokenTwitch();

if ($streamer_id < 0) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid or missing 'id' parameter."], JSON_PRETTY_PRINT);
    exit;
}

// Verificar si el usuario ya está en la base de datos
$stmt = $mysqli->prepare("SELECT * FROM streamers WHERE id = ?");
$stmt->bind_param("s", $streamer_id);
$stmt->execute();
$result = $stmt->get_result();
$streamer = $result->fetch_assoc();
$stmt->close();

if ($streamer) {
    // Si el usuario ya está en la base de datos, devolver los datos almacenados
    http_response_code(200);
    echo json_encode($streamer, JSON_PRETTY_PRINT);
    exit;
}

// Si el usuario no está en la base de datos, consultar la API de Twitch
$respuesta = getStreamerInfo($streamer_id, $access_token);

if (!isset($respuesta['data'][0])) {
    http_response_code(404);
    echo json_encode(["error" => "User not found."], JSON_PRETTY_PRINT);
    exit;
}

$streamer_data = $respuesta['data'][0];

// Guardar el nuevo usuario en la base de datos
$stmt = $mysqli->prepare("INSERT INTO streamers (id, login, display_name, type, broadcaster_type, description, profile_image_url, offline_image_url, view_count, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssss",
    $streamer_data['id'],
    $streamer_data['login'],
    $streamer_data['display_name'],
    $streamer_data['type'],
    $streamer_data['broadcaster_type'],
    $streamer_data['description'],
    $streamer_data['profile_image_url'],
    $streamer_data['offline_image_url'],
    $streamer_data['view_count'],
    $streamer_data['created_at']
);

if ($stmt->execute()) {
    http_response_code(201); // 201 Created
    echo json_encode($streamer_data, JSON_PRETTY_PRINT);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error inserting user into database."], JSON_PRETTY_PRINT);
}

$stmt->close();
?>
