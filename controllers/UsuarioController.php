<?php
/**
 * CONTROLADOR: USUARIO
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private $pdo;
    private $usuarioModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->usuarioModel = new Usuario($pdo);

        if (!isLoggedIn() || !hasPermission(1)) {
            redirect(BASE_URL . 'login');
        }
    }

    /**
     * Listar usuarios
     */
    public function index() {
        $page = (int)($_GET['page'] ?? 1);
        $search = sanitize($_GET['q'] ?? '');

        if ($search) {
            $usuarios = $this->usuarioModel->search($search, $page, ITEMS_PER_PAGE);
            $total = count($usuarios);
        } else {
            $usuarios = $this->usuarioModel->getAll($page, ITEMS_PER_PAGE);
            $total = $this->usuarioModel->count();
        }

        $pages = ceil($total / ITEMS_PER_PAGE);

        $data = [
            'usuarios' => $usuarios,
            'page' => $page,
            'pages' => $pages,
            'search' => $search,
            'total' => $total
        ];

        return view('admin/usuarios/index', $data);
    }

    /**
     * Ver usuario
     */
    public function show($id) {
        $usuario = $this->usuarioModel->getById($id);

        if (!$usuario) {
            setFlash('error', 'Usuario no encontrado');
            redirect(BASE_URL . 'admin/usuarios');
        }

        return view('admin/usuarios/show', ['usuario' => $usuario]);
    }

    /**
     * Crear usuario
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_FIELD] ?? '')) {
                setFlash('error', 'Token inválido');
                redirect(BASE_URL . 'admin/usuarios/crear');
            }

            $data = [
                'nombre' => sanitize($_POST['nombre'] ?? ''),
                'apellido' => sanitize($_POST['apellido'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'cedula' => sanitize($_POST['cedula'] ?? ''),
                'telefono' => sanitize($_POST['telefono'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'rol_id' => (int)($_POST['rol_id'] ?? 5)
            ];

            // Validaciones
            $errors = [];
            if (empty($data['nombre'])) $errors[] = 'Nombre requerido';
            if (empty($data['apellido'])) $errors[] = 'Apellido requerido';
            if (!isValidEmail($data['email'])) $errors[] = 'Email inválido';
            if (!isValidPassword($data['password'])) $errors[] = 'Contraseña débil';

            if ($errors) {
                setFlash('error', implode(', ', $errors));
                redirect(BASE_URL . 'admin/usuarios/crear');
            }

            if ($this->usuarioModel->create($data)) {
                logActivity($_SESSION['usuario_id'], 'CREATE_USER', 'usuarios', $this->pdo->lastInsertId());
                setFlash('success', 'Usuario creado correctamente');
                redirect(BASE_URL . 'admin/usuarios');
            } else {
                setFlash('error', 'Error al crear usuario');
                redirect(BASE_URL . 'admin/usuarios/crear');
            }
        }

        // Obtener roles
        $roles = $this->pdo->query('SELECT * FROM roles WHERE id != 1')->fetchAll();
        return view('admin/usuarios/create', ['roles' => $roles]);
    }

    /**
     * Editar usuario
     */
    public function edit($id) {
        $usuario = $this->usuarioModel->getById($id);

        if (!$usuario) {
            setFlash('error', 'Usuario no encontrado');
            redirect(BASE_URL . 'admin/usuarios');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_FIELD] ?? '')) {
                setFlash('error', 'Token inválido');
                redirect(BASE_URL . 'admin/usuarios/editar/' . $id);
            }

            $data = [
                'nombre' => sanitize($_POST['nombre'] ?? ''),
                'apellido' => sanitize($_POST['apellido'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'cedula' => sanitize($_POST['cedula'] ?? ''),
                'telefono' => sanitize($_POST['telefono'] ?? ''),
                'rol_id' => (int)($_POST['rol_id'] ?? 5),
                'estado' => sanitize($_POST['estado'] ?? 'activo')
            ];

            if (empty($data['nombre']) || empty($data['apellido']) || !isValidEmail($data['email'])) {
                setFlash('error', 'Verifica los datos');
                redirect(BASE_URL . 'admin/usuarios/editar/' . $id);
            }

            if ($this->usuarioModel->update($id, $data)) {
                logActivity($_SESSION['usuario_id'], 'UPDATE_USER', 'usuarios', $id);
                setFlash('success', 'Usuario actualizado correctamente');
                redirect(BASE_URL . 'admin/usuarios');
            } else {
                setFlash('error', 'Error al actualizar');
                redirect(BASE_URL . 'admin/usuarios/editar/' . $id);
            }
        }

        $roles = $this->pdo->query('SELECT * FROM roles WHERE id != 1')->fetchAll();
        return view('admin/usuarios/edit', ['usuario' => $usuario, 'roles' => $roles]);
    }

    /**
     * Eliminar usuario
     */
    public function delete($id) {
        if ($id == 1) {
            setFlash('error', 'No puedes eliminar el usuario administrador');
            redirect(BASE_URL . 'admin/usuarios');
        }

        if ($this->usuarioModel->delete($id)) {
            logActivity($_SESSION['usuario_id'], 'DELETE_USER', 'usuarios', $id);
            setFlash('success', 'Usuario eliminado correctamente');
        } else {
            setFlash('error', 'Error al eliminar');
        }

        redirect(BASE_URL . 'admin/usuarios');
    }
}

?>
