<?php
/**
 * CONTROLADOR: GALERÍA
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../models/Galeria.php';

class GaleriaController {
    private $pdo;
    private $galeriaModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->galeriaModel = new Galeria($pdo);
    }

    /**
     * Listar galería (página pública)
     */
    public function index() {
        $page = (int)($_GET['page'] ?? 1);
        $categoria = sanitize($_GET['categoria'] ?? '');

        if ($categoria) {
            $imagenes = $this->galeriaModel->getByCategory($categoria, $page, 12);
            $total = $this->galeriaModel->countByCategory($categoria);
        } else {
            $imagenes = $this->galeriaModel->getAll($page, 12);
            $total = $this->galeriaModel->count();
        }

        $pages = ceil($total / 12);
        $categorias = $this->galeriaModel->getCategories();

        $data = [
            'imagenes' => $imagenes,
            'categorias' => $categorias,
            'categoria_actual' => $categoria,
            'page' => $page,
            'pages' => $pages
        ];

        return view('public/galeria/index', $data);
    }

    /**
     * Listar galería (admin)
     */
    public function admin() {
        if (!isLoggedIn() || !hasPermission([1, 3])) {
            redirect(BASE_URL . 'login');
        }

        $page = (int)($_GET['page'] ?? 1);
        $categoria = sanitize($_GET['categoria'] ?? '');
        $search = sanitize($_GET['q'] ?? '');

        if ($search) {
            $imagenes = $this->galeriaModel->search($search, $page, 12);
            $total = count($imagenes);
        } elseif ($categoria) {
            $imagenes = $this->galeriaModel->getByCategory($categoria, $page, 12);
            $total = $this->galeriaModel->countByCategory($categoria);
        } else {
            $imagenes = $this->galeriaModel->getAll($page, 12);
            $total = $this->galeriaModel->count();
        }

        $pages = ceil($total / 12);
        $categorias = $this->galeriaModel->getCategories();

        $data = [
            'imagenes' => $imagenes,
            'categorias' => $categorias,
            'categoria_actual' => $categoria,
            'search' => $search,
            'page' => $page,
            'pages' => $pages
        ];

        return view('admin/galeria/index', $data);
    }

    /**
     * Crear imagen
     */
    public function create() {
        if (!isLoggedIn() || !hasPermission([1, 3])) {
            redirect(BASE_URL . 'login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_FIELD] ?? '')) {
                setFlash('error', 'Token inválido');
                redirect(BASE_URL . 'admin/galeria/crear');
            }

            if (!isset($_FILES['imagen']) || $_FILES['imagen']['size'] === 0) {
                setFlash('error', 'Debes seleccionar una imagen');
                redirect(BASE_URL . 'admin/galeria/crear');
            }

            $imagen = uploadFile($_FILES['imagen'], 'galeria');
            if (!$imagen) {
                setFlash('error', 'Error al subir imagen');
                redirect(BASE_URL . 'admin/galeria/crear');
            }

            $data = [
                'nombre' => sanitize($_POST['nombre'] ?? ''),
                'descripcion' => sanitize($_POST['descripcion'] ?? ''),
                'imagen' => $imagen,
                'categoria' => sanitize($_POST['categoria'] ?? 'General'),
                'autor_id' => $_SESSION['usuario_id']
            ];

            if (empty($data['nombre'])) {
                deleteFile($imagen);
                setFlash('error', 'Nombre requerido');
                redirect(BASE_URL . 'admin/galeria/crear');
            }

            if ($this->galeriaModel->create($data)) {
                logActivity($_SESSION['usuario_id'], 'CREATE_GALLERY', 'galeria', $this->pdo->lastInsertId());
                setFlash('success', 'Imagen subida correctamente');
                redirect(BASE_URL . 'admin/galeria');
            } else {
                deleteFile($imagen);
                setFlash('error', 'Error al guardar imagen');
                redirect(BASE_URL . 'admin/galeria/crear');
            }
        }

        return view('admin/galeria/create');
    }

    /**
     * Editar imagen
     */
    public function edit($id) {
        if (!isLoggedIn() || !hasPermission([1, 3])) {
            redirect(BASE_URL . 'login');
        }

        $imagen_data = $this->galeriaModel->getById($id);

        if (!$imagen_data) {
            setFlash('error', 'Imagen no encontrada');
            redirect(BASE_URL . 'admin/galeria');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_FIELD] ?? '')) {
                setFlash('error', 'Token inválido');
                redirect(BASE_URL . 'admin/galeria/editar/' . $id);
            }

            $imagen = $imagen_data['imagen'];
            if (isset($_FILES['imagen']) && $_FILES['imagen']['size'] > 0) {
                deleteFile($imagen_data['imagen']);
                $imagen = uploadFile($_FILES['imagen'], 'galeria');
                if (!$imagen) {
                    setFlash('error', 'Error al subir imagen');
                    redirect(BASE_URL . 'admin/galeria/editar/' . $id);
                }
            }

            $data = [
                'nombre' => sanitize($_POST['nombre'] ?? ''),
                'descripcion' => sanitize($_POST['descripcion'] ?? ''),
                'imagen' => $imagen,
                'categoria' => sanitize($_POST['categoria'] ?? 'General')
            ];

            if ($this->galeriaModel->update($id, $data)) {
                logActivity($_SESSION['usuario_id'], 'UPDATE_GALLERY', 'galeria', $id);
                setFlash('success', 'Imagen actualizada correctamente');
                redirect(BASE_URL . 'admin/galeria');
            } else {
                setFlash('error', 'Error al actualizar');
                redirect(BASE_URL . 'admin/galeria/editar/' . $id);
            }
        }

        return view('admin/galeria/edit', ['imagen' => $imagen_data]);
    }

    /**
     * Eliminar imagen
     */
    public function delete($id) {
        if (!isLoggedIn() || !hasPermission([1, 3])) {
            redirect(BASE_URL . 'login');
        }

        $imagen = $this->galeriaModel->getById($id);

        if ($imagen) {
            deleteFile($imagen['imagen']);
            if ($this->galeriaModel->delete($id)) {
                logActivity($_SESSION['usuario_id'], 'DELETE_GALLERY', 'galeria', $id);
                setFlash('success', 'Imagen eliminada');
            }
        } else {
            setFlash('error', 'Imagen no encontrada');
        }

        redirect(BASE_URL . 'admin/galeria');
    }
}

?>
