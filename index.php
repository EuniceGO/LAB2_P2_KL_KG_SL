<?php
require_once 'rutas.php';

// Parámetros de navegación - soportar tanto 'c' como 'controller'
$c = $_GET['c'] ?? $_GET['controller'] ?? 'categoria';
$a = $_GET['a'] ?? $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null; // obtenemos el ID si existe

// Cargar controlador según rutas.php
$archivo = Contenido::obtenerContenido($c);

if (file_exists($archivo)) {
    require_once $archivo;
    $controllerName = ucfirst($c) . "Controller";
    $controller = new $controllerName();

    if (method_exists($controller, $a)) {
        // Si existe ID, pasarlo al método
        if ($id !== null) {
            $controller->{$a}($id);
        } else {
            $controller->{$a}(); // métodos sin parámetros
        }
    } else {
        include 'layout/404.php';
    }
} else {
    include 'layout/404.php';
}

?>

