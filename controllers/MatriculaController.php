<?php
/**
 * CONTROLADOR: MATRÍCULA
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../models/Matricula.php';
require_once __DIR__ . '/../models/Estudiante.php';

class MatriculaController {
    private $pdo;
    private $matriculaModel;
    private $estudianteModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->matriculaModel = new Matricula($pdo);
        $this->estudianteModel = new Estudiante($pdo);

        if (!isLoggedIn()) {
            redirect(BASE_URL . 'login');
        }
    }

    /**
     * Listar matrículas
     */
    public function index() {
        if (!hasPermission([1, 2, 4])) {
            redirect(BASE_URL . 'dashboard');
        }

        $page = (int)($_GET['page'] ?? 1);
        $estado = sanitize($_GET['estado'] ?? '');
        $search = sanitize($_GET['q'] ?? '');

        if ($search) {
            $matriculas = $this->matriculaModel->search($search, $page, ITEMS_PER_PAGE);
            $total = count($matriculas);
        } else {
            $matriculas = $this->matriculaModel->getAll($page, ITEMS_PER_PAGE, $estado ?: null);
            $total = $this->matriculaModel->count($estado ?: null);
        }

        $pages = ceil($total / ITEMS_PER_PAGE);
        $stats = $this->matriculaModel->getStats();

        $data = [
            'matriculas' => $matriculas,
            'page' => $page,
            'pages' => $pages,
            'estado' => $estado,
            'search' => $search,
            'total' => $total,
            'stats' => $stats
        ];

        return view('matriculas/index', $data);
    }

    /**
     * Ver matrícula
     */
    public function show($id) {
        $matricula = $this->matriculaModel->getById($id);

        if (!$matricula) {
            setFlash('error', 'Matrícula no encontrada');
            redirect(BASE_URL . 'matriculas');
        }

        return view('matriculas/show', ['matricula' => $matricula]);
    }

    /**
     * Crear matrícula
     */
    public function create() {
        if (!hasPermission([1, 4])) {
            redirect(BASE_URL . 'dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_FIELD] ?? '')) {
                setFlash('error', 'Token inválido');
                redirect(BASE_URL . 'matriculas/crear');
            }

            $data = [
                'estudiante_id' => (int)($_POST['estudiante_id'] ?? 0),
                'grado' => sanitize($_POST['grado'] ?? ''),
                'jornada' => sanitize($_POST['jornada'] ?? 'mañana'),
                'año_academico' => (int)date('Y')
            ];

            if ($data['estudiante_id'] <= 0 || empty($data['grado'])) {
                setFlash('error', 'Verifica los datos');
                redirect(BASE_URL . 'matriculas/crear');
            }

            if ($this->matriculaModel->create($data)) {
                logActivity($_SESSION['usuario_id'], 'CREATE_ENROLLMENT', 'matriculas', $this->pdo->lastInsertId());
                setFlash('success', 'Matrícula creada correctamente');
                redirect(BASE_URL . 'matriculas');
            } else {
                setFlash('error', 'Error al crear matrícula');
                redirect(BASE_URL . 'matriculas/crear');
            }
        }

        $estudiantes = $this->estudianteModel->getAll(1, 1000);
        return view('matriculas/create', ['estudiantes' => $estudiantes]);
    }

    /**
     * Cambiar estado de matrícula
     */
    public function updateStatus($id) {
        if (!hasPermission([1, 2, 4])) {
            redirect(BASE_URL . 'dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_FIELD] ?? '')) {
                setFlash('error', 'Token inválido');
                redirect(BASE_URL . 'matriculas/' . $id);
            }

            $estado = sanitize($_POST['estado'] ?? '');
            $observaciones = sanitize($_POST['observaciones'] ?? '');

            if (!in_array($estado, ['pendiente', 'aprobada', 'rechazada'])) {
                setFlash('error', 'Estado inválido');
                redirect(BASE_URL . 'matriculas/' . $id);
            }

            $this->matriculaModel->updateStatus($id, $estado, $_SESSION['usuario_id']);
            $this->matriculaModel->updateObservations($id, $observaciones);

            logActivity($_SESSION['usuario_id'], 'UPDATE_ENROLLMENT', 'matriculas', $id);
            setFlash('success', 'Matrícula actualizada correctamente');
            redirect(BASE_URL . 'matriculas');
        }
    }

    /**
     * Eliminar matrícula
     */
    public function delete($id) {
        if (!hasPermission([1, 4])) {
            redirect(BASE_URL . 'dashboard');
        }

        if ($this->matriculaModel->delete($id)) {
            logActivity($_SESSION['usuario_id'], 'DELETE_ENROLLMENT', 'matriculas', $id);
            setFlash('success', 'Matrícula eliminada');
        } else {
            setFlash('error', 'Error al eliminar');
        }

        redirect(BASE_URL . 'matriculas');
    }
}

?>
