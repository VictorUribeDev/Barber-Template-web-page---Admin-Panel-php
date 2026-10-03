<?php
$host = "localhost";
$user = "root"; // Usuario de phpMyAdmin
$pass = ""; // Deja vacío si usas XAMPP
$db = "barberia"; // Nombre de tu base de datos

$conn = new mysqli($host, $user, $pass, $db);

// Verifica la conexión
if (!$conn) {
    die("Error de conexión a la base de datos.");
}


?>
