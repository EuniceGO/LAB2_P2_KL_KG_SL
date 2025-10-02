<?php
class Usuario {
    private $id_usuario;
    private $nombre;
    private $email;
    private $password;
    private $id_rol;

    public function __construct($id_usuario, $nombre, $email, $password, $id_rol) {
        $this->id_usuario = $id_usuario;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
        $this->id_rol = $id_rol;
    }

    // Getters
    public function getIdUsuario() { 
        return $this->id_usuario; 
    }
    
    public function getNombre() { 
        return $this->nombre; 
    }
    
    public function getEmail() { 
        return $this->email; 
    }
    
    public function getPassword() { 
        return $this->password; 
    }
    
    public function getIdRol() { 
        return $this->id_rol; 
    }

    // Setters
    public function setIdUsuario($id_usuario) { 
        $this->id_usuario = $id_usuario; 
    }
    
    public function setNombre($nombre) { 
        $this->nombre = $nombre; 
    }
    
    public function setEmail($email) { 
        $this->email = $email; 
    }
    
    public function setPassword($password) { 
        $this->password = $password; 
    }
    
    public function setIdRol($id_rol) { 
        $this->id_rol = $id_rol; 
    }
}
?>