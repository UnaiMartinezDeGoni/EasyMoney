<?php
$host_name = 'localhost';
$database = 'vyv';
$user_name = 'root';
$password = '';

$mysqli = new mysqli($host_name, $user_name, $password, $database);

if ($mysqli->connect_error) {
    die("Error al conectar con MySQL: " . $mysqli->connect_error);
}

?>
