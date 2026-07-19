<?php
/**
 * CONTROLADOR: CONTACTO
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../models/Contacto.php';

class ContactoController {
    private $pdo;
    private $contactoModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->contactoModel = new Contacto($pdo);
    }

    /**
     * Formulario de contacto (página pública)
     */
    public function form() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_FIELD] ?? '')) {
                setFlash('error', 'Token inválido');
                redirect(BASE_URL . 'contacto');
            }

            $data = [
                'nombre' => sanitize($_POST['nombre'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'telefono' => sanitize($_POST['telefono'] ?? ''),
                'asunto' => sanitize($_POST['asunto'] ?? ''),
                'mensaje' => sanitize($_POST['mensaje'] ?? '')
            ];

            $errors = [];
            if (empty($data['nombre'])) $errors[] = 'Nombre requerido';
            if (!isValidEmail($data['email'])) $errors[] = 'Email inválido';
            if (empty($data['asunto'])) $errors[] = 'Asunto requerido';
            if (empty($data['mensaje'])) $errors[] = 'Mensaje requerido';

            if ($errors) {
                setFlash('error', implode(', ', $errors));
                redirect(BASE_URL . 'contacto');
            }

            if ($this->contactoModel->create($data)) {
                setFlash('success', '¡Gracias por tu mensaje! Nos pondremos en contacto pronto.');
                redirect(BASE_URL . 'contacto');
            } else {
                setFlash('error', 'Error al enviar el mensaje');
                redirect(BASE_URL . 'contacto');
            }
        }

        return view('public/contacto');
    }

    /**
     * Ver mensajes de contacto (admin)
     */
    public function admin() {
        if (!isLoggedIn() || !hasPermission(1)) {
            redirect(BASE_URL . 'login');
        }

        $page = (int)($_GET['page'] ?? 1);
        $contactos = $this->contactoModel->getAll($page, ITEMS_PER_PAGE);
        $total = $this->contactoModel->count();
        $pages = ceil($total / ITEMS_PER_PAGE);

        $data = [
            'contactos' => $contactos,
            'page' => $page,
            'pages' => $pages,
            'total' => $total
        ];

        return view('admin/contactos/index', $data);
    }

    /**
     * Ver detalle de contacto
     */
    public function show($id) {
        if (!isLoggedIn() || !hasPermission(1)) {
            redirect(BASE_URL . 'login');
        }

        $contacto = $this->contactoModel->getById($id);

        if (!$contacto) {
            setFlash('error', 'Contacto no encontrado');
            redirect(BASE_URL . 'admin/contactos');
        }

        if (!$contacto['leido']) {
            $this->contactoModel->markAsRead($id);
        }

        return view('admin/contactos/show', ['contacto' => $contacto]);
    }

    /**
     * Eliminar contacto
     */
    public function delete($id) {
        if (!isLoggedIn() || !hasPermission(1)) {
            redirect(BASE_URL . 'login');
        }

        if ($this->contactoModel->delete($id)) {
            setFlash('success', 'Contacto eliminado');
        } else {
            setFlash('error', 'Error al eliminar');
        }

        redirect(BASE_URL . 'admin/contactos');
    }
}

?>
