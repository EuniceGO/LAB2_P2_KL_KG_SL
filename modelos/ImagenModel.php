<?php
require_once 'config/cn.php';
require_once 'clases/Imagen.php';

class ImagenModel {
    private $cn;

    public function __construct() { $this->cn = new CNpdo(); }

    public function getAll() {
        $sql = "SELECT * FROM Imagenes_Productos";
        $results = $this->cn->consulta($sql);
        $imagenes = [];
        foreach ($results as $row) {
            $imagenes[] = new Imagen($row['id_imagen'], $row['id_producto'], $row['url_imagen'], $row['descripcion']);
        }
        return $imagenes;
    }

    public function insert($imagenObj) {
        $sql = "INSERT INTO Imagenes_Productos (id_producto, url_imagen, descripcion) VALUES (?, ?, ?)";
        $imagenObj->getUrlImagen();
        return $this->cn->ejecutar($sql, [$imagenObj->getIdProducto(), $imagenObj->getUrlImagen(), $imagenObj->getDescripcion()]);
    }
    public function getById($id) {
    $sql = "SELECT * FROM Imagenes_Productos WHERE id_imagen = ?";
    $results = $this->cn->consulta($sql, [$id]);
    if (!empty($results)) {
        $row = $results[0];
        return new Imagen($row['id_imagen'], $row['id_producto'], $row['url_imagen'], $row['descripcion']);
    }
    return null;
}

public function update($imagenObj) {
    $sql = "UPDATE Imagenes_Productos SET id_producto = ?, url_imagen = ?, descripcion = ? WHERE id_imagen = ?";
    return $this->cn->ejecutar($sql, [
        $imagenObj->getIdProducto(),
        $imagenObj->getUrlImagen(),
        $imagenObj->getDescripcion(),
        $imagenObj->getIdImagen()
    ]);
}

public function delete($imagenObj) {
    $sql = "DELETE FROM Imagenes_Productos WHERE id_imagen = ?";
    return $this->cn->ejecutar($sql, [$imagenObj->getIdImagen()]);
}

public function search($descripcion) {
    $sql = "SELECT * FROM Imagenes_Productos WHERE descripcion LIKE ?";
    $results = $this->cn->consulta($sql, ["%$descripcion%"]);
    $imagenes = [];
    foreach ($results as $row) {
        $imagenes[] = new Imagen($row['id_imagen'], $row['id_producto'], $row['url_imagen'], $row['descripcion']);
    }
    return $imagenes;
}
}

?>



