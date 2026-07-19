<?php
/**
 * MANEJO DE SESIONES
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/config.php';

// Configuración de sesión
session_name(SESSION_NAME);
session_set_cookie_params([
    'lifetime' => SESSION_TIMEOUT,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
    'secure' => false, // Cambiar a true en producción con HTTPS
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verificar si el usuario está autenticado
 */
function isLoggedIn() {
    return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_rol']);
}

/**
 * Obtener usuario actual
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'email' => $_SESSION['usuario_email'],
            'rol_id' => $_SESSION['usuario_rol'],
            'rol' => $_SESSION['usuario_rol_nombre']
        ];
    }
    return null;
}

/**
 * Verificar permiso de acceso
 */
function hasPermission($required_role) {
    if (!isLoggedIn()) {
        return false;
    }

    $current_role = $_SESSION['usuario_rol'];

    // Administrador tiene acceso a todo
    if ($current_role == 1) {
        return true;
    }

    // Verificar permisos específicos por rol
    if (is_array($required_role)) {
        return in_array($current_role, $required_role);
    }

    return $current_role == $required_role;
}

/**
 * Cerrar sesión
 */
function logout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(SESSION_NAME, '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']);
    }
    session_destroy();
}

/**
 * Crear sesión de usuario
 */
function createSession($user) {
    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['usuario_nombre'] = $user['nombre'] . ' ' . $user['apellido'];
    $_SESSION['usuario_email'] = $user['email'];
    $_SESSION['usuario_rol'] = $user['rol_id'];
    $_SESSION['usuario_rol_nombre'] = $user['rol_nombre'];
    $_SESSION['login_time'] = time();
}

/**
 * Verificar timeout de sesión
 */
function checkSessionTimeout() {
    if (isLoggedIn()) {
        if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
            logout();
            redirect(BASE_URL . 'login');
        }
        $_SESSION['login_time'] = time();
    }
}

// Verificar timeout en cada carga
if (session_status() === PHP_SESSION_ACTIVE) {
    checkSessionTimeout();
}

?>
