<?php  
session_start();  
header('Content-Type: application/json');  
include "../public/includes/db.php";  

// Verifica que la sesión está activa, de lo contrario, devuelve un error  
if (!isset($_SESSION["usuario_id"])) {  
    echo json_encode(['success' => false, 'error' => 'No autenticado']);  
    exit();  
}  

// Recolección de datos  
$data = json_decode(file_get_contents("php://input"), true);  
$id_reserva = isset($data['id']) ? intval($data['id']) : 0;  
$nueva_fecha = isset($data['fecha']) ? $data['fecha'] : '';  
$nueva_hora = isset($data['hora']) ? $data['hora'] : '';  

try {  
    // Validar los datos recibidos  
    if ($id_reserva <= 0 || empty($nueva_fecha) || empty($nueva_hora)) {  
        throw new Exception("Datos inválidos proporcionados");  
    }  

    // Verificar disponibilidad del nuevo horario  
    $check = $conn->prepare("SELECT id FROM reservas   
                              WHERE id_barbero = (SELECT id_barbero FROM reservas WHERE id = ?)  
                              AND fecha = ?   
                              AND hora = ?   
                              AND id != ?");  
    $check->bind_param("issi", $id_reserva, $nueva_fecha, $nueva_hora, $id_reserva);  
    $check->execute();  
    $check->store_result();  

    if ($check->num_rows > 0) {  
        throw new Exception("El nuevo horario ya está ocupado para este barbero", 409);  
    }  

    // Actualizar la reserva en la base de datos  
    $update = $conn->prepare("UPDATE reservas SET fecha = ?, hora = ? WHERE id = ?");  
    $update->bind_param("ssi", $nueva_fecha, $nueva_hora, $id_reserva);  
    
    if ($update->execute()) {  
        echo json_encode(['success' => true]);  
    } else {  
        throw new Exception("Error al actualizar la reserva", 500);  
    }  

} catch (Exception $e) {  
    // Manejo de errores y respuesta adecuada  
    http_response_code($e->getCode() ?: 500); // Establecer el código de respuesta HTTP  
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);  
}  
?>