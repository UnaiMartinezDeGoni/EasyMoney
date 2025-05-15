<?php
// Archivo: auth.php
// Verifica la autenticación mediante token en la cabecera Bearer

header('Content-Type: application/json');

require_once __DIR__ . '/funcionesComunes.php'; // Usa la conexión centralizada

/**
 * Lanza un error 401 y detiene la ejecución si el token no es válido.
 */
function verificarAutenticacion(): void
{
    // Obtener instancia de mysqli desde el helper
    try {
        $mysqli = conectarMysqli();
    } catch (RuntimeException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error.'], JSON_PRETTY_PRINT);
        exit;
    }

    // Leer encabezado Authorization
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (strpos($authHeader, 'Bearer ') !== 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized. Token is missing or invalid.'], JSON_PRETTY_PRINT);
        exit;
    }

    $token = substr($authHeader, 7); // Quita "Bearer "

    // Verificar existencia y vigencia del token
    $stmt = $mysqli->prepare(
        "SELECT id FROM sessions WHERE token = ? AND expires_at > NOW()"
    );
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized. Token is invalid or expired.'], JSON_PRETTY_PRINT);
        exit;
    }
}
