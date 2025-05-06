<?php
/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Registro de usuario (devuelve api_key)
$router->post('/register', function () {
    require_once __DIR__.'/../src/registerUser.php';
});

// Obtención de token (POST /token)
$router->post('/token', function () {
    require_once __DIR__.'/../src/obtenerToken.php';
});

// Consultar datos de un streamer (GET /user)
$router->get('/user', function () {
    require_once __DIR__.'/../src/consultarStreamer.php';
});

// Streams en vivo (GET /streams)
$router->get('/streams', function () {
    require_once __DIR__.'/../src/consultarStreams.php';
});

// Streams enriquecidos (GET /streams/enriched)
$router->get('/streams/enriched', function () {
    require_once __DIR__.'/../src/consultarEnriquecidos.php';
});

// Top of the tops (GET /topsofthetops)
$router->get('/topsofthetops', function () {
    require_once __DIR__.'/../src/topOfTheTops.php';
});

