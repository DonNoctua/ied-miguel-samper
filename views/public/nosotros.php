<?php
/**
 * VISTA: NOSOTROS
 * IED Miguel Samper Agudelo
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros - IED Miguel Samper</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">IED Miguel Samper</div>
            <ul class="navbar-nav">
                <li><a href="<?= BASE_URL ?>inicio">Inicio</a></li>
                <li><a href="<?= BASE_URL ?>nosotros" class="active">Nosotros</a></li>
                <li><a href="<?= BASE_URL ?>programas">Programas</a></li>
                <li><a href="<?= BASE_URL ?>noticias">Noticias</a></li>
                <li><a href="<?= BASE_URL ?>login" class="btn-login">Acceder</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Conozca Nuestra Institución</h1>
            <p>Una trayectoria de excelencia educativa</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section style="padding: 4rem 0;">
        <div class="container">
            <div class="row col-2" style="gap: 3rem; align-items: center;">
                <div>
                    <h2>Nuestra Historia</h2>
                    <p>La Institución Educativa Departamental Miguel Samper Agudelo es una institución educativa pública con más de 50 años de experiencia en la formación integral de jóvenes.</p>
                    <p>Durante décadas hemos dedicado nuestros esfuerzos a proporcionar educación de calidad que prepare a nuestros estudiantes para enfrentar los retos del mundo moderno.</p>
                </div>
                <div style="background: linear-gradient(135deg, #DC3545, #28A745); height: 300px; border-radius: 8px;"></div>
            </div>
        </div>
    </section>

    <!-- Valores -->
    <section style="padding: 4rem 0; background: #f8f9fa;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 3rem;">Nuestros Valores</h2>
            <div class="row col-3">
                <div class="card">
                    <h3 style="color: #DC3545;">Excelencia</h3>
                    <p>Buscamos la excelencia académica en todos nuestros procesos educativos.</p>
                </div>
                <div class="card">
                    <h3 style="color: #28A745;">Integridad</h3>
                    <p>Actuamos con honestidad y transparencia en todas nuestras acciones.</p>
                </div>
                <div class="card">
                    <h3 style="color: #17a2b8;">Compromiso</h3>
                    <p>Estamos comprometidos con el desarrollo integral de nuestros estudiantes.</p>
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
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contacto</h4>
                    <ul>
                        <li>Teléfono: <?= TELEFONO_INSTITUCION ?></li>
                        <li>Email: <?= EMAIL_INSTITUCION ?></li>
                        <li>Dirección: <?= DIRECCION_INSTITUCION ?></li>
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
