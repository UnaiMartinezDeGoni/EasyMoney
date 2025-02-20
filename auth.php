<?php
require_once 'config.php'; // Asegura la conexión a la base de datos

global $mysqli; //variable global de conexion con la base de datos

function verificarAutenticacion() { //Funcion para verificar que el usuario tiene autorizacion para consultar a la api mediante el token
    global $mysqli;
    // Inicialmente se intenta obtener el token de Authorization (Esto por si se trata la web en otro servidor o entorno)
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

    // Si no encontramos el token en Authorization, verificamos en X-Auth-Token
    if (empty($authHeader) && isset($_SERVER['HTTP_X_AUTH_TOKEN'])) {
        /* Obtiene el X-Auth-Token del encabezado HTTP para comprobar si el usuario tiene un
        token valido. Este apartado se realiza mediante un X-Auth-Token debido a que nuestro servidor
        por temas de privacidad de la compañia no permite que se devuelvan los valores de tipo "Authorization: Bearer token"  */
        $authHeader = $_SERVER['HTTP_X_AUTH_TOKEN'];

        // El token en X-Auth-Token debe obligatoriamente empezar con "Bearer ", sino sera considerado como invalido
        if (strpos($authHeader, 'Bearer ') !== 0) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized. Token is invalid or expired."], JSON_PRETTY_PRINT);
            exit;
        }
    }

    // Se elimina el prefijo "Bearer " si está presente
    $authHeader = str_replace('Bearer ', '', $authHeader);
    
    // Conexion con la base de datos para comprobar si el token existe y si es así, verificar que esta activo
    $stmt = $mysqli->prepare("SELECT id FROM sessions WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $authHeader);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Si la consulta a la base devuelve un null, no se le autoriza a usar la api
    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized. Token is invalid or expired."], JSON_PRETTY_PRINT);
        exit;
    }
}
