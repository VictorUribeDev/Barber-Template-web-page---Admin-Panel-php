<?php
session_start();
include "../public/includes/db.php";

if (!isset($_SESSION["usuario_id"])) {
    die(json_encode(['success' => false, 'error' => 'Acceso no autorizado']));
}

$nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
$telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
$estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

$sql = "UPDATE reservas SET estado = ?
        WHERE nombre_cliente = ? AND telefono_cliente = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $estado, $nombre, $telefono);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();