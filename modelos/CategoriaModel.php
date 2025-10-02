<?php
require_once __DIR__ . '/../config/cn.php';
require_once __DIR__ . '/../clases/Categoria.php';

class CategoriaModel {
    private $cn;

    public function __construct() { $this->cn = new CNpdo(); }

    public function getAll() {
        $sql = "SELECT * FROM Categorias";
        $results = $this->cn->consulta($sql);
        $categorias = [];
        foreach ($results as $row) {
            $categorias[] = new Categoria($row['id_categoria'], $row['nombre'], $row['descripcion']);
        }
        return $categorias;
    }

    public function insert($categoriaObj) {
        $sql = "INSERT INTO Categorias (nombre, descripcion) VALUES (?, ?)";
        return $this->cn->ejecutar($sql, [$categoriaObj->getNombre(), $categoriaObj->getDescripcion()]);
    }

    public function getById($id) {
        $sql = "SELECT * FROM Categorias WHERE id_categoria = ?";
        $results = $this->cn->consulta($sql, [$id]);
        if (!empty($results)) {
            $row = $results[0];
            return new Categoria($row['id_categoria'], $row['nombre'], $row['descripcion']);
        }
        return null;
    }

    public function update($categoriaObj) {
        $sql = "UPDATE Categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?";
        return $this->cn->ejecutar($sql, [
            $categoriaObj->getNombre(),
            $categoriaObj->getDescripcion(),
            $categoriaObj->getIdCategoria()
        ]);
    }

    public function delete($categoriaObj) {
        $sql = "DELETE FROM Categorias WHERE id_categoria = ?";
        return $this->cn->ejecutar($sql, [$categoriaObj->getIdCategoria()]);
    }
        
        public function getProductosByCategoria($id_categoria) {
            $sql = "SELECT * FROM Productos WHERE id_categoria = ?";
            $results = $this->cn->consulta($sql, [$id_categoria]);
            $productos = [];
            foreach ($results as $row) {
                $productos[] = $row; 
            }
            return $productos;
        }

        public function getCategoriaConMasProductos() {
    $sql = "SELECT c.id_categoria, c.nombre, COUNT(p.id_producto) AS total_productos
            FROM Categorias c
            LEFT JOIN Productos p ON c.id_categoria = p.id_categoria
            GROUP BY c.id_categoria, c.nombre
            ORDER BY total_productos DESC
            LIMIT 1";
    $results = $this->cn->consulta($sql);
    if (!empty($results)) {
        return $results[0]; // Devuelve la categoría con más productos
    }
    return null;
}

public function buscarPorNombre($nombre) {
    $sql = "SELECT * FROM Categorias WHERE nombre LIKE :nombre LIMIT 1";
    $params = [":nombre" => $nombre];
    $result = $this->cn->consulta($sql, $params);
    return $result ? $result[0] : null;
}

public function getCategoriaTop() {
    $sql = "SELECT c.id_categoria, c.nombre, COUNT(p.id_producto) AS total_productos
            FROM categorias c
            LEFT JOIN productos p ON c.id_categoria = p.id_categoria
            GROUP BY c.id_categoria, c.nombre
            ORDER BY total_productos DESC
            LIMIT 1";
    $result = $this->cn->consulta($sql);
    return !empty($result) ? $result[0] : null;
}

    // Obtener total de categorías
    public function getTotalCategorias() {
        $sql = "SELECT COUNT(*) as total FROM Categorias";
        $results = $this->cn->consulta($sql);
        return $results[0]['total'];
    }

}
?>
