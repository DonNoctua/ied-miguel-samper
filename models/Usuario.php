<?php
/**
 * MODELO: USUARIO
 * IED Miguel Samper Agudelo
 */

require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $pdo;
    private $table = 'usuarios';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtener usuario por email
     */
    public function getByEmail($email) {
        $query = "SELECT u.*, r.nombre as rol_nombre FROM {$this->table} u
                  JOIN roles r ON u.rol_id = r.id
                  WHERE u.email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Obtener usuario por ID
     */
    public function getById($id) {
        $query = "SELECT u.*, r.nombre as rol_nombre FROM {$this->table} u
                  JOIN roles r ON u.rol_id = r.id
                  WHERE u.id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Crear usuario
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} (nombre, apellido, email, cedula, telefono, password, rol_id, estado)
                  VALUES (:nombre, :apellido, :email, :cedula, :telefono, :password, :rol_id, :estado)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':email' => $data['email'],
            ':cedula' => $data['cedula'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':password' => hashPassword($data['password']),
            ':rol_id' => $data['rol_id'],
            ':estado' => 'activo'
        ]);
    }

    /**
     * Actualizar usuario
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
     * Obtener todos los usuarios
     */
    public function getAll($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT u.*, r.nombre as rol_nombre FROM {$this->table} u
                  JOIN roles r ON u.rol_id = r.id
                  ORDER BY u.created_at DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Contar total de usuarios
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->pdo->query($query);
        return $stmt->fetch()['total'];
    }

    /**
     * Eliminar usuario
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword($id, $new_password) {
        $query = "UPDATE {$this->table} SET password = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':password' => hashPassword($new_password),
            ':id' => $id
        ]);
    }

    /**
     * Actualizar último acceso
     */
    public function updateLastAccess($id) {
        $query = "UPDATE {$this->table} SET ultimo_acceso = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Guardar token de recordar sesión
     */
    public function saveRememberToken($id, $token) {
        $expiry = date('Y-m-d H:i:s', time() + REMEMBER_DURATION);
        $query = "UPDATE {$this->table} SET recordar_token = :token, token_expira = :expiry WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':token' => $token,
            ':expiry' => $expiry,
            ':id' => $id
        ]);
    }

    /**
     * Obtener por token de recordar
     */
    public function getByRememberToken($token) {
        $query = "SELECT u.*, r.nombre as rol_nombre FROM {$this->table} u
                  JOIN roles r ON u.rol_id = r.id
                  WHERE u.recordar_token = :token AND u.token_expira > NOW()";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch();
    }

    /**
     * Buscar usuarios
     */
    public function search($query, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $search = "%$query%";
        $sql = "SELECT u.*, r.nombre as rol_nombre FROM {$this->table} u
                JOIN roles r ON u.rol_id = r.id
                WHERE u.nombre LIKE :search OR u.apellido LIKE :search OR u.email LIKE :search
                ORDER BY u.created_at DESC
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
