<?php
session_start();
include "../public/includes/db.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

// Obtener todos los clientes con su historial
$sql = "SELECT 
            nombre_cliente, 
            telefono_cliente, 
            COUNT(*) as total_reservas,
            MAX(fecha) as ultima_reserva
        FROM reservas
        GROUP BY nombre_cliente, telefono_cliente
        ORDER BY ultima_reserva DESC";

$result = $conn->query($sql);
$clientes = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .table-hover tbody tr:hover { background-color: #f8f9fa; }
        .badge { font-size: 0.9em; }
        .search-container { margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="my-4">Reporte de Clientes</h2>
    <a href="reservas.php" class="btn btn-secondary mb-3">← Volver</a>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Listado de Clientes</h5>
            <div class="search-container">
                <label for="searchInput" class="sr-only">Buscar cliente:</label>
                <input type="text" id="searchInput" name="searchInput" class="form-control" placeholder="Buscar cliente...">
            </div>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Total Citas</th>
                        <th>Última Cita</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= htmlspecialchars($cliente['nombre_cliente']) ?></td>
                        <td><?= htmlspecialchars($cliente['telefono_cliente']) ?></td>
                        <td>
                            <span class="badge badge-primary">
                                <?= $cliente['total_reservas'] ?> citas
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($cliente['ultima_reserva'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" 
                                    onclick="verHistorial('<?= $cliente['nombre_cliente'] ?>', '<?= $cliente['telefono_cliente'] ?>')">
                                Ver Historial
                            </button>
                            <button class="btn btn-sm btn-danger" 
                                    onclick="eliminarCliente('<?= $cliente['nombre_cliente'] ?>', '<?= $cliente['telefono_cliente'] ?>')">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Historial -->
<div class="modal fade" id="historialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historial de Citas</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="clienteNombre" class="sr-only">Cliente:</label>
                <h6 id="clienteNombre"></h6>
                <div class="form-group">
                    <label for="cambioEstadoGlobal">Cambiar estado global:</label>
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-control" id="cambioEstadoGlobal" name="cambioEstadoGlobal">
                            <option value="pendiente">Pendiente</option>
                            <option value="confirmada">Confirmada</option>
                            <option value="completada">Completada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                        <button class="btn btn-primary" onclick="aplicarCambioGlobal()">
                            Aplicar a todas
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Barbero</th>
                                <th>Estado Actual</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="historialBody">
                            <!-- Aquí se cargarán las citas -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir jQuery y Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
// Búsqueda en tiempo real
$('#searchInput').on('keyup', function() {
    const value = $(this).val().toLowerCase();
    $('tbody tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
});

// Función para ver historial
function verHistorial(nombre, telefono) {
    $('#clienteNombre').html(`<strong>${nombre}</strong> - ${telefono}`);

    $.ajax({
        url: 'obtener_historial.php',
        method: 'POST',
        data: { nombre: nombre, telefono: telefono },
        success: function(response) {
            try {
                const citas = JSON.parse(response);
                let html = '';

                citas.forEach(cita => {
                    html += `
                        <tr data-reserva-id="${cita.id}">
                            <td>${cita.fecha}</td>
                            <td>${cita.hora}</td>
                            <td>${cita.nombre_barbero}</td>
                            <td>
                                <span class="badge ${getBadgeClass(cita.estado)}">
                                    ${cita.estado}
                                </span>
                            </td>
                            <td>
                                <select class="form-control estado-select" data-reserva-id="${cita.id}">
                                    <option value="pendiente" ${cita.estado === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                    <option value="confirmada" ${cita.estado === 'confirmada' ? 'selected' : ''}>Confirmada</option>
                                    <option value="completada" ${cita.estado === 'completada' ? 'selected' : ''}>Completada</option>
                                    <option value="cancelada" ${cita.estado === 'cancelada' ? 'selected' : ''}>Cancelada</option>
                                </select>
                                <button class="btn btn-sm btn-primary" onclick="actualizarEstado(${cita.id})">
                                    Actualizar
                                </button>
                            </td>
                        </tr>`;
                });

                $('#historialBody').html(html);
                $('#historialModal').modal('show');
            } catch (e) {
                console.error('Error al parsear JSON:', e);
            }
        }
    });
}

// Función para eliminar cliente
function eliminarCliente(nombre, telefono) {
    if (!confirm(`¿Estás seguro de eliminar al cliente ${nombre}? Esta acción no se puede deshacer.`))
        return;

    $.ajax({
        url: 'eliminar_cliente.php',
        method: 'POST',
        data: { nombre: nombre, telefono: telefono },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Cliente eliminado correctamente');
                    location.reload();
                } else {
                    alert('Error al eliminar el cliente: ' + data.error);
                }
            } catch (e) {
                console.error('Error al procesar respuesta:', e);
                alert('Error inesperado');
            }
        },
        error: function() {
            alert('Error en la conexión');
        }
    });
}

// Función para actualizar el estado de una reserva
function actualizarEstado(id_reserva) {
    const nuevoEstado = $(`select.estado-select[data-reserva-id="${id_reserva}"]`).val();

    if (!id_reserva || !nuevoEstado) {
        console.error('Datos incompletos:', { id_reserva, nuevoEstado });
        mostrarNotificacion('Datos incompletos', 'danger');
        return;
    }

    $.ajax({
        url: 'actualizar_estado.php',
        method: 'POST',
        data: {
            id_reserva: id_reserva,
            estado: nuevoEstado
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Actualizar la fila en la tabla
                const $fila = $(`tr[data-reserva-id="${id_reserva}"]`);
                $fila.find('.badge')
                    .removeClass('badge-secondary badge-success badge-danger badge-primary')
                    .addClass(getBadgeClass(nuevoEstado))
                    .text(nuevoEstado);
                mostrarNotificacion('Estado actualizado', 'success');
            } else {
                mostrarNotificacion(response.error, 'danger');
            }
        },
        error: function(xhr) {
            console.error('Error en AJAX:', xhr.responseText);
            mostrarNotificacion('Error de conexión', 'danger');
        }
    });
}

// Función para aplicar cambio global de estado
function aplicarCambioGlobal() {
    const nuevoEstado = $('#cambioEstadoGlobal').val();
    const nombre = $('#clienteNombre').text().split(' - ')[0].replace('<strong>', '').replace('</strong>', '').trim();
    const telefono = $('#clienteNombre').text().split(' - ')[1].trim();

    if (!confirm(`¿Estás seguro de cambiar TODAS las citas a ${nuevoEstado.toUpperCase()}?`)) return;

    $.ajax({
        url: 'actualizar_estado_global.php',
        method: 'POST',
        data: {
            nombre: nombre,
            telefono: telefono,
            estado: nuevoEstado
        },
        success: function(response) {
            verHistorial(nombre, telefono); // Recargar vista
            mostrarNotificacion('Cambio global aplicado', 'success');
        }
    });
}

// Función de notificación
function mostrarNotificacion(mensaje, tipo = 'success') {
    const alerta = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
        ${mensaje}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>`;
    $('#historialModal .modal-body').prepend(alerta);
}

// Función para obtener la clase del badge según el estado
function getBadgeClass(estado) {
    switch (estado.toLowerCase()) {
        case 'confirmada': return 'badge-success';
        case 'cancelada': return 'badge-danger';
        case 'completada': return 'badge-primary';
        default: return 'badge-secondary';
    }
}
</script>

</body>
</html>