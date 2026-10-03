<?php
session_start();
include "../public/includes/db.php";

// Verificar autenticación
if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403);
    die(json_encode(["success" => false, "error" => "Debes iniciar sesión"]));
}

// Validar ID de reserva
$id_reserva = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_reserva || $id_reserva <= 0) {
    http_response_code(400);
    die(json_encode(["success" => false, "error" => "ID de reserva inválido"]));
}

try {
    // Verificar existencia y pertenencia de la reserva
    $stmt = $conn->prepare("SELECT id_barbero FROM reservas WHERE id = ?");
    $stmt->bind_param("i", $id_reserva);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        die(json_encode(["success" => false, "error" => "Reserva no encontrada"]));
    }

    $reserva = $result->fetch_assoc();
    
    // Verificar si el usuario actual es el barbero de la reserva
    if (isset($_SESSION["id_barbero"]) && $_SESSION["id_barbero"] != $reserva['id_barbero']) {
        http_response_code(403);
        die(json_encode(["success" => false, "error" => "No tienes permiso para esta acción"]));
    }

    // Eliminar reserva
    $stmt_delete = $conn->prepare("DELETE FROM reservas WHERE id = ?");
    $stmt_delete->bind_param("i", $id_reserva);
    
    if ($stmt_delete->execute()) {
        echo json_encode(["success" => true]);
    } else {
        throw new Exception("Error al eliminar: " . $conn->error);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}