<?php
session_start();
include "../public/includes/db.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

// Obtener todos los barberos
$sql_barberos = "SELECT id, nombre FROM barberos";
$result_barberos = $conn->query($sql_barberos);
$barberos = [];
if ($result_barberos) {
    while ($row = $result_barberos->fetch_assoc()) {
        $barberos[] = $row;
    }
}

// Obtener reservas según barbero seleccionado
$selected_barbero = isset($_GET['barbero']) ? intval($_GET['barbero']) : 0;

$sql = "SELECT r.id, r.nombre_cliente, r.telefono_cliente, r.fecha, r.hora, r.estado, b.nombre AS barbero
        FROM reservas r
        JOIN barberos b ON r.id_barbero = b.id
        WHERE (? = 0 OR r.id_barbero = ?)
        ORDER BY r.fecha, r.hora";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $selected_barbero, $selected_barbero);
$stmt->execute();
$result = $stmt->get_result();

$reservas = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $reservas[] = [
            "title" => "{$row['nombre_cliente']} - {$row['barbero']}",
            "start" => "{$row['fecha']}T{$row['hora']}",
            "extendedProps" => [
                "telefono" => $row['telefono_cliente'],
                "estado" => $row['estado'],
                "id" => $row['id']
            ]
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calendario de Reservas</title>
    
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../public/assets/css/calendar.css">
</head>
<body>

    <div class="container">
        <h2>Calendario de Reservas</h2>
        
        <!-- Menú desplegable mejorado -->
    <div class="dropdown mb-4">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown">
            Menú Principal
        </button>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="dashboard.php">Panel de Control</a>
            <a class="dropdown-item" href="reporte_clientes.php">Reporte de Clientes</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="logout.php">Cerrar Sesión</a>
        </div>
    </div>

        <!-- Selector de Barbero -->
        <div class="form-group">
            <select id="selectBarbero" class="form-control">
                <option value="0">Todos los Barberos</option>
                <?php foreach ($barberos as $barbero): ?>
                    <option value="<?= $barbero['id'] ?>" <?= $selected_barbero == $barbero['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($barbero['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success" role="alert">Operación realizada con éxito.</div>
        <?php endif; ?>

        <div id="calendar"></div>
    </div>

    <!-- Modal único para acciones -->
    <div class="modal fade" id="modalAcciones" tabindex="-1" role="dialog" aria-labelledby="modalAccionesLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAccionesLabel">Gestión de Reserva</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="info-reserva">
                        <p id="modalTexto"></p>
                    </div>
                    
                    <div class="acciones-divider">
                        <h6>Reagendar Reserva:</h6>
                        <div class="form-group">
                            <label>Nueva Fecha:</label>
                            <input type="date" id="nuevaFecha" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nueva Hora:</label>
                            <input type="time" id="nuevaHora" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <div class="btn-action-group">
                        <button type="button" class="btn btn-danger" onclick="confirmarEliminar()">Eliminar</button>
                        <button type="button" class="btn btn-primary" onclick="confirmarReagendar()">Reagendar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        var reservaSeleccionada = null;
        var calendar = null;

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: <?php echo json_encode($reservas, JSON_UNESCAPED_UNICODE); ?>,
                eventContent: function(arg) {
                    return {
                        html: `<b>${arg.event.title}</b><br>
                               <small>Tel: ${arg.event.extendedProps.telefono}</small><br>
                               <small>Estado: ${arg.event.extendedProps.estado}</small>`
                    };
                },
                dateClick: function(info) {
                    calendar.changeView('timeGridDay', info.dateStr);
                },
                eventClick: function(info) {
                    reservaSeleccionada = info.event.extendedProps.id;
                    const fechaHora = info.event.start;
                    
                    // Actualizar contenido del modal
                    document.getElementById("modalTexto").innerHTML = 
                        `<strong>Cliente:</strong> ${info.event.title.split(' - ')[0]}<br>
                         <strong>Barbero:</strong> ${info.event.title.split(' - ')[1]}<br>
                         <strong>Fecha original:</strong> ${fechaHora.toLocaleDateString('es-ES')}<br>
                         <strong>Hora original:</strong> ${fechaHora.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})}`;

                    // Establecer valores por defecto
                    document.getElementById('nuevaFecha').value = fechaHora.toISOString().split('T')[0];
                    document.getElementById('nuevaHora').value = 
                        fechaHora.toTimeString().split(' ')[0].substring(0, 5);
                        
                    // Mostrar modal
                    $('#modalAcciones').modal('show');
                }
            });

            calendar.render();

            document.getElementById('selectBarbero').addEventListener('change', function() {
                window.location.href = '?barbero=' + this.value;
            });
        });

        function confirmarEliminar() {
            if (!reservaSeleccionada) return;

            if (!confirm('¿Estás seguro de querer eliminar esta reserva?')) return;

            fetch('eliminar_reserva.php?id=' + reservaSeleccionada)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarFeedback('Reserva eliminada correctamente');
                } else {
                    mostrarFeedback(data.error || 'Error al eliminar', 'error');
                }
            })
            .catch(error => {
                console.error("Error:", error);
                mostrarFeedback('Error de conexión', 'error');
            });
        }

        function confirmarReagendar() {
    const nuevaFecha = document.getElementById('nuevaFecha').value;
    const nuevaHora = document.getElementById('nuevaHora').value;
    
    if (!nuevaFecha || !nuevaHora) {
        mostrarFeedback('Selecciona fecha y hora válidas', 'error');
        return;
    }

    fetch('actualizar_reserva.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: reservaSeleccionada,
            fecha: nuevaFecha,
            hora: nuevaHora
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { 
                throw new Error(err.error || `Error HTTP: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            mostrarFeedback('¡Reagendado exitoso!');
        } else {
            mostrarFeedback(data.error || 'Error desconocido', 'error');
        }
    })
    .catch(error => {
        console.error('Error detallado:', error);
        mostrarFeedback(error.message || 'Error de comunicación', 'error');
    });
}

        function mostrarFeedback(mensaje, tipo = 'success') {
            alert(mensaje);
            if (tipo === 'success') {
                $('#modalAcciones').modal('hide');
                location.reload();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha');
    const horaInput = document.getElementById('hora');
    const barberoSelect = document.getElementById('id_barbero');

    async function checkDisponibilidad() {
        const response = await fetch('check_disponibilidad.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_barbero: barberoSelect.value,
                fecha: fechaInput.value,
                hora: horaInput.value
            })
        });
        
        return response.json();
    }

    // Validar al enviar el formulario
    document.querySelector('form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const disponibilidad = await checkDisponibilidad();
        if (!disponibilidad.disponible) {
            alert(disponibilidad.mensaje);
            return;
        }
        
        e.target.submit();
    });
});
 
    </script>
</body>
</html>