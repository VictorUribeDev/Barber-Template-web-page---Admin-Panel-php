<?php include 'includes/header.php'; ?>

<section id="contacto" class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <!-- Mapa -->
            <div class="col-md-6 mb-4" data-aos="fade-right">
                <h2 class="text-center mb-4">Ubicación</h2>
                <div class="ratio ratio-16x9 shadow-lg rounded">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.115532836328!2d-122.41941548468128!3d37.7749297797599!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085808a5f5c7c61%3A0x5d730fe7a62d5274!2sSan%20Francisco%2C%20California!5e0!3m2!1ses!2sus!4v1675661221243!5m2!1ses!2sus" 
                        width="100%" height="300" style="border:0;" 
                        allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>

            <!-- Formulario de Contacto -->
            <div class="col-md-6" data-aos="fade-left">
                <h2 class="text-center mb-4">Contáctanos</h2>
                <form action="../admin/enviar.php" method="POST">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="mensaje" class="form-label">Mensaje</label>
                        <textarea class="form-control" id="mensaje" name="mensaje" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 py-2 mt-3">Enviar Mensaje</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Botón flotante de WhatsApp -->
<a href="https://wa.me/56912345678" target="_blank" class="whatsapp-button" data-aos="zoom-in">
    <img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp" class="rounded-circle shadow-lg">
</a>

<?php include 'includes/footer.php'; ?>
