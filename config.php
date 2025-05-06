<?php
// Datos de conexión recogidos de variables de entorno (con valores por defecto)
$host        = getenv('DB_HOST')     ?: 'db';
$port        = getenv('DB_PORT')     ?: 3306;
$usuario     = getenv('DB_USERNAME') ?: 'root';
$contrasena  = getenv('DB_PASSWORD') ?: '';
$base_de_datos = getenv('DB_DATABASE') ?: 'dbs13808390';

// Forzar conexión por TCP especificando el puerto
$mysqli = new mysqli($host, $usuario, $contrasena, $base_de_datos, $port);

// Comprobar errores de conexión
if ($mysqli->connect_error) {
    die("Error al conectar con MySQL: " . $mysqli->connect_error);
}

// Opcional: establecer conjunto de caracteres
$mysqli->set_charset('utf8mb4');
