<?php
class Imagen {
    private $id_imagen;
    private $id_producto;
    private $url_imagen;
    private $descripcion;

    public function __construct($id_imagen, $id_producto, $url_imagen, $descripcion) {
        $this->id_imagen = $id_imagen;
        $this->id_producto = $id_producto;
        $this->url_imagen = $url_imagen;
        $this->descripcion = $descripcion;
    }

    public function getIdImagen() { return $this->id_imagen; }
    public function getIdProducto() { return $this->id_producto; }
    public function getUrlImagen() { return $this->url_imagen; }
    public function getDescripcion() { return $this->descripcion; }
}
?>
