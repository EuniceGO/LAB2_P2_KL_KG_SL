<?php
require_once __DIR__ . '/../config/cn.php';
require_once __DIR__ . '/../clases/Usuario.php';
require_once __DIR__ . '/../clases/Role.php';

class UsuarioModel {
    private $cn;

    public function __construct() { 
        $this->cn = new CNpdo(); 
    }

    // Obtener todos los usuarios con información del rol
    public function getAll() {
        $sql = "SELECT u.id_usuario, u.nombre, u.email, u.password, u.id_rol, r.nombre as nombre_rol
                FROM usuarios u
                LEFT JOIN roles r ON u.id_rol = r.id_rol
                ORDER BY u.id_usuario DESC";
        $results = $this->cn->consulta($sql);
        $usuarios = [];
        foreach ($results as $row) {
            $usuarios[] = new Usuario(
                $row['id_usuario'], 
                $row['nombre'], 
                $row['email'], 
                $row['password'], 
                $row['id_rol']
            );
        }
        return $usuarios;
    }

    // Obtener usuario por ID
    public function getById($id) {
        $sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
        $results = $this->cn->consulta($sql, [$id]);
        if (!empty($results)) {
            $row = $results[0];
            return new Usuario(
                $row['id_usuario'], 
                $row['nombre'], 
                $row['email'], 
                $row['password'], 
                $row['id_rol']
            );
        }
        return null;
    }

    // Obtener usuario por email
    public function getByEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $results = $this->cn->consulta($sql, [$email]);
        if (!empty($results)) {
            $row = $results[0];
            return new Usuario(
                $row['id_usuario'], 
                $row['nombre'], 
                $row['email'], 
                $row['password'], 
                $row['id_rol']
            );
        }
        return null;
    }

    // Insertar nuevo usuario
    public function insert($usuarioObj) {
        // Guardar contraseña sin hashear
        $sql = "INSERT INTO usuarios (nombre, email, password, id_rol) VALUES (?, ?, ?, ?)";
        return $this->cn->ejecutar($sql, [
            $usuarioObj->getNombre(), 
            $usuarioObj->getEmail(), 
            $usuarioObj->getPassword(), 
            $usuarioObj->getIdRol()
        ]);
    }

    // Actualizar usuario
    public function update($usuarioObj) {
        $sql = "UPDATE usuarios SET nombre = ?, email = ?, id_rol = ? WHERE id_usuario = ?";
        return $this->cn->ejecutar($sql, [
            $usuarioObj->getNombre(), 
            $usuarioObj->getEmail(), 
            $usuarioObj->getIdRol(), 
            $usuarioObj->getIdUsuario()
        ]);
    }

    // Actualizar contraseña del usuario
    public function updatePassword($idUsuario, $newPassword) {
        // Guardar contraseña sin hashear
        $sql = "UPDATE usuarios SET password = ? WHERE id_usuario = ?";
        return $this->cn->ejecutar($sql, [$newPassword, $idUsuario]);
    }

    // Eliminar usuario
    public function delete($id) {
        $sql = "DELETE FROM usuarios WHERE id_usuario = ?";
        return $this->cn->ejecutar($sql, [$id]);
    }

    // Autenticar usuario (Login)
    public function authenticate($email, $password) {
        $sql = "SELECT u.*, r.nombre as nombre_rol 
                FROM usuarios u
                LEFT JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.email = ?";
        $results = $this->cn->consulta($sql, [$email]);
        
        if (!empty($results)) {
            $row = $results[0];
            // Comparar contraseñas directamente (sin hash)
            if ($password === $row['password']) {
                return [
                    'usuario' => new Usuario(
                        $row['id_usuario'], 
                        $row['nombre'], 
                        $row['email'], 
                        $row['password'], 
                        $row['id_rol']
                    ),
                    'rol' => $row['nombre_rol']
                ];
            }
        }
        return false;
    }

    // Verificar si el email ya existe
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id_usuario != ?";
            $params[] = $excludeId;
        }
        
        $results = $this->cn->consulta($sql, $params);
        return $results[0]['total'] > 0;
    }

    // Buscar usuarios
    public function search($termino) {
        $sql = "SELECT u.id_usuario, u.nombre, u.email, u.password, u.id_rol, r.nombre as nombre_rol
                FROM usuarios u
                LEFT JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.nombre LIKE ? OR u.email LIKE ? OR r.nombre LIKE ?
                ORDER BY u.id_usuario DESC";
        $termino = "%$termino%";
        $results = $this->cn->consulta($sql, [$termino, $termino, $termino]);
        $usuarios = [];
        foreach ($results as $row) {
            $usuarios[] = new Usuario(
                $row['id_usuario'], 
                $row['nombre'], 
                $row['email'], 
                $row['password'], 
                $row['id_rol']
            );
        }
        return $usuarios;
    }

    // Obtener usuarios por rol
    public function getByRole($idRol) {
        $sql = "SELECT u.*, r.nombre as nombre_rol
                FROM usuarios u
                LEFT JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.id_rol = ?
                ORDER BY u.nombre ASC";
        $results = $this->cn->consulta($sql, [$idRol]);
        $usuarios = [];
        foreach ($results as $row) {
            $usuarios[] = new Usuario(
                $row['id_usuario'], 
                $row['nombre'], 
                $row['email'], 
                $row['password'], 
                $row['id_rol']
            );
        }
        return $usuarios;
    }

    // Contar total de usuarios
    public function getTotalUsuarios() {
        $sql = "SELECT COUNT(*) as total FROM usuarios";
        $results = $this->cn->consulta($sql);
        return $results[0]['total'];
    }

    // Obtener estadísticas de usuarios por rol
    public function getUsuariosPorRol() {
        $sql = "SELECT r.nombre as rol, COUNT(u.id_usuario) as total_usuarios
                FROM roles r
                LEFT JOIN usuarios u ON r.id_rol = u.id_rol
                GROUP BY r.id_rol, r.nombre
                ORDER BY total_usuarios DESC";
        return $this->cn->consulta($sql);
    }
}
?>