<?php
//Datos para la conexión con nuetra base de datos
$host_name = 'db5017192807.hosting-data.io';
$database = 'dbs13808390';
$user_name = 'dbu466004';
$password = 'XQW.By6y8H95kF.';

//Creacion de una conexion con la base de datos
$mysqli = new mysqli($host_name, $user_name, $password, $database);

//Caso de error al crear la conexion
if ($mysqli->connect_error) {
    die("Error al conectar con MySQL: " . $mysqli->connect_error);
}
?>
