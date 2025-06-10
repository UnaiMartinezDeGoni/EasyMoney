<?php

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
        'uses'       => 'GetStreamerById\GetStreamerByIdController@getStreamer',
    ]
);
$router->get('/analytics/streams/enriched', [
    'middleware' => 'auth.streamer',
    'uses'       => 'GetEnrichedStreams\GetEnrichedStreamsController@getEnrichedStreams',
]);
$router->get(
    '/analytics/topsofthetops',
    [    'middleware' => 'auth.streamer',
        'uses' => 'TopOfTheTops\TopOfTheTopsController@index']
);
$router->get(
    '/analytics/streams',
    ['middleware' => 'auth.streamer',
        'uses' => 'GetStreams\GetStreamsController']
);
