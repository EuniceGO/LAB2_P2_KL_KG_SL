<?php
/**
 * Script para simular productos en el carrito y probar el checkout
 */

session_start();

// Incluir clase Carrito
require_once 'clases/Carrito.php';

echo "<h2>🛒 Simulador de Carrito para Prueba de Checkout</h2>";

// Simular cliente logueado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role_id'] = 2; // Cliente
    $_SESSION['user_name'] = 'Cliente de Prueba';
    $_SESSION['user_email'] = 'cliente@prueba.com';
    echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0;'>✅ Cliente logueado automáticamente</div>";
}

// Acción para agregar productos de prueba
if (isset($_GET['agregar_productos'])) {
    try {
        // Crear productos de prueba directamente
        require_once 'clases/Producto.php';
        
        // Producto 1
        $producto1 = new Producto(1, "Producto de Prueba 1", 25.99, 1);
        Carrito::agregarProducto($producto1, 2);
        
        // Producto 2  
        $producto2 = new Producto(2, "Producto de Prueba 2", 15.50, 1);
        Carrito::agregarProducto($producto2, 1);
        
        // Producto 3
        $producto3 = new Producto(3, "Producto de Prueba 3", 35.75, 2);
        Carrito::agregarProducto($producto3, 3);
        
        echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0;'>✅ Productos de prueba agregados al carrito</div>";
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0;'>❌ Error: " . $e->getMessage() . "</div>";
    }
}

// Acción para limpiar carrito
if (isset($_GET['limpiar_carrito'])) {
    try {
        Carrito::vaciarCarrito();
        echo "<div style='background: #d1ecf1; padding: 10px; margin: 10px 0;'>🧹 Carrito limpiado</div>";
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0;'>❌ Error: " . $e->getMessage() . "</div>";
    }
}

// Mostrar estado actual del carrito
try {
    $resumen = Carrito::obtenerResumen();
    
    echo "<h3>Estado del Carrito:</h3>";
    if ($resumen['esta_vacio']) {
        echo "<div style='background: #fff3cd; padding: 10px; margin: 10px 0;'>⚠️ El carrito está vacío</div>";
    } else {
        echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0;'>";
        echo "✅ Carrito con " . count($resumen['productos']) . " productos<br>";
        echo "Total: $" . number_format($resumen['total'], 2);
        echo "</div>";
        
        echo "<h4>Productos en el carrito:</h4>";
        echo "<ul>";
        foreach ($resumen['productos'] as $producto) {
            echo "<li>" . htmlspecialchars($producto['nombre']) . " - Cantidad: " . $producto['cantidad'] . " - $" . number_format($producto['precio'] * $producto['cantidad'], 2) . "</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0;'>❌ Error al obtener resumen: " . $e->getMessage() . "</div>";
}

echo "<h3>Acciones:</h3>";
echo "<p><a href='?agregar_productos=1'>➕ Agregar Productos de Prueba al Carrito</a></p>";
echo "<p><a href='?limpiar_carrito=1'>🗑️ Limpiar Carrito</a></p>";

echo "<h3>Ir al Checkout:</h3>";
echo "<p><a href='?c=carrito&a=checkout' target='_blank' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🛒 Ir al Checkout</a></p>";

echo "<h3>Datos de Sesión:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Enlaces Útiles:</h3>";
echo "<p><a href='test_checkout_login.php'>🧪 Prueba de Login para Checkout</a></p>";
echo "<p><a href='?'>🏠 Página Principal</a></p>";
?>