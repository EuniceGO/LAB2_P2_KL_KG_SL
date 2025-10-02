<?php
class Contenido {
    public static $contenido = [        
        "categoria" => "controladores/CategoriaController.php",
        "producto"  => "controladores/ProductoController.php",
        "imagen"    => "controladores/ImagenController.php",
        "usuario"   => "controladores/UsuarioController.php",
        "role"      => "controladores/RoleController.php"
    ];    

    public static function obtenerContenido($clave) {
        $vista=self::$contenido[$clave] ?? null;
        return $vista ?: "vistas/404.php";                
    }
}
?>
