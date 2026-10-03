<?php
session_start();
include "../public/includes/db.php"; // Asegúrate de que este archivo contiene la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $conn->real_escape_string($_POST["nombre"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $mensaje = $conn->real_escape_string($_POST["mensaje"]);
    
    // Insertar mensaje en la base de datos
    $sql = "INSERT INTO mensajes (nombre, email, mensaje, fecha) VALUES ('$nombre', '$email', '$mensaje', NOW())";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION["mensaje_exito"] = "Mensaje enviado correctamente.";
    } else {
        $_SESSION["mensaje_error"] = "Error al enviar el mensaje.";
    }

    header("Location: ../public/contacto.php"); // Redirige de vuelta a la página de contacto
    exit();
}
?>
