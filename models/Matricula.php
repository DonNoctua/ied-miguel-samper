<?php
/**
 * MODELO: MATRÍCULA
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';

class Matricula {
    private $pdo;
    private $table = 'matriculas';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Crear matrícula
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (estudiante_id, grado, jornada, año_academico, estado, fecha_solicitud)
                  VALUES 
                  (:estudiante_id, :grado, :jornada, :año_academico, :estado, :fecha_solicitud)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':estudiante_id' => $data['estudiante_id'],
            ':grado' => $data['grado'],
            ':jornada' => $data['jornada'] ?? 'mañana',
            ':año_academico' => $data['año_academico'],
            ':estado' => 'pendiente',
            ':fecha_solicitud' => date('Y-m-d')
        ]);
    }

    /**
     * Obtener matrícula por ID
     */
    public function getById($id) {
        $query = "SELECT m.*, e.nombre, e.apellido, e.numero_documento, e.genero, e.fecha_nacimiento,
                         e.direccion, e.telefono, e.email, e.nombre_acudiente, e.telefono_acudiente,
                         u.nombre as aprobado_por_nombre, u.apellido as aprobado_por_apellido
                  FROM {$this->table} m
                  LEFT JOIN estudiantes e ON m.estudiante_id = e.id
                  LEFT JOIN usuarios u ON m.aprobado_por = u.id
                  WHERE m.id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtener todas las matrículas
     */
    public function getAll($page = 1, $limit = 10, $estado = null) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT m.*, e.nombre, e.apellido, e.numero_documento, e.genero
                  FROM {$this->table} m
                  LEFT JOIN estudiantes e ON m.estudiante_id = e.id";

        if ($estado) {
            $query .= " WHERE m.estado = :estado";
        }

        $query .= " ORDER BY m.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($query);

        if ($estado) {
            $stmt->bindValue(':estado', $estado);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Contar total
     */
    public function count($estado = null) {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        if ($estado) {
            $query .= " WHERE estado = :estado";
        }
        $stmt = $this->pdo->prepare($query);
        if ($estado) {
            $stmt->bindValue(':estado', $estado);
        }
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    /**
     * Actualizar estado de matrícula
     */
    public function updateStatus($id, $estado, $usuario_id = null) {
        $query = "UPDATE {$this->table} SET estado = :estado";
        $values = [':estado' => $estado, ':id' => $id];

        if ($usuario_id) {
            $query .= ", aprobado_por = :usuario_id, fecha_aprobacion = NOW()";
            $values[':usuario_id'] = $usuario_id;
        }

        $query .= " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($values);
    }

    /**
     * Actualizar observaciones
     */
    public function updateObservations($id, $observaciones) {
        $query = "UPDATE {$this->table} SET observaciones = :observaciones WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':observaciones' => $observaciones,
            ':id' => $id
        ]);
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
     * Buscar
     */
    public function search($query, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $search = "%$query%";
        $sql = "SELECT m.*, e.nombre, e.apellido, e.numero_documento
                FROM {$this->table} m
                LEFT JOIN estudiantes e ON m.estudiante_id = e.id
                WHERE e.nombre LIKE :search OR e.apellido LIKE :search OR e.numero_documento LIKE :search
                ORDER BY m.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':search', $search);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener estadísticas
     */
    public function getStats() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendiente,
                    SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) as aprobada,
                    SUM(CASE WHEN estado = 'rechazada' THEN 1 ELSE 0 END) as rechazada
                  FROM {$this->table}";
        $stmt = $this->pdo->query($query);
        return $stmt->fetch();
    }
}

?>
