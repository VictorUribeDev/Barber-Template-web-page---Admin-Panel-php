<?php  
session_start();  
include "../public/includes/db.php";  

if (!isset($_SESSION["usuario_id"])) {  
    die("Acceso no autorizado");  
}  

$nombre = $_POST['nombre'] ?? '';  
$telefono = $_POST['telefono'] ?? '';  

// Modificamos la consulta SQL para incluir el nombre del barbero
$sql = "SELECT   
            r.id,  
            r.fecha,   
            r.hora,   
            b.nombre as nombre_barbero,   
            r.estado  
        FROM reservas r  
        JOIN barberos b ON r.id_barbero = b.id  
        WHERE r.nombre_cliente = ? AND r.telefono_cliente = ?  
        ORDER BY r.fecha DESC, r.hora DESC";  

$stmt = $conn->prepare($sql);  
$stmt->bind_param("ss", $nombre, $telefono);  
$stmt->execute();  
$result = $stmt->get_result();  

$citas = [];  
while ($row = $result->fetch_assoc()) {  
    $row['fecha'] = date('d/m/Y', strtotime($row['fecha']));  
    $citas[] = $row;  
}  

echo json_encode($citas);