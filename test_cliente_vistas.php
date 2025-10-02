<?php
session_start();
require_once 'config/cn.php';

echo "<h2>🧪 Prueba de Vista de Cliente</h2>";
echo "<h3>Estado Actual de la Sesión:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Simular login de cliente
if (!isset($_SESSION['user_id'])) {
    echo "<p>No hay sesión activa. Vamos a simular un cliente logueado...</p>";
    
    // Buscar un usuario cliente (role_id = 2)
    try {
        $cn = new CNpdo();
        $sql = "SELECT u.id_usuario, u.nombre, u.email, u.role_id, r.nombre as role_name 
                FROM Usuarios u 
                JOIN Roles r ON u.role_id = r.id_role 
                WHERE u.role_id = 2 
                LIMIT 1";
        $results = $cn->consulta($sql);
        
        if (!empty($results)) {
            $cliente = $results[0];
            $_SESSION['user_id'] = $cliente['id_usuario'];
            $_SESSION['user_name'] = $cliente['nombre'];
            $_SESSION['user_email'] = $cliente['email'];
            $_SESSION['user_role_id'] = $cliente['role_id'];
            $_SESSION['user_role_name'] = $cliente['role_name'];
            
            echo "<p>✅ Cliente simulado logueado:</p>";
            echo "<ul>";
            echo "<li>ID: " . $cliente['id_usuario'] . "</li>";
            echo "<li>Nombre: " . $cliente['nombre'] . "</li>";
            echo "<li>Email: " . $cliente['email'] . "</li>";
            echo "<li>Role ID: " . $cliente['role_id'] . " (Cliente)</li>";
            echo "</ul>";
        } else {
            echo "<p>❌ No se encontraron usuarios clientes (role_id = 2) en la base de datos</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Error al conectar a la base de datos: " . $e->getMessage() . "</p>";
    }
}

echo "<h3>Enlaces de Prueba:</h3>";
echo "<p><a href='?c=producto&a=catalogo' class='btn btn-primary'>🛍️ Ver Catálogo de Cliente</a></p>";
echo "<p><a href='?controller=usuario&action=dashboardCliente' class='btn btn-success'>📊 Ver Dashboard de Cliente</a></p>";
echo "<p><a href='?c=carrito&a=index' class='btn btn-info'>🛒 Ver Carrito</a></p>";
echo "<p><a href='index.php' class='btn btn-secondary'>🏠 Volver al Inicio</a></p>";

echo "<h3>Simulación de Admin:</h3>";
echo "<p><a href='?admin=1' class='btn btn-warning'>👨‍💼 Simular Admin</a></p>";

// Simular admin si se solicita
if (isset($_GET['admin'])) {
    try {
        $cn = new CNpdo();
        $sql = "SELECT u.id_usuario, u.nombre, u.email, u.role_id, r.nombre as role_name 
                FROM Usuarios u 
                JOIN Roles r ON u.role_id = r.id_role 
                WHERE u.role_id = 1 
                LIMIT 1";
        $results = $cn->consulta($sql);
        
        if (!empty($results)) {
            $admin = $results[0];
            $_SESSION['user_id'] = $admin['id_usuario'];
            $_SESSION['user_name'] = $admin['nombre'];
            $_SESSION['user_email'] = $admin['email'];
            $_SESSION['user_role_id'] = $admin['role_id'];
            $_SESSION['user_role_name'] = $admin['role_name'];
            
            echo "<p>✅ Admin simulado logueado:</p>";
            echo "<ul>";
            echo "<li>ID: " . $admin['id_usuario'] . "</li>";
            echo "<li>Nombre: " . $admin['nombre'] . "</li>";
            echo "<li>Email: " . $admin['email'] . "</li>";
            echo "<li>Role ID: " . $admin['role_id'] . " (Admin)</li>";
            echo "</ul>";
            
            echo "<p><a href='index.php' class='btn btn-success'>🏠 Ver como Admin</a></p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    }
}
?>