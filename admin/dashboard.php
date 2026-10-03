<?php
session_start();
include "../public/includes/db.php";

// Verificar si el usuario está autenticado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

// Consultas independientes para cada sección
$barberos = $conn->query("SELECT * FROM barberos");
$servicios_para_tabla = $conn->query("SELECT * FROM servicio");
$servicios_para_select = $conn->query("SELECT * FROM servicio"); // Consulta separada para el dropdown
$mensajes = $conn->query("SELECT * FROM mensajes ORDER BY fecha DESC");

// Verificar errores en consultas
if (!$barberos || !$servicios_para_tabla || !$servicios_para_select || !$mensajes) {
    die("Error en consulta: " . $conn->error);
}

// Eliminar barbero
if (isset($_POST["eliminar_barbero"])) {
    $id_barbero = $_POST["eliminar_barbero"];
    $stmt = $conn->prepare("DELETE FROM barberos WHERE id = ?");
    $stmt->bind_param("i", $id_barbero);
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar barbero: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Eliminar mensaje
if (isset($_POST["eliminar_mensaje"])) {
    $id_mensaje = $_POST["eliminar_mensaje"];
    $stmt = $conn->prepare("DELETE FROM mensajes WHERE id = ?");
    $stmt->bind_param("i", $id_mensaje);
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar mensaje: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Eliminar servicio
if (isset($_POST["eliminar_servicio"])) {
    $id_servicio = $_POST["eliminar_servicio"];
    $stmt = $conn->prepare("DELETE FROM servicio WHERE id = ?");
    $stmt->bind_param("i", $id_servicio);
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar servicio: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Nuevo: Lógica para manejar publicaciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Agregar nueva publicación
    if (isset($_POST['subir_publicacion'])) {
        $servicio_id = $_POST['servicio_id'];
        $titulo = $_POST['titulo'];
        $imagen = $_FILES['imagen']['name'];
        $imagen_tmp = $_FILES['imagen']['tmp_name'];
        $ruta_imagen = "../public/assets/images/publicaciones/" . basename($imagen);

        if (move_uploaded_file($imagen_tmp, $ruta_imagen)) {
            // Preparar la consulta SQL
            $stmt = $conn->prepare("INSERT INTO publicaciones_servicios (servicio_id, titulo, imagen) VALUES (?, ?, ?)");
            
            // Verificar si la preparación de la consulta fue exitosa
            if ($stmt === false) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }

            // Vincular los parámetros
            $stmt->bind_param("iss", $servicio_id, $titulo, $ruta_imagen);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                header("Location: dashboard.php");
                exit();
            } else {
                $error_publicacion = "Error al crear la publicación: " . $stmt->error;
            }

            // Cerrar la declaración
            $stmt->close();
        } else {
            $error_publicacion = "Error al subir la imagen";
        }
    }

    // Eliminar publicación
    if (isset($_POST['eliminar_publicacion'])) {
        $id_publicacion = $_POST['eliminar_publicacion'];
        $stmt = $conn->prepare("DELETE FROM publicaciones_servicios WHERE id = ?");
        $stmt->bind_param("i", $id_publicacion);
        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            $error_publicacion = "Error al eliminar la publicación: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Obtener publicaciones existentes
$publicaciones = $conn->query("
    SELECT ps.*, s.nombre as servicio_nombre 
    FROM publicaciones_servicios ps
    JOIN servicio s ON ps.servicio_id = s.id
    ORDER BY ps.fecha DESC
");
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .dashboard-container {
            max-width: 1200px; margin: 50px auto; padding: 30px;
            background: #fff; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-custom { width: 100%; margin-top: 10px; }
        .table-container { margin-top: 20px; }
        .btn-delete {
            background-color: red; color: white; border: none;
            padding: 5px 10px; cursor: pointer; border-radius: 5px;
        }
        .btn-delete:hover { background-color: darkred; }
        .btn-volver { margin-bottom: 20px; }
        .mensaje-completo { cursor: pointer; color: #007bff; }
        .mensaje-completo:hover { text-decoration: underline; }
        .modal-header { background-color: #007bff; color: white; }
        .modal-footer { border-top: none; }
    </style>
</head>
<body>
<div class="container">
    <div class="dashboard-container">
        <a href="../public/index.php" class="btn btn-secondary btn-volver"><i class="fas fa-home"></i> Visitar Página</a>

        <h2 class="mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION["nombre"]); ?> 👋</h2>
        <a href="reservas.php" class="btn btn-primary btn-custom"><i class="fas fa-calendar-alt"></i> Ver Reservas</a>
        <a href="#bandeja" class="btn btn-warning btn-custom"><i class="fas fa-envelope"></i> Ver Mensajes</a>
        <a href="logout.php" class="btn btn-danger btn-custom"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>

        <div class="row mt-4">
            <div class="col-md-6">
                <h4><i class="fas fa-user-plus"></i> Agregar Nuevo Barbero</h4>
                <form action="agregar_barbero.php" method="POST">
                    <input type="text" name="nombre_barbero" class="form-control mb-2" 
                           placeholder="Nombre del Barbero" required>
                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-plus"></i> Agregar Barbero</button>
                </form>
            </div>

            <div class="col-md-6">
                <h4><i class="fas fa-concierge-bell"></i> Agregar Nuevo Servicio</h4>
                <form action="agregar_servicio.php" method="POST" enctype="multipart/form-data">
                    <input type="text" name="nombre_servicio" class="form-control mb-2" 
                           placeholder="Nombre del Servicio" required>
                    <input type="number" name="precio_servicio" class="form-control mb-2" 
                           placeholder="Precio" step="0.01" required>
                    

                    

                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-plus"></i> Agregar Servicio</button>
                </form>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <h4><i class="fas fa-newspaper"></i> Gestión de Publicaciones</h4>
            
            <!-- Formulario para agregar publicaciones -->
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-4">
                        <select name="servicio_id" class="form-select" required>
                            <?php 
                            $servicios_para_select->data_seek(0); // Reiniciar el puntero del resultset
                            while($s = $servicios_para_select->fetch_assoc()): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="titulo" class="form-control" 
                               placeholder="Título (Ej: Mullet Moderno)" required>
                    </div>
                    <div class="col-md-4">
                        <input type="file" name="imagen" class="form-control" required>
                    </div>
                    
                    <div class="col-md-12 mt-2">
                        <button type="submit" name="subir_publicacion" class="btn btn-info w-100"><i class="fas fa-paper-plane"></i> Publicar Estilo</button>
                    </div>
                </div>
            </form>

            <!-- Listado de publicaciones existentes -->
            <div class="mt-4">
                <h5>Publicaciones Existentes</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th>Título</th>
                                <th>Imagen</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($pub = $publicaciones->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($pub['servicio_nombre']) ?></td>
                                <td><?= htmlspecialchars($pub['titulo']) ?></td>
                                <td>
                                    <img src="<?= htmlspecialchars($pub['imagen']) ?>" 
                                         class="img-thumbnail" 
                                         style="width: 100px; height: 100px; object-fit: cover;" 
                                         alt="<?= htmlspecialchars($pub['titulo']) ?>">
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($pub['fecha'])) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="eliminar_publicacion" value="<?= $pub['id'] ?>">
                                        <button type="submit" class="btn-delete" 
                                                onclick="return confirm('¿Eliminar esta publicación?')">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                    
                                </td>
                            </tr>

                            <!-- Modal para editar (pendiente de implementar lógica) -->
                            
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabla de Barberos -->
        <div class="table-container">
            <h4><i class="fas fa-users"></i> Lista de Barberos</h4>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $barberos->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row["nombre"]) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="eliminar_barbero" value="<?= $row["id"] ?>">
                                        <button type="submit" class="btn-delete" 
                                                onclick="return confirm('¿Seguro que deseas eliminar este barbero?');">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabla de Servicios -->
        <div class="table-container">
            <h4><i class="fas fa-tools"></i> Lista de Servicios</h4>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $servicios_para_tabla->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row["nombre"]) ?></td>
                                <td>$<?= number_format($row["precio"], 2) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="eliminar_servicio" value="<?= $row["id"] ?>">
                                        <button type="submit" class="btn-delete" 
                                                onclick="return confirm('¿Seguro que deseas eliminar este servicio?');">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bandeja de entrada -->
        <div class="table-container" id="bandeja">
            <h4><i class="fas fa-inbox"></i> 📩 Bandeja de Entrada</h4>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Fecha</th>
                            <th>Mensaje</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $mensajes->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row["nombre"]) ?></td>
                                <td><?= htmlspecialchars($row["email"]) ?></td>
                                <td><?= date("d/m/Y H:i", strtotime($row["fecha"])) ?></td>
                                <td>
                                    <span class="mensaje-completo" data-bs-toggle="modal" 
                                          data-bs-target="#mensajeModal<?= $row["id"] ?>">
                                        <?= htmlspecialchars($row["mensaje"]) ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="eliminar_mensaje" value="<?= $row["id"] ?>">
                                        <button type="submit" class="btn-delete" 
                                                onclick="return confirm('¿Seguro que deseas eliminar este mensaje?');">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div class="modal fade" id="mensajeModal<?= $row["id"] ?>" tabindex="-1" 
                                 aria-labelledby="mensajeModalLabel<?= $row["id"] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Mensaje de <?= htmlspecialchars($row["nombre"]) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" 
                                                    aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Correo:</strong> <?= htmlspecialchars($row["email"]) ?></p>
                                            <p><strong>Fecha:</strong> <?= date("d/m/Y H:i", strtotime($row["fecha"])) ?></p>                                              <?= date("d/m/Y H:i", strtotime($row["fecha"])) ?></p>  
                                            <p><strong>Mensaje:</strong></p>  
                                            <p><?= htmlspecialchars($row["mensaje"]) ?></p>  
                                        </div>  
                                        <div class="modal-footer">  
                                            <button type="button" class="btn btn-secondary"   
                                                    data-bs-dismiss="modal">Cerrar</button>  
                                        </div>  
                                    </div>  
                                </div>  
                            </div>  
                        <?php endwhile; ?>  
                    </tbody>  
                </table>  
            </div>  
        </div>  
    </div>  
</div>  

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>  
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>  
</body>  
</html>
