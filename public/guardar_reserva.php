<?php  
session_start(); // Mantener la sesión del usuario  
include "../public/includes/db.php"; // Conexión a la BD  

if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    // Recoger datos del formulario  
    $nombre = trim($_POST["nombre"]);  
    $telefono = trim($_POST["telefono"]);  
    $id_barbero = $_POST["id_barbero"];  
    $id_servicio = $_POST["id_servicio"];  
    $fecha = $_POST["fecha"];  
    $hora = $_POST["hora"];  

    $mensaje = ""; // Variable para almacenar el mensaje  
    $error = false; // Variable para almacenar si hay error   

    // Verificar disponibilidad del nuevo horario  
    try {  
        // Preparar la consulta para verificar si el horario está ocupado  
        $check = $conn->prepare("SELECT id FROM reservas   
                                  WHERE id_barbero = ?   
                                  AND fecha = ?   
                                  AND hora = ?");  
        $check->bind_param("iss", $id_barbero, $fecha, $hora);  
        $check->execute();  
        $check->store_result();  

        // Si hay resultados, significa que el horario ya está ocupado  
        if ($check->num_rows > 0) {  
            throw new Exception("El horario ya está ocupado para este barbero.");  
        }  

        // Si el horario está disponible, insertar la reserva  
        $sql = "INSERT INTO reservas (id_barbero, nombre_cliente, telefono_cliente, fecha, hora, estado)   
                VALUES (?, ?, ?, ?, ?, 'pendiente')";  
        $insert = $conn->prepare($sql);  
        $insert->bind_param("issss", $id_barbero, $nombre, $telefono, $fecha, $hora);  

        if ($insert->execute()) {  
            $mensaje = "¡Reserva guardada con éxito!";  
            // Redirigir a la página de reservas después de un breve retraso  
            header("refresh:2; url=reserva.php");  
        } else {  
            throw new Exception("Error al guardar la reserva: " . $conn->error);  
        }   

    } catch (Exception $e) {  
        // Manejo de errores  
        $error = true;  
        $mensaje = $e->getMessage();  
    } finally {  
        // Cerrar las declaraciones y la conexión  
        if (isset($check)) $check->close();  
        if (isset($insert)) $insert->close();  
        $conn->close();  
    }  
}  
?>  

<!DOCTYPE html>  
<html lang="es">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Resultados de la Reserva</title>  
    <style>  
        body {  
            font-family: Arial, sans-serif;  
            display: flex;  
            justify-content: center;  
            align-items: center;  
            height: 100vh;  
            margin: 0;  
            background-color: #f4f4f4;  
        }  
        .mensaje {  
            padding: 20px;  
            border-radius: 5px;  
            background-color: <?php echo $error ? '#ffcccc' : '#ccffcc'; ?>;  
            color: <?php echo $error ? '#d8000c' : '#005e00'; ?>;  
            border: 1px solid <?php echo $error ? '#d8000c' : '#005e00'; ?>;  
            text-align: center;  
            width: 300px;  
        }  
    </style>  
</head>  
<body>  

<div class="mensaje">  
    <?php   
    if (isset($mensaje) && !empty($mensaje)) {  
        echo htmlspecialchars($mensaje);  
    } else {  
        echo "Error en la reserva.";  
    }  
    ?>  
</div>  

</body>  
</html>