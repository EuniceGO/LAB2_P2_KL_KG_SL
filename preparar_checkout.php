<?php
/**
 * Preparar carrito y ir directamente al checkout
 */

session_start();

// Asegurar que estemos logueados como cliente
if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 2) {
    $_SESSION['user_id'] = 2;
    $_SESSION['user_name'] = 'Cliente de Prueba';
    $_SESSION['user_email'] = 'cliente@test.com';
    $_SESSION['user_role_id'] = 2;
    $_SESSION['user_role'] = 'Cliente';
}

echo "<h2>🛒 Preparando Carrito para Checkout</h2>";

try {
    // Incluir clases necesarias
    require_once 'clases/Carrito.php';
    require_once 'clases/Producto.php';
    
    // Verificar si el carrito ya tiene productos
    $resumen = Carrito::obtenerResumen();
    
    if ($resumen['esta_vacio']) {
        echo "<div style='background: #fff3cd; padding: 15px; margin: 10px 0;'>";
        echo "⚠️ Carrito vacío. Agregando productos de prueba...";
        echo "</div>";
        
        // Agregar productos de prueba
        $producto1 = new Producto(1, "Laptop Gaming", 1299.99, 1);
        Carrito::agregarProducto($producto1, 1);
        
        $producto2 = new Producto(2, "Mouse Inalámbrico", 49.99, 1);
        Carrito::agregarProducto($producto2, 2);
        
        $producto3 = new Producto(3, "Teclado Mecánico", 89.99, 1);
        Carrito::agregarProducto($producto3, 1);
        
        echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0;'>";
        echo "✅ Productos agregados exitosamente al carrito";
        echo "</div>";
        
        // Verificar nuevamente
        $resumen = Carrito::obtenerResumen();
    }
    
    if (!$resumen['esta_vacio']) {
        echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0;'>";
        echo "🎉 <strong>Carrito listo con " . count($resumen['productos']) . " productos</strong><br>";
        echo "Total: $" . number_format($resumen['total'], 2);
        echo "</div>";
        
        echo "<h3>Productos en el carrito:</h3>";
        echo "<ul>";
        foreach ($resumen['productos'] as $producto) {
            echo "<li>" . htmlspecialchars($producto['nombre']) . " - Cantidad: " . $producto['cantidad'] . " - $" . number_format($producto['precio'] * $producto['cantidad'], 2) . "</li>";
        }
        echo "</ul>";
        
        echo "<div style='text-align: center; margin: 30px 0;'>";
        echo "<a href='?c=carrito&a=checkout' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 18px;'>";
        echo "🛒 IR AL CHECKOUT AHORA";
        echo "</a>";
        echo "</div>";
        
        echo "<p style='text-align: center;'><small>Los campos del formulario deberían estar pre-llenados con tus datos de cliente</small></p>";
        
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; margin: 10px 0;'>";
        echo "❌ Error: No se pudieron agregar productos al carrito";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; margin: 10px 0;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
}

echo "<h3>Información del usuario actual:</h3>";
echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
echo "<strong>Nombre:</strong> " . ($_SESSION['user_name'] ?? 'No definido') . "<br>";
echo "<strong>Email:</strong> " . ($_SESSION['user_email'] ?? 'No definido') . "<br>";
echo "<strong>Rol:</strong> " . ($_SESSION['user_role'] ?? 'No definido') . " (ID: " . ($_SESSION['user_role_id'] ?? 'No definido') . ")";
echo "</div>";

echo "<h3>Enlaces alternativos:</h3>";
echo "<p><a href='cambiar_tipo_usuario.php?tipo=cliente'>👤 Asegurar que soy cliente</a></p>";
echo "<p><a href='test_carrito_checkout.php'>🧪 Simulador completo</a></p>";
echo "<p><a href='?'>🏠 Página principal</a></p>";
?>

<style>
body { 
    font-family: Arial, sans-serif; 
    margin: 20px; 
    background: #f8f9fa;
}
h2, h3 { color: #333; }
a { text-decoration: none; }
a:hover { opacity: 0.8; }
</style>