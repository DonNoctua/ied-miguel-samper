<?php
/**
 * CONTROLADOR: ESTUDIANTE
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../models/Estudiante.php';

class EstudianteController {
    private $pdo;
    private $estudianteModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->estudianteModel = new Estudiante($pdo);

        if (!isLoggedIn()) {
            redirect(BASE_URL . 'login');
        }
    }

    /**
     * Listar estudiantes
     */
    public function index() {
        if (!hasPermission([1, 2, 3, 4, 5])) {
            redirect(BASE_URL . 'dashboard');
        }

        $page = (int)($_GET['page'] ?? 1);
        $search = sanitize($_GET['q'] ?? '');
        $grado = sanitize($_GET['grado'] ?? '');

        if ($search) {
            $estudiantes = $this->estudianteModel->search($search, $page, ITEMS_PER_PAGE);
            $total = count($estudiantes);
        } elseif ($grado) {
            $estudiantes = $this->estudianteModel->getByGrade($grado, $page, ITEMS_PER_PAGE);
            $total = count($estudiantes);
        } else {
            $estudiantes = $this->estudianteModel->getAll($page, ITEMS_PER_PAGE);
            $total = $this->estudianteModel->count();
        }

        $pages = ceil($total / ITEMS_PER_PAGE);

        $data = [
            'estudiantes' => $estudiantes,
            'page' => $page,
            'pages' => $pages,
            'search' => $search,
            'grado' => $grado,
            'total' => $total
        ];

        return view('estudiantes/index', $data);
    }

    /**
     * Ver estudiante
     */
    public function show($id) {
        $estudiante = $this->estudianteModel->getById($id);

        if (!$estudiante) {
            setFlash('error', 'Estudiante no encontrado');
            redirect(BASE_URL . 'estudiantes');
        }

        return view('estudiantes/show', ['estudiante' => $estudiante]);
    }

    /**
     * Crear estudiante
     */
    public function create() {
        if (!hasPermission([1, 4])) {
            redirect(BASE_URL . 'dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_FIELD] ?? '')) {
                setFlash('error', 'Token inválido');
                redirect(BASE_URL . 'estudiantes/crear');
            }

            $data = [
                'numero_documento' => sanitize($_POST['numero_documento'] ?? ''),
                'nombre' => sanitize($_POST['nombre'] ?? ''),
                'apellido' => sanitize($_POST['apellido'] ?? ''),
                'fecha_nacimiento' => sanitize($_POST['fecha_nacimiento'] ?? ''),
                'genero' => sanitize($_POST['genero'] ?? ''),
                'direccion' => sanitize($_POST['direccion'] ?? ''),
                'telefono' => sanitize($_POST['telefono'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'nombre_acudiente' => sanitize($_POST['nombre_acudiente'] ?? ''),
                'telefono_acudiente' => sanitize($_POST['telefono_acudiente'] ?? ''),
                'grado' => sanitize($_POST['grado'] ?? ''),
                'jornada' => sanitize($_POST['jornada'] ?? 'mañana')
            ];

            $errors = [];
            if (!isValidDocument($data['numero_documento'])) $errors[] = 'Documento inválido';
            if (empty($data['nombre'])) $errors[] = 'Nombre requerido';
            if (empty($data['apellido'])) $errors[] = 'Apellido requerido';
            if (empty($data['fecha_nacimiento'])) $errors[] = 'Fecha de nacimiento requerida';
            if (empty($data['genero'])) $errors[] = 'Género requerido';

            if ($errors) {
                setFlash('error', implode(', ', $errors));
                redirect(BASE_URL . 'estudiantes/crear');
            }

            if ($this->estudianteModel->create($data)) {
                logActivity($_SESSION['usuario_id'], 'CREATE_STUDENT', 'estudiantes', $this->pdo->lastInsertId());
                setFlash('success', 'Estudiante creado correctamente');
                redirect(BASE_URL . 'estudiantes');
            } else {
                setFlash('error', 'Error al crear estudiante');
                redirect(BASE_URL . 'estudiantes/crear');
            }
        }

        return view('estudiantes/create');
    }

    /**
     * Editar estudiante
     */
    public function edit($id) {
        if (!hasPermission([1, 4])) {
            redirect(BASE_URL . 'dashboard');
        }

        $estudiante = $this->estudianteModel->getById($id);

        if (!$estudiante) {
            setFlash('error', 'Estudiante no encontrado');
            redirect(BASE_URL . 'estudiantes');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_FIELD] ?? '')) {
                setFlash('error', 'Token inválido');
                redirect(BASE_URL . 'estudiantes/editar/' . $id);
            }

            $data = [
                'nombre' => sanitize($_POST['nombre'] ?? ''),
                'apellido' => sanitize($_POST['apellido'] ?? ''),
                'fecha_nacimiento' => sanitize($_POST['fecha_nacimiento'] ?? ''),
                'genero' => sanitize($_POST['genero'] ?? ''),
                'direccion' => sanitize($_POST['direccion'] ?? ''),
                'telefono' => sanitize($_POST['telefono'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'nombre_acudiente' => sanitize($_POST['nombre_acudiente'] ?? ''),
                'telefono_acudiente' => sanitize($_POST['telefono_acudiente'] ?? ''),
                'grado' => sanitize($_POST['grado'] ?? ''),
                'jornada' => sanitize($_POST['jornada'] ?? 'mañana'),
                'estado' => sanitize($_POST['estado'] ?? 'activo')
            ];

            if ($this->estudianteModel->update($id, $data)) {
                logActivity($_SESSION['usuario_id'], 'UPDATE_STUDENT', 'estudiantes', $id);
                setFlash('success', 'Estudiante actualizado correctamente');
                redirect(BASE_URL . 'estudiantes');
            } else {
                setFlash('error', 'Error al actualizar');
                redirect(BASE_URL . 'estudiantes/editar/' . $id);
            }
        }

        return view('estudiantes/edit', ['estudiante' => $estudiante]);
    }

    /**
     * Eliminar estudiante
     */
    public function delete($id) {
        if (!hasPermission([1, 4])) {
            redirect(BASE_URL . 'dashboard');
        }

        if ($this->estudianteModel->delete($id)) {
            logActivity($_SESSION['usuario_id'], 'DELETE_STUDENT', 'estudiantes', $id);
            setFlash('success', 'Estudiante eliminado');
        } else {
            setFlash('error', 'Error al eliminar');
        }

        redirect(BASE_URL . 'estudiantes');
    }
}

?>
