<?php

global $mysqli;
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../funcionesComunes.php';

// Captura y decodifica el JSON entrante
$data = json_decode(file_get_contents('php://input'), true);

// 1) Email no indicado
if (empty($data['email'])) {
    header('Content-Type: application/json', true, 400);
    echo json_encode(["error" => "The email is mandatory"], JSON_PRETTY_PRINT);
    exit;
}

// 2) Email inválido
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    header('Content-Type: application/json', true, 400);
    echo json_encode(["error" => "The email must be a valid email address"], JSON_PRETTY_PRINT);
    exit;
}

$email = $data['email'];

try {
    // 3) Verifica si el usuario existe y obtiene su api_key actual
    $stmt = $mysqli->prepare("SELECT api_key FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Genera una nueva api_key
    $apiKey = generateApiKey();

    if ($user) {
        // 4a) Si existe, actualiza la API Key
        $stmt = $mysqli->prepare("UPDATE users SET api_key = ? WHERE email = ?");
        $stmt->bind_param("ss", $apiKey, $email);
        $stmt->execute();
    } else {
        // 4b) Si no existe, inserta un nuevo registro
        $stmt = $mysqli->prepare("INSERT INTO users (email, api_key) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $apiKey);
        $stmt->execute();
    }

    // 5) Respuesta exitosa con el api_key\    
    header('Content-Type: application/json', true, 200);
    echo json_encode(["api_key" => $apiKey], JSON_PRETTY_PRINT);
    exit;

} catch (Exception $e) {
    // 6) Error interno de servidor
    header('Content-Type: application/json', true, 500);
    echo json_encode(["error" => "Internal server error."], JSON_PRETTY_PRINT);
    exit;
}
