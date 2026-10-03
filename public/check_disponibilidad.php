<?php
include "includes/db.php";

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $conn->prepare("SELECT id FROM reservas 
                       WHERE id_barbero = ? 
                       AND fecha = ? 
                       AND hora = ?");
$stmt->bind_param("iss", $data['id_barbero'], $data['fecha'], $data['hora']);
$stmt->execute();
$stmt->store_result();

echo json_encode([
    'disponible' => $stmt->num_rows === 0,
    'mensaje' => $stmt->num_rows > 0 
        ? 'Horario no disponible' 
        : 'Horario disponible'
]);
?>