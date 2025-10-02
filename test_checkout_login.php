<?php
/**
 * Script de prueba para verificar el funcionamiento del checkout con cliente logueado
 */

session_start();

// Simular un cliente logueado
$_SESSION['user_id'] = 1;
$_SESSION['user_role_id'] = 2; // Cliente
$_SESSION['user_name'] = 'Cliente de Prueba';
$_SESSION['user_email'] = 'cliente@prueba.com';

echo "<h2>🧪 Prueba de Checkout con Cliente Logueado</h2>";

echo "<h3>Datos de Sesión Actual:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Incluir el modelo de cliente para probar
require_once 'modelos/ClienteModel.php';

try {
    $clienteModel = new ClienteModel();
    $cliente = $clienteModel->obtenerPorUsuario($_SESSION['user_id']);
    
    echo "<h3>Datos del Cliente desde BD:</h3>";
    if ($cliente) {
        echo "<pre>";
        print_r($cliente);
        echo "</pre>";
        
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 10px; margin: 10px 0;'>";
        echo "✅ <strong>Cliente encontrado en BD</strong><br>";
        echo "Nombre: " . htmlspecialchars($cliente['nombre'] ?? 'N/A') . "<br>";
        echo "Email: " . htmlspecialchars($cliente['email'] ?? 'N/A') . "<br>";
        echo "Teléfono: " . htmlspecialchars($cliente['telefono'] ?? 'N/A') . "<br>";
        echo "Dirección: " . htmlspecialchars($cliente['direccion'] ?? 'N/A') . "<br>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; margin: 10px 0;'>";
        echo "❌ <strong>Cliente no encontrado en BD</strong><br>";
        echo "Se usarán datos de sesión como fallback";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; margin: 10px 0;'>";
    echo "⚠️ <strong>Error al obtener cliente:</strong> " . $e->getMessage() . "<br>";
    echo "Se usarán datos de sesión como fallback";
    echo "</div>";
}

echo "<h3>Enlaces de Prueba:</h3>";
echo "<p><a href='?c=carrito&a=checkout' target='_blank'>🛒 Ir al Checkout</a></p>";
echo "<p><a href='?' target='_blank'>🏠 Ir al Inicio</a></p>";

echo "<h3>Instrucciones:</h3>";
echo "<ol>";
echo "<li>Agregue algunos productos al carrito</li>";
echo "<li>Vaya al checkout usando el enlace de arriba</li>";
echo "<li>Verifique que los campos estén pre-llenados con los datos del cliente</li>";
echo "<li>Si no hay datos en BD, debería mostrar los datos de sesión como fallback</li>";
echo "</ol>";

echo "<h3>Para limpiar la sesión:</h3>";
echo "<p><a href='?clear=1' style='color: red;'>🗑️ Limpiar Sesión</a></p>";

if (isset($_GET['clear'])) {
    session_destroy();
    echo "<div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 10px; margin: 10px 0;'>";
    echo "🧹 Sesión limpiada. <a href='test_checkout_login.php'>Recargar página</a>";
    echo "</div>";
}
?>