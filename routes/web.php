<?php
/** @var \Laravel\Lumen\Routing\Router $router */

use Illuminate\Http\Request;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/register', function (Request $request) {
    return app()->call(
        'App\Http\Controllers\Register\RegisterController@register',
        ['request' => $request]
    );
});

$router->post('/token', function (Request $request) {
    return app()->call(
        'App\Http\Controllers\Token\TokenController@generateToken',
        ['request' => $request]
    );
});

$router->get(
    '/analytics/user',
    [
        'middleware' => 'auth.streamer',
        'uses'       => 'GetStreamerById\GetStreamerByIdController@index',
    ]
);

$router->group(['middleware' => 'auth.token'], function () use ($router) {
    $router->get(
        '/analytics/streams',
        ['uses' => 'GetStreams\GetStreamsController']
    );


    $router->get('/analytics/streams/enriched', [
        'middleware' => 'auth',   // <-- aquí estaba 'auth'
        'uses'       => 'GetEnrichedStreams\GetEnrichedStreamsController@getEnrichedStreams',
    ]);

    $router->get(
        '/analytics/topsofthetops',
        ['uses' => 'TopOfTheTops\TopOfTheTopsController@index']
    );
});
