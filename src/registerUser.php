<?php
// src/registerUser.php

header('Content-Type: application/json');

require_once __DIR__ . '/../funcionesComunes.php';


// Lee el JSON
$data = json_decode(file_get_contents('php://input'), true);

// Validaciones…
if (empty($data['email'])) {
    http_response_code(400);
    echo json_encode(["error" => "The email is mandatory"], JSON_PRETTY_PRINT);
    exit;
}
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "The email must be a valid email address"], JSON_PRETTY_PRINT);
    exit;
}

$email = $data['email'];

try {
    $mysqli = conectarMysqli();


    // 3) Verifica si el usuario existe
    $stmt = $mysqli->prepare("SELECT api_key FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();


    // 4) Genera o actualiza la API key
    $apiKey = generateApiKey();
    if ($user) {
        $stmt = $mysqli->prepare("UPDATE users SET api_key = ? WHERE email = ?");
        $stmt->bind_param("ss", $apiKey, $email);
        $stmt->execute();
    } else {
        $stmt = $mysqli->prepare("INSERT INTO users (email, api_key) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $apiKey);
        $stmt->execute();
    }

    // 5) Responde con la API key
    http_response_code(200);
    echo json_encode(["api_key" => $apiKey], JSON_PRETTY_PRINT);
    exit;

} catch (Throwable $e) {
    // Para depuración activa, puedes:
    error_log('[RegisterUser] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Internal server error."], JSON_PRETTY_PRINT);
    exit;
}
