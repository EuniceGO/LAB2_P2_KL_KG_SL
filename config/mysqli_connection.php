<?php
/**
 * Configuración de conexión MySQLi
 * Para modelos que requieren MySQLi en lugar de PDO
 */

// Configuración de base de datos
$host = "localhost";
$usuario = "root";
$password = "";
$baseDatos = "productos_iniciales";

// Crear conexión MySQLi
$conn = new mysqli($host, $usuario, $password, $baseDatos);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Configurar charset
$conn->set_charset("utf8mb4");

?>