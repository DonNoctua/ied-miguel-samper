<?php
/**
 * ARCHIVO PRINCIPAL - ROUTER
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/session.php';

// Función para cargar vistas
function view($name, $data = []) {
    extract($data);
    $file = VIEWS_PATH . $name . '.php';
    if (file_exists($file)) {
        ob_start();
        include $file;
        return ob_get_clean();
    }
    return "Vista no encontrada: $name";
}

// Obtener ruta actual
$request = $_SERVER['REQUEST_URI'];
$request = str_replace(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'), '', $request);
$request = ltrim($request, '/');
$request = strtok($request, '?');

if (empty($request)) {
    $request = 'inicio';
}

// Router
$parts = explode('/', $request);
$action = $parts[0] ?? '';
$id = $parts[1] ?? null;
$subaction = $parts[2] ?? null;

// Rutas
switch ($action) {
    // Autenticación
    case 'login':
        require_once CONTROLLERS_PATH . 'AuthController.php';
        $controller = new AuthController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            echo $controller->loginForm();
        }
        break;

    case 'logout':
        require_once CONTROLLERS_PATH . 'AuthController.php';
        $controller = new AuthController($pdo);
        $controller->logout();
        break;

    case 'cambiar-password':
        require_once CONTROLLERS_PATH . 'AuthController.php';
        $controller = new AuthController($pdo);
        $controller->changePassword();
        break;

    // Dashboard
    case 'dashboard':
        require_once CONTROLLERS_PATH . 'DashboardController.php';
        $controller = new DashboardController($pdo);
        echo $controller->index();
        break;

    // Usuarios (Admin)
    case 'admin':
        if ($id === 'usuarios') {
            require_once CONTROLLERS_PATH . 'UsuarioController.php';
            $controller = new UsuarioController($pdo);

            if ($subaction === 'crear') {
                echo $controller->create();
            } elseif ($subaction === 'editar' && isset($parts[3])) {
                echo $controller->edit($parts[3]);
            } elseif ($subaction === 'eliminar' && isset($parts[3])) {
                $controller->delete($parts[3]);
            } elseif (is_numeric($id)) {
                echo $controller->show($id);
            } else {
                echo $controller->index();
            }
        }
        // Noticias
        elseif ($id === 'noticias') {
            require_once CONTROLLERS_PATH . 'NoticiaController.php';
            $controller = new NoticiaController($pdo);

            if ($subaction === 'crear') {
                echo $controller->create();
            } elseif ($subaction === 'editar' && isset($parts[3])) {
                echo $controller->edit($parts[3]);
            } elseif ($subaction === 'eliminar' && isset($parts[3])) {
                $controller->delete($parts[3]);
            } else {
                echo $controller->admin();
            }
        }
        // Galería
        elseif ($id === 'galeria') {
            require_once CONTROLLERS_PATH . 'GaleriaController.php';
            $controller = new GaleriaController($pdo);

            if ($subaction === 'crear') {
                echo $controller->create();
            } elseif ($subaction === 'editar' && isset($parts[3])) {
                echo $controller->edit($parts[3]);
            } elseif ($subaction === 'eliminar' && isset($parts[3])) {
                $controller->delete($parts[3]);
            } else {
                echo $controller->admin();
            }
        }
        // Contactos
        elseif ($id === 'contactos') {
            require_once CONTROLLERS_PATH . 'ContactoController.php';
            $controller = new ContactoController($pdo);

            if (is_numeric($subaction)) {
                echo $controller->show($subaction);
            } elseif ($subaction === 'eliminar' && isset($parts[3])) {
                $controller->delete($parts[3]);
            } else {
                echo $controller->admin();
            }
        }
        break;

    // Estudiantes
    case 'estudiantes':
        require_once CONTROLLERS_PATH . 'EstudianteController.php';
        $controller = new EstudianteController($pdo);

        if ($id === 'crear') {
            echo $controller->create();
        } elseif ($id === 'editar' && isset($parts[2])) {
            echo $controller->edit($parts[2]);
        } elseif ($id === 'eliminar' && isset($parts[2])) {
            $controller->delete($parts[2]);
        } elseif (is_numeric($id)) {
            echo $controller->show($id);
        } else {
            echo $controller->index();
        }
        break;

    // Matrículas
    case 'matriculas':
        require_once CONTROLLERS_PATH . 'MatriculaController.php';
        $controller = new MatriculaController($pdo);

        if ($id === 'crear') {
            echo $controller->create();
        } elseif ($id === 'editar' && isset($parts[2])) {
            echo $controller->edit($parts[2]);
        } elseif ($id === 'actualizar-estado' && isset($parts[2])) {
            $controller->updateStatus($parts[2]);
        } elseif ($id === 'eliminar' && isset($parts[2])) {
            $controller->delete($parts[2]);
        } elseif (is_numeric($id)) {
            echo $controller->show($id);
        } else {
            echo $controller->index();
        }
        break;

    // Noticias (públicas)
    case 'noticias':
        require_once CONTROLLERS_PATH . 'NoticiaController.php';
        $controller = new NoticiaController($pdo);

        if (is_numeric($id)) {
            echo $controller->show($id);
        } else {
            echo $controller->index();
        }
        break;

    // Galería (pública)
    case 'galeria':
        require_once CONTROLLERS_PATH . 'GaleriaController.php';
        $controller = new GaleriaController($pdo);
        echo $controller->index();
        break;

    // Contacto
    case 'contacto':
        require_once CONTROLLERS_PATH . 'ContactoController.php';
        $controller = new ContactoController($pdo);
        echo $controller->form();
        break;

    // Páginas públicas
    case 'inicio':
        require_once CONTROLLERS_PATH . 'PageController.php';
        $controller = new PageController($pdo);
        echo $controller->index();
        break;

    case 'nosotros':
        require_once CONTROLLERS_PATH . 'PageController.php';
        $controller = new PageController($pdo);
        echo $controller->nosotros();
        break;

    case 'historia':
        require_once CONTROLLERS_PATH . 'PageController.php';
        $controller = new PageController($pdo);
        echo $controller->historia();
        break;

    case 'mision':
        require_once CONTROLLERS_PATH . 'PageController.php';
        $controller = new PageController($pdo);
        echo $controller->mision();
        break;

    case 'vision':
        require_once CONTROLLERS_PATH . 'PageController.php';
        $controller = new PageController($pdo);
        echo $controller->vision();
        break;

    case 'programas':
        require_once CONTROLLERS_PATH . 'PageController.php';
        $controller = new PageController($pdo);
        echo $controller->programas();
        break;

    case 'matriculas-info':
        require_once CONTROLLERS_PATH . 'PageController.php';
        $controller = new PageController($pdo);
        echo $controller->matriculas();
        break;

    // Página no encontrada
    default:
        http_response_code(404);
        echo view('public/404');
        break;
}

?>
