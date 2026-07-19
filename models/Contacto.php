<?php
/**
 * MODELO: CONTACTO
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';

class Contacto {
    private $pdo;
    private $table = 'contactos';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Crear contacto
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (nombre, email, telefono, asunto, mensaje)
                  VALUES 
                  (:nombre, :email, :telefono, :asunto, :mensaje)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':email' => $data['email'],
            ':telefono' => $data['telefono'] ?? null,
            ':asunto' => $data['asunto'],
            ':mensaje' => $data['mensaje']
        ]);
    }

    /**
     * Obtener contacto por ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtener todos
     */
    public function getAll($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT * FROM {$this->table}
                  ORDER BY created_at DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Contar total
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->pdo->query($query);
        return $stmt->fetch()['total'];
    }

    /**
     * Marcar como leído
     */
    public function markAsRead($id) {
        $query = "UPDATE {$this->table} SET leido = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Eliminar
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}

?>
