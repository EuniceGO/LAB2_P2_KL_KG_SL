<?php
/**
 * Simulador de login como cliente para probar el checkout
 */

session_start();

echo "<h2>🔄 Cambiar Tipo de Usuario para Pruebas</h2>";

if (isset($_GET['tipo'])) {
    if ($_GET['tipo'] === 'cliente') {
        // Simular login como cliente
        $_SESSION['user_id'] = 2;
        $_SESSION['user_name'] = 'Cliente de Prueba';
        $_SESSION['user_email'] = 'cliente@prueba.com';
        $_SESSION['user_role_id'] = 2;
        $_SESSION['user_role'] = 'Cliente';
        
        echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "✅ <strong>Ahora estás logueado como CLIENTE</strong><br>";
        echo "Nombre: " . $_SESSION['user_name'] . "<br>";
        echo "Email: " . $_SESSION['user_email'] . "<br>";
        echo "Rol: " . $_SESSION['user_role'] . " (ID: " . $_SESSION['user_role_id'] . ")";
        echo "</div>";
        
    } elseif ($_GET['tipo'] === 'admin') {
        // Volver al admin original
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Admin Principal';
        $_SESSION['user_email'] = 'admin@sistema.com';
        $_SESSION['user_role_id'] = 1;
        $_SESSION['user_role'] = 'Administrador';
        
        echo "<div style='background: #f8d7da; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "🔙 <strong>Volviste a ser ADMINISTRADOR</strong><br>";
        echo "Nombre: " . $_SESSION['user_name'] . "<br>";
        echo "Email: " . $_SESSION['user_email'] . "<br>";
        echo "Rol: " . $_SESSION['user_role'] . " (ID: " . $_SESSION['user_role_id'] . ")";
        echo "</div>";
    }
}

echo "<h3>Estado Actual:</h3>";
echo "<div style='background: #e2e3e5; padding: 10px; border-radius: 5px;'>";
echo "<strong>Usuario:</strong> " . ($_SESSION['user_name'] ?? 'No logueado') . "<br>";
echo "<strong>Email:</strong> " . ($_SESSION['user_email'] ?? 'No definido') . "<br>";
echo "<strong>Rol:</strong> " . ($_SESSION['user_role'] ?? 'No definido') . " (ID: " . ($_SESSION['user_role_id'] ?? 'No definido') . ")<br>";

if (isset($_SESSION['user_role_id'])) {
    if ($_SESSION['user_role_id'] == 2) {
        echo "<span style='color: green;'>✅ Puede ver datos pre-llenados en checkout</span>";
    } else {
        echo "<span style='color: red;'>❌ No verá datos pre-llenados (solo clientes role_id=2)</span>";
    }
}
echo "</div>";

echo "<h3>Cambiar Tipo de Usuario:</h3>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='?tipo=cliente' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>👤 Cambiar a CLIENTE</a>";
echo "<a href='?tipo=admin' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>👨‍💼 Cambiar a ADMIN</a>";
echo "</div>";

echo "<h3>Pasos para Probar el Checkout:</h3>";
echo "<ol>";
echo "<li><strong>Cambiar a CLIENTE</strong> usando el botón de arriba</li>";
echo "<li><a href='test_carrito_checkout.php?agregar_productos=1'>➕ Agregar productos al carrito</a></li>";
echo "<li><a href='?c=carrito&a=checkout' target='_blank'>🛒 Ir al Checkout</a></li>";
echo "<li>Verificar que los campos estén pre-llenados automáticamente</li>";
echo "</ol>";

echo "<h3>Enlaces Útiles:</h3>";
echo "<p><a href='debug_checkout_datos.php'>🔍 Debug completo</a></p>";
echo "<p><a href='test_carrito_checkout.php'>🛒 Simulador de carrito</a></p>";
echo "<p><a href='?'>🏠 Página principal</a></p>";

// Mostrar información de la sesión para debug
echo "<h3>Datos de Sesión Completos:</h3>";
echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
print_r($_SESSION);
echo "</pre>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #333; }
a { text-decoration: none; }
a:hover { opacity: 0.8; }
</style>