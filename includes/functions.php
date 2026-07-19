<?php
/**
 * FUNCIONES GLOBALES DEL SISTEMA
 * IED Miguel Samper Agudelo
 */

/**
 * Sanitizar entrada de datos
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validar email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generar token CSRF
 */
function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verificar token CSRF
 */
function verifyCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Validar contraseña
 */
function isValidPassword($password) {
    return strlen($password) >= 6 && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[0-9]/', $password);
}

/**
 * Hash de contraseña
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Verificar contraseña
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generar token aleatorio
 */
function generateToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Redirigir página
 */
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

/**
 * Obtener mensaje flash
 */
function getFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

/**
 * Establecer mensaje flash
 */
function setFlash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

/**
 * Validar archivo
 */
function isValidFile($file, $type = 'image') {
    if ($type === 'image') {
        return in_array($file['type'], ALLOWED_IMAGE_TYPES) &&
               $file['size'] <= MAX_FILE_SIZE &&
               in_array(pathinfo($file['name'], PATHINFO_EXTENSION), ALLOWED_IMAGE_EXTENSIONS);
    }
    return false;
}

/**
 * Subir archivo
 */
function uploadFile($file, $directory) {
    if (!isValidFile($file)) {
        return false;
    }

    if (!is_dir(UPLOADS_PATH . $directory)) {
        mkdir(UPLOADS_PATH . $directory, 0755, true);
    }

    $filename = uniqid() . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $filepath = UPLOADS_PATH . $directory . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $directory . '/' . $filename;
    }

    return false;
}

/**
 * Eliminar archivo
 */
function deleteFile($filepath) {
    $fullpath = UPLOADS_PATH . $filepath;
    if (file_exists($fullpath)) {
        return unlink($fullpath);
    }
    return false;
}

/**
 * Formatear fecha
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    $timestamp = strtotime($date);
    return date($format, $timestamp);
}

/**
 * Obtener diferencia de tiempo
 */
function timeAgo($date) {
    $time = strtotime($date);
    $diff = time() - $time;

    if ($diff < 60) return 'Hace un momento';
    if ($diff < 3600) return 'Hace ' . floor($diff / 60) . ' minutos';
    if ($diff < 86400) return 'Hace ' . floor($diff / 3600) . ' horas';
    if ($diff < 604800) return 'Hace ' . floor($diff / 86400) . ' días';
    if ($diff < 2592000) return 'Hace ' . floor($diff / 604800) . ' semanas';
    if ($diff < 31536000) return 'Hace ' . floor($diff / 2592000) . ' meses';

    return 'Hace ' . floor($diff / 31536000) . ' años';
}

/**
 * Registrar actividad en logs
 */
function logActivity($user_id, $action, $table, $record_id, $details = []) {
    global $pdo;

    try {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $details_json = json_encode($details);

        $query = "INSERT INTO logs (usuario_id, accion, tabla, registro_id, detalles, ip_address)
                  VALUES (:user_id, :action, :table, :record_id, :details, :ip)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':user_id' => $user_id,
            ':action' => $action,
            ':table' => $table,
            ':record_id' => $record_id,
            ':details' => $details_json,
            ':ip' => $ip_address
        ]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Validar número de documento
 */
function isValidDocument($doc) {
    return preg_match('/^[0-9]{8,10}$/', $doc);
}

/**
 * Calcular edad
 */
function getAge($birthDate) {
    $today = new DateTime();
    $birth = new DateTime($birthDate);
    $age = $today->diff($birth);
    return $age->y;
}

?>
