<?php
/**
 * MODELO: ESTUDIANTE
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';

class Estudiante {
    private $pdo;
    private $table = 'estudiantes';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Crear estudiante
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (numero_documento, nombre, apellido, fecha_nacimiento, genero, 
                   direccion, telefono, email, nombre_acudiente, telefono_acudiente, 
                   grado, jornada, estado)
                  VALUES 
                  (:numero_documento, :nombre, :apellido, :fecha_nacimiento, :genero,
                   :direccion, :telefono, :email, :nombre_acudiente, :telefono_acudiente,
                   :grado, :jornada, :estado)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':numero_documento' => $data['numero_documento'],
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':fecha_nacimiento' => $data['fecha_nacimiento'],
            ':genero' => $data['genero'],
            ':direccion' => $data['direccion'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':email' => $data['email'] ?? null,
            ':nombre_acudiente' => $data['nombre_acudiente'] ?? null,
            ':telefono_acudiente' => $data['telefono_acudiente'] ?? null,
            ':grado' => $data['grado'] ?? null,
            ':jornada' => $data['jornada'] ?? 'mañana',
            ':estado' => 'activo'
        ]);
    }

    /**
     * Obtener estudiante por ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtener por número de documento
     */
    public function getByDocument($doc) {
        $query = "SELECT * FROM {$this->table} WHERE numero_documento = :doc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':doc' => $doc]);
        return $stmt->fetch();
    }

    /**
     * Obtener todos
     */
    public function getAll($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT * FROM {$this->table} WHERE estado = 'activo'
                  ORDER BY apellido, nombre ASC
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
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE estado = 'activo'";
        $stmt = $this->pdo->query($query);
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
        $query = "UPDATE {$this->table} SET estado = 'inactivo' WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Buscar
     */
    public function search($query, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $search = "%$query%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE estado = 'activo' AND
                (nombre LIKE :search OR apellido LIKE :search OR numero_documento LIKE :search OR email LIKE :search)
                ORDER BY apellido, nombre ASC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':search', $search);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener por grado
     */
    public function getByGrade($grado, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT * FROM {$this->table} 
                  WHERE estado = 'activo' AND grado = :grado
                  ORDER BY apellido, nombre ASC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':grado', $grado);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

?>
