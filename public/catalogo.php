<?php   
include 'includes/header.php';  
include "../public/includes/db.php";  

$servicios = $conn->query("  
    SELECT s.id, s.nombre, s.precio,  
           GROUP_CONCAT(ps.titulo SEPARATOR '|') AS estilos,  
           GROUP_CONCAT(ps.imagen SEPARATOR '|') AS imagenes   
    FROM servicio s  
    LEFT JOIN publicaciones_servicios ps ON s.id = ps.servicio_id  
    GROUP BY s.id  
");  

if (!$servicios) {  
    die("Error en consulta: " . $conn->error);  
}  
?>  

<div class="container my-5">  
    <h2 class="text-center mb-4" data-aos="fade-down">Catálogo de Servicios</h2>  

    <div class="row">  
        <?php while($servicio = $servicios->fetch_assoc()): ?>  
        <div class="col-md-6 mb-4" data-aos="fade-up">  
            <div class="d-flex align-items-center bg-light p-3 rounded shadow-sm">  
                <div>  
                    <h5 class="mb-1"><?= htmlspecialchars($servicio['nombre']) ?></h5>  
                    <p class="h5 text-primary mb-0">$<?= number_format($servicio['precio'], 0) ?></p>  
                    
                    <?php if(!empty($servicio['estilos'])): ?>  
                    <div class="mt-2">  
                        <small class="text-muted">Estilos disponibles:</small>  
                        <?php foreach(explode('|', $servicio['estilos']) as $estilo): ?>  
                            <span class="badge bg-secondary me-1"><?= htmlspecialchars($estilo) ?></span>  
                        <?php endforeach; ?>  
                    </div>  
                    <?php endif; ?>  
                </div>  
            </div>  
            
            <?php if(!empty($servicio['imagenes'])): ?>  
            <div class="row mt-3 g-2">  
                <?php foreach(explode('|', $servicio['imagenes']) as $imagen): ?>  
                <div class="col-4">  
                    <img src="<?= htmlspecialchars($imagen) ?>"   
                         class="img-fluid rounded gallery-image"  
                         alt="Estilo <?= htmlspecialchars($servicio['nombre']) ?>">  
                </div>  
                <?php endforeach; ?>  
            </div>  
            <?php else: ?>  
            <div class="mt-2 text-center text-muted">No hay imágenes disponibles.</div>  
            <?php endif; ?>  
        </div>  
        <?php endwhile; ?>  
    </div>  

    <!-- Lista de Precios -->  
    <div class="mt-5">  
        <h3 class="text-center">Lista de Precios</h3>  
        <div class="table-responsive">  
            <table class="table table-bordered">  
                <thead class="table-dark">  
                    <tr>  
                        <th>Servicio</th>  
                        <th>Precio</th>  
                    </tr>  
                </thead>  
                <tbody>  
                    <?php   
                    $servicios_tabla = $conn->query("SELECT nombre, precio FROM servicio ORDER BY precio ASC");  
                    while($servicio = $servicios_tabla->fetch_assoc()):   
                    ?>  
                    <tr>  
                        <td><?= htmlspecialchars($servicio['nombre']) ?></td>  
                        <td>$<?= number_format($servicio['precio'], 0) ?></td>   
                    </tr>  
                    <?php endwhile; ?>  
                </tbody>  
            </table>  
        </div>  
    </div>  
</div>  

<style>  
.gallery-image {
    width: 100%;         /* Se ajusta al ancho del contenedor */
    height: 150px;       /* Alto fijo para todas las imágenes */
    object-fit: cover;   /* Recorta la imagen para llenar el contenedor */
    object-position: center;
    transition: transform 0.3s;
}


.gallery-image:hover {  
    transform: scale(1.05);  
    cursor: pointer;  
}  

.table th {  
    background-color: #2c3e50 !important;  
}  

.badge {  
    font-size: 0.85em;  
    padding: 0.5em 0.75em;  
}  
</style>  

<?php include 'includes/footer.php'; ?>