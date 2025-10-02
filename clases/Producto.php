<?php
class Producto {
    private $id_producto;
    private $nombre;
    private $precio;
    private $id_categoria;
    private $codigo_qr;
    private $imagen_url;

    public function __construct($id_producto, $nombre, $precio, $id_categoria, $codigo_qr = null, $imagen_url = null) {
        $this->id_producto = $id_producto;
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->id_categoria = $id_categoria;
        $this->codigo_qr = $codigo_qr;
        $this->imagen_url = $imagen_url;
    }

    // Getters
    public function getIdProducto() { return $this->id_producto; }
    public function getNombre() { return $this->nombre; }
    public function getPrecio() { return $this->precio; }
    public function getIdCategoria() { return $this->id_categoria; }
    public function getCodigoQr() { return $this->codigo_qr; }
    public function getImagenUrl() { return $this->imagen_url; }

    // Setters
    public function setIdProducto($id_producto) { $this->id_producto = $id_producto; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setPrecio($precio) { $this->precio = $precio; }
    public function setIdCategoria($id_categoria) { $this->id_categoria = $id_categoria; }
    public function setCodigoQr($codigo_qr) { $this->codigo_qr = $codigo_qr; }
    public function setImagenUrl($imagen_url) { $this->imagen_url = $imagen_url; }
}
?>
