<?php
session_start();
include "../public/includes/db.php";

if (!isset($_SESSION["usuario_id"])) {
    die(json_encode(['success' => false, 'error' => 'Acceso no autorizado']));
}

$id_reserva = $_POST['id_reserva'] ?? null;
$estado = $_POST['estado'] ?? null;

if (!$id_reserva || !$estado) {
    die(json_encode(['success' => false, 'error' => 'Datos incompletos']));
}

// Validar el estado
$estadosPermitidos = ['pendiente', 'confirmada', 'completada', 'cancelada'];
if (!in_array(strtolower($estado), $estadosPermitidos)) {
    die(json_encode(['success' => false, 'error' => 'Estado no válido']));
}

// Actualizar el estado en la base de datos
$sql = "UPDATE reservas SET estado = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $estado, $id_reserva);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}