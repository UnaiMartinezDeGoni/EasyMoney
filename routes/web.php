<?php
/** @var \Laravel\Lumen\Routing\Router $router */


use Illuminate\Http\Request;

// Versión de la API
$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Registro de usuario (POST /register)
$router->post('/register', function () {
    require_once __DIR__ . '/../src/registerUser.php';
});


// Obtención de token (POST /token)
$router->post('/token', function () {
    header('Content-Type: application/json');
    require_once __DIR__ . '/../src/obtenerToken.php';
});

// Middlewares para rutas protegidas
$protected = function () {
    header('Content-Type: application/json');
    require_once __DIR__ . '/../auth.php';
    verificarAutenticacion();
};

// Consultar datos de un streamer (GET /analytics/user?id=…)
$router->get('/analytics/user', function () use ($protected) {
    $protected();

    // 1) Capturamos el parámetro “id” y lo casteamos a entero
    /*$streamer_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;


    // 2) Validación básica
    header('Content-Type: application/json');
    if ($streamer_id < 1) {
        http_response_code(400);
        echo json_encode([
            "error" => "Invalid or missing 'id' parameter."
        ], JSON_PRETTY_PRINT);
        return;
    }*/

    // 3) Incluimos la lógica y delegamos
    require_once __DIR__ . '/../src/consultarStreamer.php';
    //getUserById($streamer_id);
});

// Streams en vivo (GET /analytics/streams)
$router->get('/analytics/streams', function () use ($protected) {
    $protected();
    require_once __DIR__ . '/../src/consultarStreams.php';
});

// Streams enriquecidos (GET /analytics/streams/enriched)
$router->get('/analytics/streams/enriched', function () use ($protected) {
    $protected();
    require_once __DIR__ . '/../src/consultarEnriquecidos.php';
});

// Top of the tops (GET /analytics/topsofthetops)
$router->get('/analytics/topsofthetops', function () use ($protected) {
    $protected();
    require_once __DIR__ . '/../src/topOfTheTops.php';
});