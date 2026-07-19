<?php
/**
 * CONTROLADOR: AUTENTICACIÓN
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $pdo;
    private $usuarioModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->usuarioModel = new Usuario($pdo);
    }

    /**
     * Mostrar formulario de login
     */
    public function loginForm() {
        // Si ya está autenticado, redirigir al dashboard
        if (isLoggedIn()) {
            redirect(BASE_URL . 'dashboard');
        }

        // Verificar token de recordar sesión
        if (isset($_COOKIE['remember_token'])) {
            $usuario = $this->usuarioModel->getByRememberToken($_COOKIE['remember_token']);
            if ($usuario) {
                createSession($usuario);
                logActivity($usuario['id'], 'LOGIN_REMEMBER', 'usuarios', $usuario['id']);
                redirect(BASE_URL . 'dashboard');
            }
        }

        return view('auth/login');
    }

    /**
     * Procesar login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'login');
        }

        // Validar CSRF
        if (!verifyCSRFToken($_POST[CSRF_TOKEN_FIELD] ?? '')) {
            setFlash('error', 'Token de seguridad inválido');
            redirect(BASE_URL . 'login');
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $recordar = isset($_POST['recordar']);

        // Validaciones
        if (empty($email) || empty($password)) {
            setFlash('error', 'Por favor completa todos los campos');
            redirect(BASE_URL . 'login');
        }

        if (!isValidEmail($email)) {
            setFlash('error', 'Email inválido');
            redirect(BASE_URL . 'login');
        }

        // Buscar usuario
        $usuario = $this->usuarioModel->getByEmail($email);

        if (!$usuario) {
            setFlash('error', 'Email o contraseña incorrectos');
            redirect(BASE_URL . 'login');
        }

        if ($usuario['estado'] !== 'activo') {
            setFlash('error', 'Tu cuenta está desactivada');
            redirect(BASE_URL . 'login');
        }

        // Verificar contraseña
        if (!verifyPassword($password, $usuario['password'])) {
            setFlash('error', 'Email o contraseña incorrectos');
            redirect(BASE_URL . 'login');
        }

        // Crear sesión
        createSession($usuario);
        $this->usuarioModel->updateLastAccess($usuario['id']);

        // Recordar sesión
        if ($recordar) {
            $token = generateToken();
            $this->usuarioModel->saveRememberToken($usuario['id'], $token);
            setcookie('remember_token', $token, time() + REMEMBER_DURATION, '/', '', false, true);
        }

        logActivity($usuario['id'], 'LOGIN', 'usuarios', $usuario['id']);
        setFlash('success', '¡Bienvenido ' . $usuario['nombre'] . '!');
        redirect(BASE_URL . 'dashboard');
    }

    /**
     * Logout
     */
    public function logout() {
        if (isLoggedIn()) {
            $user_id = $_SESSION['usuario_id'];
            logActivity($user_id, 'LOGOUT', 'usuarios', $user_id);
        }

        logout();
        setcookie('remember_token', '', time() - 42000, '/');
        setFlash('success', 'Sesión cerrada correctamente');
        redirect(BASE_URL . 'login');
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword() {
        if (!isLoggedIn()) {
            redirect(BASE_URL . 'login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_FIELD] ?? '')) {
                setFlash('error', 'Token de seguridad inválido');
                redirect(BASE_URL . 'perfil?tab=seguridad');
            }

            $usuario_id = $_SESSION['usuario_id'];
            $usuario = $this->usuarioModel->getById($usuario_id);
            $password_actual = $_POST['password_actual'] ?? '';
            $password_nuevo = $_POST['password_nuevo'] ?? '';
            $password_confirmar = $_POST['password_confirmar'] ?? '';

            // Validaciones
            if (empty($password_actual) || empty($password_nuevo) || empty($password_confirmar)) {
                setFlash('error', 'Por favor completa todos los campos');
                redirect(BASE_URL . 'perfil?tab=seguridad');
            }

            if (!verifyPassword($password_actual, $usuario['password'])) {
                setFlash('error', 'Contraseña actual incorrecta');
                redirect(BASE_URL . 'perfil?tab=seguridad');
            }

            if (!isValidPassword($password_nuevo)) {
                setFlash('error', 'La contraseña debe tener al menos 6 caracteres, una mayúscula y un número');
                redirect(BASE_URL . 'perfil?tab=seguridad');
            }

            if ($password_nuevo !== $password_confirmar) {
                setFlash('error', 'Las contraseñas no coinciden');
                redirect(BASE_URL . 'perfil?tab=seguridad');
            }

            if ($this->usuarioModel->changePassword($usuario_id, $password_nuevo)) {
                logActivity($usuario_id, 'CHANGE_PASSWORD', 'usuarios', $usuario_id);
                setFlash('success', 'Contraseña actualizada correctamente');
            } else {
                setFlash('error', 'Error al cambiar la contraseña');
            }

            redirect(BASE_URL . 'perfil?tab=seguridad');
        }
    }
}

?>
