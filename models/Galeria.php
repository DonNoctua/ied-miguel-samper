<?php
/**
 * MODELO: GALERÍA
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';

class Galeria {
    private $pdo;
    private $table = 'galeria';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Crear imagen
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (nombre, descripcion, imagen, categoria, autor_id, estado)
                  VALUES 
                  (:nombre, :descripcion, :imagen, :categoria, :autor_id, :estado)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? '',
            ':imagen' => $data['imagen'],
            ':categoria' => $data['categoria'] ?? 'General',
            ':autor_id' => $data['autor_id'],
            ':estado' => 'activo'
        ]);
    }

    /**
     * Obtener imagen por ID
     */
    public function getById($id) {
        $query = "SELECT g.*, u.nombre as autor_nombre, u.apellido as autor_apellido
                  FROM {$this->table} g
                  LEFT JOIN usuarios u ON g.autor_id = u.id
                  WHERE g.id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtener todas las imágenes
     */
    public function getAll($page = 1, $limit = 12) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT g.*, u.nombre as autor_nombre, u.apellido as autor_apellido
                  FROM {$this->table} g
                  LEFT JOIN usuarios u ON g.autor_id = u.id
                  WHERE g.estado = 'activo'
                  ORDER BY g.created_at DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener por categoría
     */
    public function getByCategory($categoria, $page = 1, $limit = 12) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT g.*, u.nombre as autor_nombre, u.apellido as autor_apellido
                  FROM {$this->table} g
                  LEFT JOIN usuarios u ON g.autor_id = u.id
                  WHERE g.estado = 'activo' AND g.categoria = :categoria
                  ORDER BY g.created_at DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':categoria', $categoria);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Contar total
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE estado = 'activo'";
        $stmt = $this->pdo->query($query);
        return $stmt->fetch()['total'];
    }

    /**
     * Contar por categoría
     */
    public function countByCategory($categoria) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE estado = 'activo' AND categoria = :categoria";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':categoria' => $categoria]);
        return $stmt->fetch()['total'];
    }

    /**
     * Actualizar
     */
    public function update($id, $data) {
        $fields = [];
        $values = [':id' => $id];

        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = :$key";
                $values[":$key"] = $value;
            }
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($values);
    }

    /**
     * Eliminar
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Obtener todas las categorías
     */
    public function getCategories() {
        $query = "SELECT DISTINCT categoria FROM {$this->table} WHERE estado = 'activo' ORDER BY categoria";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll();
    }

    /**
     * Buscar
     */
    public function search($query, $page = 1, $limit = 12) {
        $offset = ($page - 1) * $limit;
        $search = "%$query%";
        $sql = "SELECT g.*, u.nombre as autor_nombre, u.apellido as autor_apellido
                FROM {$this->table} g
                LEFT JOIN usuarios u ON g.autor_id = u.id
                WHERE g.estado = 'activo' AND (g.nombre LIKE :search OR g.descripcion LIKE :search)
                ORDER BY g.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':search', $search);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

?>
