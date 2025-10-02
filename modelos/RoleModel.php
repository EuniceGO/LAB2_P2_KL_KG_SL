<?php
require_once __DIR__ . '/../config/cn.php';
require_once __DIR__ . '/../clases/Role.php';

class RoleModel {
    private $cn;

    public function __construct() { 
        $this->cn = new CNpdo(); 
    }

    // Obtener todos los roles
    public function getAll() {
        $sql = "SELECT * FROM roles ORDER BY id_rol ASC";
        $results = $this->cn->consulta($sql);
        $roles = [];
        foreach ($results as $row) {
            $roles[] = new Role($row['id_rol'], $row['nombre'], $row['descripcion']);
        }
        return $roles;
    }

    // Obtener rol por ID
    public function getById($id) {
        $sql = "SELECT * FROM roles WHERE id_rol = ?";
        $results = $this->cn->consulta($sql, [$id]);
        if (!empty($results)) {
            $row = $results[0];
            return new Role($row['id_rol'], $row['nombre'], $row['descripcion']);
        }
        return null;
    }

    // Obtener rol por nombre
    public function getByNombre($nombre) {
        $sql = "SELECT * FROM roles WHERE nombre = ?";
        $results = $this->cn->consulta($sql, [$nombre]);
        if (!empty($results)) {
            $row = $results[0];
            return new Role($row['id_rol'], $row['nombre'], $row['descripcion']);
        }
        return null;
    }

    // Insertar nuevo rol
    public function insert($roleObj) {
        $sql = "INSERT INTO roles (nombre, descripcion) VALUES (?, ?)";
        return $this->cn->ejecutar($sql, [$roleObj->getNombre(), $roleObj->getDescripcion()]);
    }

    // Actualizar rol
    public function update($roleObj) {
        $sql = "UPDATE roles SET nombre = ?, descripcion = ? WHERE id_rol = ?";
        return $this->cn->ejecutar($sql, [
            $roleObj->getNombre(), 
            $roleObj->getDescripcion(), 
            $roleObj->getIdRol()
        ]);
    }

    // Eliminar rol
    public function delete($id) {
        $sql = "DELETE FROM roles WHERE id_rol = ?";
        return $this->cn->ejecutar($sql, [$id]);
    }

    // Verificar si el rol puede ser eliminado (no tiene usuarios asociados)
    public function canDelete($id) {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE id_rol = ?";
        $results = $this->cn->consulta($sql, [$id]);
        return $results[0]['total'] == 0;
    }

    // Buscar roles por nombre
    public function search($termino) {
        $sql = "SELECT * FROM roles WHERE nombre LIKE ? OR descripcion LIKE ? ORDER BY id_rol ASC";
        $termino = "%$termino%";
        $results = $this->cn->consulta($sql, [$termino, $termino]);
        $roles = [];
        foreach ($results as $row) {
            $roles[] = new Role($row['id_rol'], $row['nombre'], $row['descripcion']);
        }
        return $roles;
    }
}
?>