<?php
/**
 * CONTROLADOR: NOTICIA
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../models/Noticia.php';

class NoticiaController {
    private $pdo;
    private $noticiaModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->noticiaModel = new Noticia($pdo);
    }

    /**
     * Listar noticias (página pública)
     */
    public function index() {
        $page = (int)($_GET['page'] ?? 1);
        $noticias = $this->noticiaModel->getAll($page, ITEMS_PER_PAGE);
        $total = $this->noticiaModel->count();
        $pages = ceil($total / ITEMS_PER_PAGE);
        $destacadas = $this->noticiaModel->getFeatured(5);

        $data = [
            'noticias' => $noticias,
            'destacadas' => $destacadas,
            'page' => $page,
            'pages' => $pages
        ];

        return view('public/noticias/index', $data);
    }

    /**
     * Ver noticia
     */
    public function show($id) {
        $noticia = $this->noticiaModel->getById($id);

        if (!$noticia || $noticia['estado'] !== 'publicado') {
            setFlash('error', 'Noticia no encontrada');
            redirect(BASE_URL . 'noticias');
        }

        $this->noticiaModel->incrementViews($id);
        $noticias_relacionadas = $this->noticiaModel->getAll(1, 3);

        return view('public/noticias/show', [
            'noticia' => $noticia,
            'relacionadas' => $noticias_relacionadas
        ]);
    }

    /**
     * Listar noticias (admin)
     */
    public function admin() {
        if (!isLoggedIn() || !hasPermission([1, 3])) {
            redirect(BASE_URL . 'login');
        }

        $page = (int)($_GET['page'] ?? 1);
        $estado = sanitize($_GET['estado'] ?? '');
        $search = sanitize($_GET['q'] ?? '');

        if ($search) {
            $noticias = $this->noticiaModel->search($search, $page, ITEMS_PER_PAGE);
            $total = count($noticias);
        } else {
            $noticias = $this->noticiaModel->getAll($page, ITEMS_PER_PAGE, $estado ?: null);
            $total = $this->noticiaModel->count($estado ?: null);
        }

        $pages = ceil($total / ITEMS_PER_PAGE);

        $data = [
            'noticias' => $noticias,
            'page' => $page,
            'pages' => $pages,
            'estado' => $estado,
            'search' => $search
        ];

        return view('admin/noticias/index', $data);
    }

    /**
     * Crear noticia
     */
    public function create() {
        if (!isLoggedIn() || !hasPermission([1, 3])) {
            redirect(BASE_URL . 'login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_FIELD] ?? '')) {
                setFlash('error', 'Token inválido');
                redirect(BASE_URL . 'admin/noticias/crear');
            }

            $imagen = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['size'] > 0) {
                $imagen = uploadFile($_FILES['imagen'], 'noticias');
                if (!$imagen) {
                    setFlash('error', 'Error al subir imagen');
                    redirect(BASE_URL . 'admin/noticias/crear');
                }
            }

            $data = [
                'titulo' => sanitize($_POST['titulo'] ?? ''),
                'descripcion' => sanitize($_POST['descripcion'] ?? ''),
                'contenido' => $_POST['contenido'] ?? '',
                'autor_id' => $_SESSION['usuario_id'],
                'imagen' => $imagen,
                'estado' => sanitize($_POST['estado'] ?? 'borrador'),
                'destacado' => isset($_POST['destacado']) ? 1 : 0
            ];

            $errors = [];
            if (empty($data['titulo'])) $errors[] = 'Título requerido';
            if (empty($data['descripcion'])) $errors[] = 'Descripción requerida';

            if ($errors) {
                setFlash('error', implode(', ', $errors));
                redirect(BASE_URL . 'admin/noticias/crear');
            }

            if ($this->noticiaModel->create($data)) {
                logActivity($_SESSION['usuario_id'], 'CREATE_NEWS', 'noticias', $this->pdo->lastInsertId());
                setFlash('success', 'Noticia creada correctamente');
                redirect(BASE_URL . 'admin/noticias');
            } else {
                setFlash('error', 'Error al crear noticia');
                redirect(BASE_URL . 'admin/noticias/crear');
            }
        }

        return view('admin/noticias/create');
    }

    /**
     * Editar noticia
     */
    public function edit($id) {
        if (!isLoggedIn() || !hasPermission([1, 3])) {
            redirect(BASE_URL . 'login');
        }

        $noticia = $this->noticiaModel->getById($id);

        if (!$noticia) {
            setFlash('error', 'Noticia no encontrada');
            redirect(BASE_URL . 'admin/noticias');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_FIELD] ?? '')) {
                setFlash('error', 'Token inválido');
                redirect(BASE_URL . 'admin/noticias/editar/' . $id);
            }

            $imagen = $noticia['imagen'];
            if (isset($_FILES['imagen']) && $_FILES['imagen']['size'] > 0) {
                if ($noticia['imagen']) {
                    deleteFile($noticia['imagen']);
                }
                $imagen = uploadFile($_FILES['imagen'], 'noticias');
                if (!$imagen) {
                    setFlash('error', 'Error al subir imagen');
                    redirect(BASE_URL . 'admin/noticias/editar/' . $id);
                }
            }

            $data = [
                'titulo' => sanitize($_POST['titulo'] ?? ''),
                'descripcion' => sanitize($_POST['descripcion'] ?? ''),
                'contenido' => $_POST['contenido'] ?? '',
                'imagen' => $imagen,
                'estado' => sanitize($_POST['estado'] ?? 'borrador'),
                'destacado' => isset($_POST['destacado']) ? 1 : 0
            ];

            if ($this->noticiaModel->update($id, $data)) {
                logActivity($_SESSION['usuario_id'], 'UPDATE_NEWS', 'noticias', $id);
                setFlash('success', 'Noticia actualizada correctamente');
                redirect(BASE_URL . 'admin/noticias');
            } else {
                setFlash('error', 'Error al actualizar');
                redirect(BASE_URL . 'admin/noticias/editar/' . $id);
            }
        }

        return view('admin/noticias/edit', ['noticia' => $noticia]);
    }

    /**
     * Eliminar noticia
     */
    public function delete($id) {
        if (!isLoggedIn() || !hasPermission([1, 3])) {
            redirect(BASE_URL . 'login');
        }

        $noticia = $this->noticiaModel->getById($id);

        if ($noticia && $noticia['imagen']) {
            deleteFile($noticia['imagen']);
        }

        if ($this->noticiaModel->delete($id)) {
            logActivity($_SESSION['usuario_id'], 'DELETE_NEWS', 'noticias', $id);
            setFlash('success', 'Noticia eliminada');
        } else {
            setFlash('error', 'Error al eliminar');
        }

        redirect(BASE_URL . 'admin/noticias');
    }
}

?>
