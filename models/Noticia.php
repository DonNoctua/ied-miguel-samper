<?php
/**
 * MODELO: NOTICIA
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';

class Noticia {
    private $pdo;
    private $table = 'noticias';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Crear noticia
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (titulo, descripcion, contenido, autor_id, imagen, estado, fecha_publicacion, destacado)
                  VALUES 
                  (:titulo, :descripcion, :contenido, :autor_id, :imagen, :estado, :fecha_publicacion, :destacado)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':titulo' => $data['titulo'],
            ':descripcion' => $data['descripcion'],
            ':contenido' => $data['contenido'] ?? '',
            ':autor_id' => $data['autor_id'],
            ':imagen' => $data['imagen'] ?? null,
            ':estado' => $data['estado'] ?? 'borrador',
            ':fecha_publicacion' => $data['estado'] === 'publicado' ? date('Y-m-d') : null,
            ':destacado' => $data['destacado'] ?? 0
        ]);
    }

    /**
     * Obtener noticia por ID
     */
    public function getById($id) {
        $query = "SELECT n.*, u.nombre as autor_nombre, u.apellido as autor_apellido
                  FROM {$this->table} n
                  LEFT JOIN usuarios u ON n.autor_id = u.id
                  WHERE n.id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtener todas las noticias
     */
    public function getAll($page = 1, $limit = 10, $estado = null) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT n.*, u.nombre as autor_nombre, u.apellido as autor_apellido
                  FROM {$this->table} n
                  LEFT JOIN usuarios u ON n.autor_id = u.id";

        if ($estado) {
            $query .= " WHERE n.estado = :estado";
        } else {
            $query .= " WHERE n.estado = 'publicado'";
        }

        $query .= " ORDER BY n.destacado DESC, n.fecha_publicacion DESC LIMIT :limit OFFSET :offset";
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
     * Actualizar noticia
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
     * Buscar
     */
    public function search($query, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $search = "%$query%";
        $sql = "SELECT n.*, u.nombre as autor_nombre, u.apellido as autor_apellido
                FROM {$this->table} n
                LEFT JOIN usuarios u ON n.autor_id = u.id
                WHERE n.titulo LIKE :search OR n.descripcion LIKE :search
                ORDER BY n.fecha_publicacion DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':search', $search);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Incrementar vistas
     */
    public function incrementViews($id) {
        $query = "UPDATE {$this->table} SET vistas = vistas + 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Obtener noticias destacadas
     */
    public function getFeatured($limit = 5) {
        $query = "SELECT n.*, u.nombre as autor_nombre, u.apellido as autor_apellido
                  FROM {$this->table} n
                  LEFT JOIN usuarios u ON n.autor_id = u.id
                  WHERE n.estado = 'publicado' AND n.destacado = 1
                  ORDER BY n.fecha_publicacion DESC
                  LIMIT :limit";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

?>
