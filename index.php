<?PHP
header('Content-Type: application/json');  // Todas las respuestas serán JSON

// Obtener la ruta solicitada y limpiar correctamente la URL
$path = trim(str_replace('index.php', '', basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))), '/');
$method = $_SERVER['REQUEST_METHOD'];

// Enrutador RESTful: Detectar el recurso en la URL
switch ($path) {
    case 'user':  // Caso de uso: Consultar info del streamer
        if ($method === 'GET') {
            require_once 'src/consultarStreamer.php';
        } else {
            http_response_code(404);  // Metodo no permitido
            echo json_encode(["error" => "Método HTTP no permitido."]);
        }
        break;

    case 'streams':  // Caso de uso: Consultar streams en vivo
        if ($method === 'GET') {
            require_once 'src/consultarStreams.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."]);
        }
        break;
    
    case 'enriched':  // Caso de uso: Consultar streams en vivo
        if ($method === 'GET') {
            require_once 'src/consultarEnriquecidos.php';
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Método HTTP no permitido."]);
        }
        break;

    default:
        http_response_code(404);  // Ruta no encontrada
        echo json_encode(["error" => "Recurso no encontrado o endpoint no válido."]);
        break;
}

?>

