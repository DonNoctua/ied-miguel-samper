<?php
/**
 * CONTROLADOR: DASHBOARD
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Estudiante.php';
require_once __DIR__ . '/../models/Matricula.php';
require_once __DIR__ . '/../models/Noticia.php';
require_once __DIR__ . '/../models/Galeria.php';

class DashboardController {
    private $pdo;
    private $usuarioModel;
    private $estudianteModel;
    private $matriculaModel;
    private $noticiaModel;
    private $galeriaModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->usuarioModel = new Usuario($pdo);
        $this->estudianteModel = new Estudiante($pdo);
        $this->matriculaModel = new Matricula($pdo);
        $this->noticiaModel = new Noticia($pdo);
        $this->galeriaModel = new Galeria($pdo);

        if (!isLoggedIn()) {
            redirect(BASE_URL . 'login');
        }
    }

    /**
     * Dashboard según rol
     */
    public function index() {
        $user = getCurrentUser();
        $data = [];

        // Datos comunes
        $data['user'] = $user;

        switch ($user['rol_id']) {
            case 1: // Administrador
                $data['usuarios_count'] = $this->usuarioModel->count();
                $data['estudiantes_count'] = $this->estudianteModel->count();
                $data['matriculas_count'] = $this->matriculaModel->count();
                $data['noticias_count'] = $this->noticiaModel->count();
                $data['galeria_count'] = $this->galeriaModel->count();
                $data['matriculas_stats'] = $this->matriculaModel->getStats();
                $data['noticias_recientes'] = $this->noticiaModel->getAll(1, 5);
                break;

            case 2: // Rector
                $data['estudiantes_count'] = $this->estudianteModel->count();
                $data['matriculas_count'] = $this->matriculaModel->count();
                $data['matriculas_stats'] = $this->matriculaModel->getStats();
                $data['matriculas_recientes'] = $this->matriculaModel->getAll(1, 5);
                break;

            case 3: // Coordinador
                $data['noticias_count'] = $this->noticiaModel->count();
                $data['galeria_count'] = $this->galeriaModel->count();
                $data['estudiantes_count'] = $this->estudianteModel->count();
                $data['noticias_recientes'] = $this->noticiaModel->getAll(1, 5);
                break;

            case 4: // Secretaría
                $data['matriculas_count'] = $this->matriculaModel->count();
                $data['estudiantes_count'] = $this->estudianteModel->count();
                $data['matriculas_stats'] = $this->matriculaModel->getStats();
                break;

            case 5: // Docente
                $data['estudiantes_count'] = $this->estudianteModel->count();
                $data['noticias_recientes'] = $this->noticiaModel->getAll(1, 10);
                break;
        }

        return view('dashboard/index', $data);
    }
}

?>
