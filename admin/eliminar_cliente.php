<?php
session_start();
include "../public/includes/db.php";

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["success" => false, "error" => "No autorizado"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $conn->real_escape_string($_POST["nombre"]);
    $telefono = $conn->real_escape_string($_POST["telefono"]);

    // Eliminar las reservas del cliente antes de eliminarlo
    $sql_delete_reservas = "DELETE FROM reservas WHERE nombre_cliente='$nombre' AND telefono_cliente='$telefono'";
    if ($conn->query($sql_delete_reservas) === TRUE) {
        // Luego, eliminar el cliente
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Error al eliminar cliente"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Método no permitido"]);
}
?>
 