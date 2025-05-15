<?php
// config.php

// 1) Autoloader de Composer
require __DIR__ . '/vendor/autoload.php';

// 2) Carga de variables de entorno (.env)
if (file_exists(__DIR__ . '/.env')) {
    // en Lumen ya están cargadas en bootstrap/app.php, pero tus scripts "planos" necesitan cargarlas:
    (new Dotenv\Dotenv(__DIR__))->load();
}

// 3) Conexión a la base de datos (mysqli)
$host     = getenv('DB_HOST')     ?: '127.0.0.1';
$port     = getenv('DB_PORT')     ?: 3306;
$dbname   = getenv('DB_DATABASE') ?: '';
$user     = getenv('DB_USERNAME') ?: '';
$password = getenv('DB_PASSWORD') ?: '';

$mysqli = new mysqli($host, $user, $password, $dbname, $port);
if ($mysqli->connect_error) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'Database connection failed: ' . $mysqli->connect_error], JSON_PRETTY_PRINT);
    exit;
}
