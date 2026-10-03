<?php
$servername = "localhost";
$username = "root"; // Usuario de tu base de datos
$password = ""; // Contraseña de tu base de datos (vacía por defecto en XAMPP)
$dbname = "barberia"; // Nombre de tu base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
