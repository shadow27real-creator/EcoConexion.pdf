<?php
declare(strict_types=1);

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'ecoconexion';

$mysqli = new mysqli($host, $user, $password, $database);

if ($mysqli->connect_error) {
    die('Error de conexión a la base de datos: ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');