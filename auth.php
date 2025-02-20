<?php
require_once 'config.php'; // Asegura la conexión a la base de datos

global $mysqli;

function verificarAutenticacion() {
    global $mysqli;
    $authHeader = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? '';
    
    $stmt = $mysqli->prepare("SELECT 1 FROM sessions WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $authHeader);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized. Token is invalid or expired."]);
        exit;
    }
}
