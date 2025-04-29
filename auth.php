<?php
require_once 'config.php'; // Asegura la conexión a la base de datos

global $mysqli; //variable global de conexion con la base de datos

function verificarAutenticacion() { //Funcion para verificar que el usuario tiene autorizacion para consultar a la api mediante el token
    global $mysqli;

    // Se intenta obtener el token de Authorization
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

    // Validar que el encabezado comience con "Bearer "
    if (strpos($authHeader, 'Bearer ') !== 0) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized. Token is invalid or expired."], JSON_PRETTY_PRINT);
        exit;
    }

    // Se elimina el prefijo "Bearer " para obtener solo el token
    $token = str_replace('Bearer ', '', $authHeader);

    // Conexion con la base de datos para comprobar si el token existe y si está activo
    $stmt = $mysqli->prepare("SELECT id FROM sessions WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si la consulta no devuelve resultados, el token no es válido o está expirado
    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized. Token is invalid or expired."], JSON_PRETTY_PRINT);
        exit;
    }
}