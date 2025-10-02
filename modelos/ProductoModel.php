<?php
require_once __DIR__ . '/../config/cn.php';
require_once __DIR__ . '/../clases/Producto.php';
require_once __DIR__ . '/../clases/QRCodeGenerator.php';

class ProductoModel {
    private $cn;

    public function __construct() { 
        $this->cn = new CNpdo(); 
    }

    // Obtener todos los productos sin joins
    public function getAlll() {
        $sql = "SELECT * FROM Productos";
        $results = $this->cn->consulta($sql);
        $productos = [];
        foreach ($results as $row) {
            $producto = new Producto(
                $row['id_producto'], 
                $row['nombre'], 
                $row['precio'], 
                $row['id_categoria'],
                $row['codigo_qr'] ?? null
            );
            $producto->setImagenUrl($row['imagen_url'] ?? null);
            $productos[] = $producto;
        }
        return $productos;
    }
    
    // Obtener todos los productos junto con el nombre de la categoría
    public function getAll() {
        $sql = "SELECT p.id_producto, p.nombre AS nombre_producto, p.precio, p.id_categoria, p.codigo_qr, p.imagen_url, c.nombre AS nombre_categoria
                FROM Productos p
                LEFT JOIN Categorias c ON p.id_categoria = c.id_categoria
                ORDER BY p.id_producto DESC";
        $results = $this->cn->consulta($sql);
        $productos = [];
        foreach ($results as $row) {
            $producto = new Producto(
                $row['id_producto'], 
                $row['nombre_producto'], 
                $row['precio'], 
                $row['id_categoria'],
                $row['codigo_qr'],
                $row['imagen_url']
            );
            $productos[] = $producto;
        }
        return $productos;
    }

    // Insertar nuevo producto y generar QR
    public function insert($productoObj) {
        // Primero insertar el producto con imagen_url
        $sql = "INSERT INTO Productos (nombre, precio, id_categoria, imagen_url) VALUES (?, ?, ?, ?)";
        $result = $this->cn->ejecutar($sql, [
            $productoObj->getNombre(), 
            $productoObj->getPrecio(), 
            $productoObj->getIdCategoria(),
            $productoObj->getImagenUrl()
        ]);
        
        if ($result) {
            // Obtener el ID del producto recién insertado
            $lastId = $this->cn->getConexion()->lastInsertId();
            
            // Crear objeto producto con el ID para generar QR
            $productoCompleto = new Producto(
                $lastId,
                $productoObj->getNombre(),
                $productoObj->getPrecio(),
                $productoObj->getIdCategoria(),
                null,
                $productoObj->getImagenUrl()
            );
            
            // Generar código QR
            $qrPath = QRCodeGenerator::generateAndSaveProductQR($productoCompleto);
            
            if ($qrPath) {
                // Actualizar el producto con la ruta del QR
                $this->updateQRCode($lastId, $qrPath);
                $productoObj->setCodigoQr($qrPath);
            }
            
            return $lastId;
        }
        
        return false;
    }

    // Obtener producto por ID
    public function getById($id) {
        $sql = "SELECT * FROM Productos WHERE id_producto = ?";
        $results = $this->cn->consulta($sql, [$id]);
        if (!empty($results)) {
            $row = $results[0];
            return new Producto(
                $row['id_producto'], 
                $row['nombre'], 
                $row['precio'], 
                $row['id_categoria'],
                $row['codigo_qr'] ?? null,
                $row['imagen_url'] ?? null
            );
        }
        return null;
    }

    // Actualizar producto
    public function update($productoObj) {
        $sql = "UPDATE Productos SET nombre = ?, precio = ?, id_categoria = ?, codigo_qr = ?, imagen_url = ? WHERE id_producto = ?";
        return $this->cn->ejecutar($sql, [
            $productoObj->getNombre(),
            $productoObj->getPrecio(),
            $productoObj->getIdCategoria(),
            $productoObj->getCodigoQr(),
            $productoObj->getImagenUrl(),
            $productoObj->getIdProducto()
        ]);
    }

    // Actualizar solo el código QR
    public function updateQRCode($idProducto, $codigoQr) {
        $sql = "UPDATE Productos SET codigo_qr = ? WHERE id_producto = ?";
        return $this->cn->ejecutar($sql, [$codigoQr, $idProducto]);
    }

    // Regenerar código QR para un producto existente
    public function regenerateQR($idProducto) {
        $producto = $this->getById($idProducto);
        if ($producto) {
            $qrPath = QRCodeGenerator::generateAndSaveProductQR($producto);
            if ($qrPath) {
                $this->updateQRCode($idProducto, $qrPath);
                return $qrPath;
            }
        }
        return false;
    }

    // Eliminar producto
    public function delete($productoObj) {
        // Eliminar archivo QR si existe
        if ($productoObj->getCodigoQr() && file_exists($productoObj->getCodigoQr())) {
            unlink($productoObj->getCodigoQr());
        }
        
        $sql = "DELETE FROM Productos WHERE id_producto = ?";
        return $this->cn->ejecutar($sql, [$productoObj->getIdProducto()]);
    }

    // Buscar productos por nombre
    public function getProductosByNombre($nombreProducto) {
        $sql = "SELECT p.nombre, i.url_imagen, p.codigo_qr FROM productos p LEFT JOIN imagenes_productos i ON p.id_producto = i.id_producto WHERE p.nombre = ?";
        $results = $this->cn->consulta($sql, [$nombreProducto]);
        $imagenes = [];
        foreach ($results as $row) {
            if (!empty($row['url_imagen'])) {
                $imagenes[] = [
                    'nombre' => $row['nombre'],
                    'url_imagen' => $row['url_imagen'],
                    'codigo_qr' => $row['codigo_qr']
                ];
            }
        }
        return $imagenes;
    }

    // Buscar productos
    public function search($termino) {
        $sql = "SELECT p.id_producto, p.nombre AS nombre_producto, p.precio, p.id_categoria, p.codigo_qr, p.imagen_url, c.nombre AS nombre_categoria
                FROM Productos p
                LEFT JOIN Categorias c ON p.id_categoria = c.id_categoria
                WHERE p.nombre LIKE ? OR c.nombre LIKE ?
                ORDER BY p.id_producto DESC";
        $termino = "%$termino%";
        $results = $this->cn->consulta($sql, [$termino, $termino]);
        $productos = [];
        foreach ($results as $row) {
            $productos[] = new Producto(
                $row['id_producto'],
                $row['nombre_producto'],
                $row['precio'],
                $row['id_categoria'],
                $row['codigo_qr'],
                $row['imagen_url']
            );
        }
        return $productos;
    }

    // Obtener total de productos
    public function getTotalProductos() {
        $sql = "SELECT COUNT(*) as total FROM Productos";
        $results = $this->cn->consulta($sql);
        return $results[0]['total'];
    }

    // Obtener productos por categoría
    public function getProductosPorCategoria() {
        $sql = "SELECT c.nombre as categoria, COUNT(p.id_producto) as total_productos
                FROM Categorias c
                LEFT JOIN Productos p ON c.id_categoria = p.id_categoria
                GROUP BY c.id_categoria, c.nombre
                ORDER BY total_productos DESC";
        return $this->cn->consulta($sql);
    }
    
    // Obtener productos filtrados por categoría específica
    public function getByCategoria($id_categoria) {
        $sql = "SELECT p.id_producto, p.nombre AS nombre_producto, p.precio, p.id_categoria, p.codigo_qr, p.imagen_url, c.nombre AS nombre_categoria
                FROM Productos p
                LEFT JOIN Categorias c ON p.id_categoria = c.id_categoria
                WHERE p.id_categoria = :id_categoria
                ORDER BY p.nombre ASC";
        $results = $this->cn->consulta($sql, [':id_categoria' => $id_categoria]);
        $productos = [];
        foreach ($results as $row) {
            $producto = new Producto(
                $row['id_producto'], 
                $row['nombre_producto'], 
                $row['precio'], 
                $row['id_categoria'],
                $row['codigo_qr'],
                $row['imagen_url']
            );
            $productos[] = $producto;
        }
        return $productos;
    }
}
?>