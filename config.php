<?php
//Datos para la conexión con nuetra base de datos
$host = 'localhost';
$usuario = 'root';
$contrasena = ''; // en XAMPP, por defecto no hay contraseña
$base_de_datos = 'dbs13808390';

//Creacion de una conexion con la base de datos
$mysqli = new mysqli($host, $usuario, $contrasena, $base_de_datos);

//Caso de error al crear la conexion
if ($mysqli->connect_error) {
    die("Error al conectar con MySQL: " . $mysqli->connect_error);
}
?>
