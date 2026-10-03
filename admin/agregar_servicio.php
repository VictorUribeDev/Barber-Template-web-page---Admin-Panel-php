<?php
include "../public/includes/db.php"; // Asegúrate de tener la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_servicio = $_POST["nombre_servicio"];
    $precio = $_POST["precio_servicio"];
    
    // Insertar el nuevo servicio en la tabla servicios
    $sql = "INSERT INTO servicio (nombre, precio) VALUES ('$nombre_servicio', '$precio')";

    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<div class='alert error'>Error al agregar servicio: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Servicio</title>
    <style>
        /* Estilos CSS aquí */
    </style>
</head>
<body>

    <div class="container">
        <div class="form-container">
            <h2>Agregar Servicio</h2>
            <form action="agregar_servicio.php" method="POST">
                <div class="input-group">
                    <label for="nombre_servicio">Nombre del Servicio</label>
                    <input type="text" id="nombre_servicio" name="nombre_servicio" required placeholder="Ej: Corte de cabello">
                </div>
                
                <div class="input-group">
                    <label for="precio_servicio">Precio</label>
                    <input type="number" id="precio_servicio" name="precio_servicio" step="0.01" min="0" required placeholder="Ej: 25.00">
                </div>

                <button type="submit" class="btn">Agregar Servicio</button>
            </form>
        </div>
    </div>

</body>
</html>