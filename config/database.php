<?php
/**
 * CONEXIÓN A BASE DE DATOS CON PDO
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/config.php';

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $charset = DB_CHARSET;
    private $pdo;
    private $error;

    /**
     * Conectar a la base de datos
     */
    public function connect() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=' . $this->charset;

        $options = [
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
            return $this->pdo;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            die('Error de conexión: ' . $this->error);
        }
    }

    /**
     * Obtener conexión PDO
     */
    public function getConnection() {
        if (!$this->pdo) {
            $this->connect();
        }
        return $this->pdo;
    }
}

// Crear instancia global de base de datos
$db = new Database();
$pdo = $db->getConnection();

?>
