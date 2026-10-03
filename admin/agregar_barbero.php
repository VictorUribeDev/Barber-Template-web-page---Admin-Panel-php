<?php
include "../public/includes/db.php"; // Asegúrate de tener la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_barbero = $_POST["nombre_barbero"];
    
    // Insertar el nuevo barbero en la tabla barberos
    $sql = "INSERT INTO barberos (nombre) VALUES ('$nombre_barbero')";

    if ($conn->query($sql) === TRUE) {
        echo "Barbero agregado con éxito. <a href='dashboard.php'>Volver al panel</a>";
    } else {
        echo "Error al agregar barbero: " . $conn->error;
    }
}
?>

