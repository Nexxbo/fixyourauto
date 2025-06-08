<?php
class UsuarioModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getUsuarioByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUsuarioById($id) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrarUsuario($nombre, $email, $password) {
        try {
            $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)");
            $stmt->execute([
                'nombre' => $nombre,
                'email' => $email,
                'password' => $password
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Database error in registrarUsuario: " . $e->getMessage());
            return false;
        }
    }

    public function updateNombreUsuario($id, $nombre) {
        try {
            $stmt = $this->db->prepare("UPDATE usuarios SET nombre = :nombre WHERE id = :id");
            $stmt->execute([
                'id' => $id,
                'nombre' => $nombre
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Database error in updateNombreUsuario: " . $e->getMessage());
            return false;
        }
    }

    public function updatePasswordUsuario($id, $password) {
        try {
            $stmt = $this->db->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
            $stmt->execute([
                'id' => $id,
                'password' => $password
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Database error in updatePasswordUsuario: " . $e->getMessage());
            return false;
        }
    }

    public function deleteUsuario($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return true;
        } catch (PDOException $e) {
            error_log("Database error in deleteUsuario: " . $e->getMessage());
            return false;
        }
    }

    public function guardarPiezaGuardada($usuario_id, $pieza_id, $cantidad) {
        try {
            $stmt = $this->db->prepare("INSERT INTO guardados (usuario_id, pieza_id, cantidad) VALUES (:usuario_id, :pieza_id, :cantidad)");
            $stmt->execute([
                'usuario_id' => $usuario_id,
                'pieza_id' => $pieza_id,
                'cantidad' => $cantidad
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Database error in guardarPiezaGuardada: " . $e->getMessage());
            return false;
        }
    }

    public function getPiezasGuardadasPorUsuario($usuario_id) {
        try {
            $stmt = $this->db->prepare("SELECT guardados.*, piezas.*
                                         FROM guardados
                                         INNER JOIN piezas ON guardados.pieza_id = piezas.id
                                         WHERE guardados.usuario_id = :usuario_id");
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getPiezasGuardadasPorUsuario: " . $e->getMessage());
            return false;
        }
    }
    public function deletePiezaGuardada($guardado_id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM guardados WHERE id = :guardado_id");
            $stmt->bindParam(':guardado_id', $guardado_id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Database error in deletePiezaGuardada: " . $e->getMessage());
            return false;
        }
    }
}
?>