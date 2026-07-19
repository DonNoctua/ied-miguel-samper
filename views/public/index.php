<?php
/**
 * VISTA: PÁGINA DE INICIO
 * IED Miguel Samper Agudelo
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - IED Miguel Samper</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">IED Miguel Samper</div>
            <ul class="navbar-nav">
                <li><a href="<?= BASE_URL ?>inicio" class="active">Inicio</a></li>
                <li><a href="<?= BASE_URL ?>nosotros">Nosotros</a></li>
                <li><a href="<?= BASE_URL ?>programas">Programas</a></li>
                <li><a href="<?= BASE_URL ?>noticias">Noticias</a></li>
                <li><a href="<?= BASE_URL ?>galeria">Galería</a></li>
                <li><a href="<?= BASE_URL ?>contacto">Contacto</a></li>
                <li><a href="<?= BASE_URL ?>login" class="btn-login">Acceder</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Bienvenido a IED Miguel Samper</h1>
            <p>Formamos líderes para un futuro mejor</p>
            <div style="margin-top: 2rem;">
                <a href="<?= BASE_URL ?>matriculas-info" class="btn btn-tertiary" style="background: white; color: #DC3545;">Infórmate sobre Matrículas</a>
            </div>
        </div>
    </section>

    <!-- Noticias Destacadas -->
    <?php if (isset($noticias_destacadas) && count($noticias_destacadas) > 0): ?>
    <section style="padding: 4rem 0;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 3rem;">Noticias Destacadas</h2>
            <div class="row col-3">
                <?php foreach ($noticias_destacadas as $noticia): ?>
                    <div class="card">
                        <?php if ($noticia['imagen']): ?>
                            <img src="<?= UPLOADS_URL . htmlspecialchars($noticia['imagen']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>" style="width: 100%; height: 200px; object-fit: cover; margin: -1.5rem -1.5rem 1rem -1.5rem; border-radius: 8px 8px 0 0;">
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($noticia['titulo']) ?></h3>
                        <p><?= htmlspecialchars($noticia['descripcion']) ?></p>
                        <a href="<?= BASE_URL ?>noticias/<?= $noticia['id'] ?>" class="btn btn-primary btn-sm">Leer Más</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Galería -->
    <?php if (isset($galeria_reciente) && count($galeria_reciente) > 0): ?>
    <section style="padding: 4rem 0; background: #f8f9fa;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 3rem;">Galería de Imágenes</h2>
            <div class="row col-3">
                <?php foreach ($galeria_reciente as $imagen): ?>
                    <div style="cursor: pointer; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <img src="<?= UPLOADS_URL . htmlspecialchars($imagen['imagen']) ?>" alt="<?= htmlspecialchars($imagen['nombre']) ?>" style="width: 100%; height: 250px; object-fit: cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 2rem;">
                <a href="<?= BASE_URL ?>galeria" class="btn btn-secondary">Ver Todas las Imágenes</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Sección de Información -->
    <section style="padding: 4rem 0;">
        <div class="container">
            <div class="row col-2">
                <div class="card">
                    <h3 style="color: #DC3545;">Nuestra Misión</h3>
                    <p>Formar estudiantes competentes, éticos y responsables que contribuyan al desarrollo de la sociedad.</p>
                </div>
                <div class="card">
                    <h3 style="color: #28A745;">Nuestra Visión</h3>
                    <p>Ser una institución educativa líder en excelencia académica y formación integral.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Institución</h4>
                    <ul>
                        <li><a href="<?= BASE_URL ?>nosotros">Nosotros</a></li>
                        <li><a href="<?= BASE_URL ?>historia">Historia</a></li>
                        <li><a href="<?= BASE_URL ?>mision">Misión</a></li>
                        <li><a href="<?= BASE_URL ?>vision">Visión</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Académico</h4>
                    <ul>
                        <li><a href="<?= BASE_URL ?>programas">Programas</a></li>
                        <li><a href="<?= BASE_URL ?>matriculas-info">Matrículas</a></li>
                        <li><a href="<?= BASE_URL ?>noticias">Noticias</a></li>
                        <li><a href="<?= BASE_URL ?>galeria">Galería</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contacto</h4>
                    <ul>
                        <li>Teléfono: <?= TELEFONO_INSTITUCION ?></li>
                        <li>Email: <?= EMAIL_INSTITUCION ?></li>
                        <li>Dirección: <?= DIRECCION_INSTITUCION ?></li>
                        <li>NIT: <?= NIT_INSTITUCION ?></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 <?= NOMBRE_INSTITUCION ?>. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>
