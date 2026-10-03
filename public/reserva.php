<?php 
include "includes/header.php"; 
include "includes/db.php"; // Asegura la conexión con la BD

if (!isset($conn)) {
    die("Error de conexión a la base de datos.");
}
?>

<div class="container my-5 reserva-container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6" data-aos="fade-up">
            <!-- Tarjeta del formulario -->
            <div class="card shadow-lg border-0 reserva-card">
                <div class="card-header reserva-header text-center">
                    <h2 class="mb-0 reserva-title">Reserva tu Hora</h2>
                    <p class="mb-0 reserva-subtitle">¡Agenda tu cita en pocos pasos!</p>
                </div>
                <div class="card-body p-4 p-md-5 reserva-body">
                    <form action="guardar_reserva.php" method="POST">
                        <!-- Nombre -->
                        <div class="mb-4">
                            <label for="nombre" class="form-label reserva-label">Nombre:</label>
                            <input type="text" class="form-control reserva-input" id="nombre" name="nombre" required>
                        </div>

                        <!-- Teléfono -->
                        <div class="mb-4">
                            <label for="telefono" class="form-label reserva-label">Teléfono:</label>
                            <input type="tel" class="form-control reserva-input" id="telefono" name="telefono" required pattern="[0-9]{9,12}">
                        </div>

                        <!-- Selección de Barbero -->
                        <div class="mb-4">
                            <label for="id_barbero" class="form-label reserva-label">Selecciona un Barbero:</label>
                            <select class="form-select reserva-select" id="id_barbero" name="id_barbero" required>
                                <option value="">Selecciona un barbero</option>
                                <?php
                                $barberos = $conn->query("SELECT id, nombre FROM barberos");
                                if ($barberos && $barberos->num_rows > 0) {
                                    while ($barbero = $barberos->fetch_assoc()) {
                                        echo "<option value='{$barbero['id']}'>{$barbero['nombre']}</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>No hay barberos disponibles</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Selección de Servicio -->
                        <div class="mb-4">
                            <label for="id_servicio" class="form-label reserva-label">Selecciona un Servicio:</label>
                            <select class="form-select reserva-select" id="id_servicio" name="id_servicio" required>
                                <option value="">Selecciona un servicio</option>
                                <?php
                                $servicio = $conn->query("SELECT id, nombre, precio FROM servicio");
                                if ($servicio && $servicio->num_rows > 0) {
                                    while ($servicios = $servicio->fetch_assoc()) {
                                        echo "<option value='{$servicios['id']}'>{$servicios['nombre']} - \${$servicios['precio']}</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>No hay servicios disponibles</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Fecha -->
                        <div class="mb-4">
                            <label for="fecha" class="form-label reserva-label">Fecha:</label>
                            <input type="date" class="form-control reserva-input" id="fecha" name="fecha" required>
                        </div>

                        <!-- Hora -->
                        <div class="mb-4">
                            <label for="hora" class="form-label reserva-label">Hora:</label>
                            <input type="time" class="form-control reserva-input" id="hora" name="hora" required>
                        </div>

                        <!-- Botón de Reserva -->
                        <div class="d-grid">
                            <button type="submit" class="btn reserva-button shadow-lg">Reservar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
