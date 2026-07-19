<?php
/**
 * VISTA: CONTACTO
 * IED Miguel Samper Agudelo
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - IED Miguel Samper</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">IED Miguel Samper</div>
            <ul class="navbar-nav">
                <li><a href="<?= BASE_URL ?>inicio">Inicio</a></li>
                <li><a href="<?= BASE_URL ?>contacto" class="active">Contacto</a></li>
                <li><a href="<?= BASE_URL ?>login" class="btn-login">Acceder</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Contacto</h1>
            <p>Nos encanta escucharte</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section style="padding: 4rem 0;">
        <div class="container">
            <div class="row col-2" style="gap: 3rem;">
                <!-- Formulario -->
                <div>
                    <h2>Envínos un Mensaje</h2>
                    
                    <?php if ($success = getFlash('success')): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <?php if ($error = getFlash('error')): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>contacto">
                        <input type="hidden" name="<?= CSRF_TOKEN_FIELD ?>" value="<?= generateCSRFToken() ?>">

                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono (Opcional)</label>
                            <input type="tel" id="telefono" name="telefono">
                        </div>

                        <div class="form-group">
                            <label for="asunto">Asunto</label>
                            <input type="text" id="asunto" name="asunto" required>
                        </div>

                        <div class="form-group">
                            <label for="mensaje">Mensaje</label>
                            <textarea id="mensaje" name="mensaje" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
                    </form>
                </div>

                <!-- Información de Contacto -->
                <div>
                    <h2>Información de Contacto</h2>
                    <div class="card" style="margin-top: 2rem;">
                        <div style="margin-bottom: 1.5rem;">
                            <h4>Dirección</h4>
                            <p><?= DIRECCION_INSTITUCION ?></p>
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <h4>Teléfono</h4>
                            <p><?= TELEFONO_INSTITUCION ?></p>
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <h4>Email</h4>
                            <p><a href="mailto:<?= EMAIL_INSTITUCION ?>"><?= EMAIL_INSTITUCION ?></a></p>
                        </div>
                        <div>
                            <h4>NIT</h4>
                            <p><?= NIT_INSTITUCION ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-bottom" style="padding: 2rem 0; margin-top: 0; border: none;">
                <p>&copy; 2024 <?= NOMBRE_INSTITUCION ?>. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>
