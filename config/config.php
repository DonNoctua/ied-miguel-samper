<?php
/**
 * CONFIGURACIÓN GENERAL DEL SISTEMA
 * IED Miguel Samper Agudelo
 */

// Zona horaria
date_default_timezone_set('America/Bogota');

// Información de la institución
define('NOMBRE_INSTITUCION', 'Institución Educativa Departamental Miguel Samper Agudelo');
define('NOMBRE_CORTO', 'IED Miguel Samper');
define('EMAIL_INSTITUCION', 'contacto@iedmiguelsamper.edu.co');
define('TELEFONO_INSTITUCION', '+57 1 1234567');
define('DIRECCION_INSTITUCION', 'Bogotá, Colombia');
define('NIT_INSTITUCION', '860.123.456-7');

// Colores institucionales
define('COLOR_PRIMARIO', '#DC3545'); // Rojo
define('COLOR_SECUNDARIO', '#28A745'); // Verde
define('COLOR_TERCIARIO', '#FFFFFF'); // Blanco

// URLs
define('BASE_URL', 'http://localhost/ied-miguel-samper/');
define('PUBLIC_URL', BASE_URL . 'public/');
define('UPLOADS_URL', BASE_URL . 'uploads/');

// Rutas en servidor
define('BASE_PATH', dirname(__DIR__) . '/');
define('UPLOADS_PATH', BASE_PATH . 'uploads/');
define('VIEWS_PATH', BASE_PATH . 'views/');
define('CONTROLLERS_PATH', BASE_PATH . 'controllers/');
define('MODELS_PATH', BASE_PATH . 'models/');

// Configuración de sesión
define('SESSION_TIMEOUT', 1800); // 30 minutos
define('SESSION_NAME', 'IED_MIGUEL_SAMPER_SESSION');
define('REMEMBER_DURATION', 2592000); // 30 días

// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ied_miguel_samper');
define('DB_CHARSET', 'utf8mb4');

// Modo de depuración
define('DEBUG_MODE', true);

// Límites de carga
define('MAX_FILE_SIZE', 5242880); // 5 MB
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Paginación
define('ITEMS_PER_PAGE', 10);

// Token CSRF
define('CSRF_TOKEN_NAME', '_token');
define('CSRF_TOKEN_FIELD', '_csrf_token');

// Errores
ini_set('display_errors', DEBUG_MODE ? 1 : 0);
ini_set('log_errors', 1);

?>
