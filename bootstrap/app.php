<?php

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->singleton(
    App\Services\AuthService::class,
    function ($app) {
        return new App\Services\AuthService($app->make(App\Interfaces\DBRepositoriesInterface::class));
    }
);

$app->bind(
    App\Interfaces\TwitchApiRepositoryInterface::class,
    App\Repositories\TwitchApiRepository::class

);

$app->routeMiddleware([
    'auth.token'    => App\Http\Middleware\AuthenticateToken::class,
    'auth.streamer' => App\Http\Middleware\AuthenticateToken::class,
]);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->configure('app');
$app->configure('database');

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

return $app;
