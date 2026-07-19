<?php
/**
 * VISTA: LOGIN
 * IED Miguel Samper Agudelo
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - IED Miguel Samper</title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="login-logo">🎓</div>
                <h1><?= NOMBRE_CORTO ?></h1>
                <p>Portal Institucional</p>
            </div>

            <?php if ($error = getFlash('error')): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <?php if ($success = getFlash('success')): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>login">
                <input type="hidden" name="<?= CSRF_TOKEN_FIELD ?>" value="<?= generateCSRFToken() ?>">

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword()">👁️</button>
                    </div>
                </div>

                <div class="remember-forgot">
                    <label>
                        <input type="checkbox" name="recordar"> Recordar sesión
                    </label>
                    <a href="#">¿Olvidé la contraseña?</a>
                </div>

                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem; color: #666; font-size: 0.9rem;">
                <p>© 2024 <?= NOMBRE_INSTITUCION ?></p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
