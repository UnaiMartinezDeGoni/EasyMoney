<?php
/** @var \Laravel\Lumen\Routing\Router $router */


use Illuminate\Http\Request;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/register', function (Request $request) {
    return app()->call(
        'App\Http\Controllers\RegisterUserByEmail\RegisterUserByEmailController@index',
        ['request' => $request]
    );
});


$router->post('/token', function () {
    header('Content-Type: application/json');
    require_once __DIR__ . '/../src/obtenerToken.php';
});

$protected = function () {
    header('Content-Type: application/json');
    require_once __DIR__ . '/../auth.php';
    verificarAutenticacion();
};

$router->get('/analytics/user', function (Request $request) {
    require_once __DIR__ . '/../auth.php';
    verificarAutenticacion();
    return app()->call('App\Http\Controllers\GetUserId\GetUserByIdController@index', [
        'request' => $request
    ]);
});


$router->get(
    'analytics/streams',
    [
        'middleware' => 'auth:api',
        'uses'      => 'GetStreams\GetStreamsController'
    ]
);

$router->get('/analytics/streams/enriched', function () use ($protected) {
    $protected();
    require_once __DIR__ . '/../src/consultarEnriquecidos.php';
});

$router->get('/analytics/topsofthetops', function () use ($protected) {
    $protected();
    require_once __DIR__ . '/../src/topOfTheTops.php';
});
