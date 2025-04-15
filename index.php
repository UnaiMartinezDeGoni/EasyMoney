<?php
// Definir la cabecera JSON para las respuestas
header('Content-Type: application/json');

// Recopilar información de depuración
$debugInfo = [
    'REQUEST_URI'  => $_SERVER['REQUEST_URI'],
    'PATH_INFO'    => isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null,
    'SCRIPT_NAME'  => $_SERVER['SCRIPT_NAME'],
    'PHP_SELF'     => $_SERVER['PHP_SELF']
];

// Obtener la ruta solicitada: si la URL fue reescrita, normalmente estará en PATH_INFO; en caso contrario, se elimina el posible prefijo 'analytics/'
if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '/') {
    $path = trim($_SERVER['PATH_INFO'], '/');
} else {
    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    if (strpos($uri, 'analytics/') === 0) {
        $path = substr($uri, strlen('analytics/'));
    } else {
        $path = $uri;
    }
}
$debugInfo['computed_path'] = $path;

// Si se pasa el parámetro debug (por ejemplo, https://tudominio.es/register?debug),
// se imprime la información de depuración y se termina la ejecución
if (isset($_GET['debug'])) {
    echo json_encode(['debug' => $debugInfo], JSON_PRETTY_PRINT);
    exit;
}

// Incluir el archivo de autenticación (asegúrate de que 'auth.php' existe y es correcto)
require_once 'auth.php';

// Procesar según la ruta solicitada
$metodo = $_SERVER['REQUEST_METHOD'];
switch ($path) {
    case 'user': // Consultar info del streamer
        if ($metodo === 'GET') {
            verificarAutenticacion();
            require_once 'src/consultarStreamer.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."], JSON_PRETTY_PRINT);
        }
        break;

    case 'streams': // Consultar streams en vivo
        if ($metodo === 'GET') {
            verificarAutenticacion();
            require_once 'src/consultarStreams.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."], JSON_PRETTY_PRINT);
        }
        break;

    case 'streams/enriched': // Consultar streams enriquecidos
        if ($metodo === 'GET') {
            verificarAutenticacion();
            require_once 'src/consultarEnriquecidos.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."], JSON_PRETTY_PRINT);
        }
        break;

    case 'topsofthetops': // Información de los 40 videos más vistos por juego
        if ($metodo === 'GET') {
            verificarAutenticacion();
            require_once 'src/topOfTheTops.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."], JSON_PRETTY_PRINT);
        }
        break;

    case 'register': // Solicitar Api Key para el registro
        if ($metodo === 'POST') {
            require_once 'src/registerUser.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."], JSON_PRETTY_PRINT);
        }
        break;

    case 'token': // Solicitar token de acceso a la API
        if ($metodo === 'POST') {
            require_once 'src/obtenerToken.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."], JSON_PRETTY_PRINT);
        }
        break;

    default: // Ruta no reconocida o recurso inexistente
        http_response_code(404);
        echo json_encode(["error" => "Recurso no encontrado o endpoint no válido."], JSON_PRETTY_PRINT);
        break;
}
?>
