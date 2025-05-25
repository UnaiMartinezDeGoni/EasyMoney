<?php

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Aquí se crea la instancia central de la aplicación.
|
*/
$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

// Opcional: habilitar Facades o Eloquent si lo necesitas.
// $app->withFacades();
// $app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings & Middleware
|--------------------------------------------------------------------------
*/
// Registrar AuthService para inyección y para que los tests mockeen
$app->singleton(
    App\Services\AuthService::class,
    fn() => new App\Services\AuthService()
);

// Registrar interfaz de TwitchApiRepository para que los tests mockeen
$app->bind(
    App\Interfaces\TwitchApiRepositoryInterface::class,
    App\Repositories\TwitchApiRepository::class
);

// Registrar ambos alias de middleware, apuntando a la misma clase AuthenticateToken
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

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
*/
$app->configure('app');
$app->configure('database');

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
*/
$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

return $app;
