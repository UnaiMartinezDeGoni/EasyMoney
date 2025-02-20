<?php

global $mysqli;
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';    // Archivo necesario para la conexión a la base de datos
require_once __DIR__ .'/../funcionesComunes.php'; 

// Captura el contenido del cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

//Caso en el que no se mande el email
if (!isset($data['email']) || empty($data['email'])) {
    echo json_encode(["error" => "The email is mandatory"], JSON_PRETTY_PRINT);
    http_response_code(400);
    exit;
}

//se verifica que la estructura del email sea correcta
$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
    echo json_encode(["error" => "The email must be a valid email address"], JSON_PRETTY_PRINT);
    http_response_code(400);
    exit;
}

try {
    // Se conecta a la base de datos y verifica si el usuario ya existe
    $stmt = $mysqli->prepare("SELECT api_key FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    //Genera una api_key con la funcion implementada de funcionesComunes.php
    $apiKey = generateApiKey();

    if ($user) {
        // Si el usuario ya existe, actualiza la API Key en la base
        $stmt = $mysqli->prepare("UPDATE users SET api_key = ? WHERE email = ?");
        $stmt->bind_param("ss", $apiKey, $email);
        $stmt->execute();
    } else {
        // Si el usuario no existe, inserta al usuario y la API Key en la base de datos
        $stmt = $mysqli->prepare("INSERT INTO users (email, api_key) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $apiKey);
        $stmt->execute();
    }

    // Devolver la API Key al usuario
    echo json_encode(["api_key" => $apiKey], JSON_PRETTY_PRINT);
    http_response_code(200);

} catch (Exception $e) {    //Caso en el que se detecte otro tipo de error
    echo json_encode(["error" => "Internal server error."], JSON_PRETTY_PRINT);
    http_response_code(500);
}
