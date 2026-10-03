<?php include 'includes/header.php'; ?>

<!-- Carrusel -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="assets/images/barberia1.jpg" class="d-block w-100" alt="Barbería Premium">
        </div>
        <div class="carousel-item">
            <img src="assets/images/barberia2.jpg" class="d-block w-100" alt="Barbería Premium">
        </div>
        <div class="carousel-item">
            <img src="assets/images/barberia3.jpg" class="d-block w-100" alt="Barbería Premium">
        </div>
    </div>

    <!-- Texto sobre el carrusel -->
    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center h-100">
        <br><br><br><br><br>
        <a href="menu.php" class="btn btn-primary btn-lg mt-3">Menú</a>
    </div>

    <!-- Controles del carrusel -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<?php include 'includes/footer.php'; ?>
