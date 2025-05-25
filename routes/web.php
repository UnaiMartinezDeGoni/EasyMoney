<?php
/** @var \Laravel\Lumen\Routing\Router $router */

use Illuminate\Http\Request;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/register', function (Request $request) {
    return app()->call(
        'App\Http\Controllers\RegisterUserByEmail\RegisterUserByEmailController@register',
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
    '/analytics/streamer',
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

    $router->post(
        '/analytics/streams/enriched',
        function (Request $request) {
            return app()->call(
                'App\Http\Controllers\GetEnrichedStreams\GetEnrichedStreamsController@getEnrichedStreams',
                ['request' => $request]
            );
        }
    );

    $router->get(
        '/analytics/topsofthetops',
        ['uses' => 'TopOfTheTops\TopOfTheTopsController@index']
    );
});
