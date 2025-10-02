<?php
class Role {
    private $id_rol;
    private $nombre;
    private $descripcion;

    public function __construct($id_rol, $nombre, $descripcion) {
        $this->id_rol = $id_rol;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
    }

    // Getters
    public function getIdRol() { 
        return $this->id_rol; 
    }
    
    public function getNombre() { 
        return $this->nombre; 
    }
    
    public function getDescripcion() { 
        return $this->descripcion; 
    }

    // Setters
    public function setIdRol($id_rol) { 
        $this->id_rol = $id_rol; 
    }
    
    public function setNombre($nombre) { 
        $this->nombre = $nombre; 
    }
    
    public function setDescripcion($descripcion) { 
        $this->descripcion = $descripcion; 
    }
}
?>