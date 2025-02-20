<?php

global $mysqli;
header('Content-Type: application/json');
require_once 'config.php';
require_once 'funcionesComunes.php'; // Conexión a la base de datos

// Captura el contenido del cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || empty($data['email'])) {
    echo json_encode(["error" => "The email is mandatory"]);
    http_response_code(400);
    exit;
}

$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
    echo json_encode(["error" => "The email must be a valid email address"]);
    http_response_code(400);
    exit;
}

try {
    // Verificar si el usuario ya existe
    $stmt = $mysqli->prepare("SELECT api_key FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $apiKey = generateApiKey();

    if ($user) {
        // Si el usuario ya existe, actualizar la API Key
        $stmt = $mysqli->prepare("UPDATE users SET api_key = ? WHERE email = ?");
        $stmt->bind_param("ss", $apiKey, $email);
        $stmt->execute();
    } else {
        // Si el usuario no existe, insertarlo en la base de datos
        $stmt = $mysqli->prepare("INSERT INTO users (email, api_key) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $apiKey);
        $stmt->execute();
    }

    // Devolver la API Key al usuario
    echo json_encode(["api_key" => $apiKey]);
    http_response_code(200);
} catch (Exception $e) {
    echo json_encode(["error" => "Internal server error."]);
    http_response_code(500);
}
