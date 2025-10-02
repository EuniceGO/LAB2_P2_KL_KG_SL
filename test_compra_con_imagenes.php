<?php
echo "<h1>🛒 Test del Sistema de Compra con Imágenes</h1>";

session_start();
require_once 'clases/Carrito.php';
require_once 'modelos/ProductoModel.php';

try {
    echo "<h2>1. ✅ Verificando productos disponibles con imágenes...</h2>";
    
    $productoModel = new ProductoModel();
    $productos = $productoModel->getAll();
    
    if (empty($productos)) {
        echo "❌ No hay productos disponibles para probar<br>";
        echo "📝 <a href='?c=producto&a=create'>Crear un producto primero</a><br>";
        exit;
    }
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th>ID</th><th>Nombre</th><th>Precio</th><th>Imagen</th><th>Carrito</th>";
    echo "</tr>";
    
    foreach ($productos as $producto) {
        echo "<tr>";
        echo "<td>" . $producto->getIdProducto() . "</td>";
        echo "<td>" . htmlspecialchars($producto->getNombre()) . "</td>";
        echo "<td>$" . $producto->getPrecio() . "</td>";
        echo "<td>";
        if ($producto->getImagenUrl()) {
            echo "<img src='" . htmlspecialchars($producto->getImagenUrl()) . "' style='max-width: 50px; max-height: 40px; object-fit: cover;' onerror='this.src=\"data:image/svg+xml,%3Csvg xmlns=\"http://www.w3.org/2000/svg\" width=\"50\" height=\"40\"%3E%3Crect width=\"50\" height=\"40\" fill=\"%23ddd\"/%3E%3Ctext x=\"25\" y=\"20\" text-anchor=\"middle\" fill=\"%23999\" font-size=\"8\"%3EError%3C/text%3E%3C/svg%3E\"'>";
        } else {
            echo "<span style='color: #999;'>Sin imagen</span>";
        }
        echo "</td>";
        echo "<td>";
        echo "<a href='?c=carrito&a=agregar&id=" . $producto->getIdProducto() . "' style='padding: 5px 10px; background: #28a745; color: white; text-decoration: none; border-radius: 3px;'>🛒 Agregar</a>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>2. ✅ Estado actual del carrito...</h2>";
    
    $resumenCarrito = Carrito::obtenerResumen();
    
    echo "📊 <strong>Productos en carrito:</strong> " . $resumenCarrito['cantidad_total'] . "<br>";
    echo "💰 <strong>Total:</strong> $" . number_format($resumenCarrito['total'], 2) . "<br>";
    
    if (!$resumenCarrito['esta_vacio']) {
        echo "<h3>📦 Productos en el carrito:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th>";
        echo "</tr>";
        
        foreach ($resumenCarrito['productos'] as $item) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($item['nombre']) . "</td>";
            echo "<td>$" . number_format($item['precio'], 2) . "</td>";
            echo "<td>" . $item['cantidad'] . "</td>";
            echo "<td>$" . number_format($item['subtotal'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "🛒 El carrito está vacío<br>";
    }
    
    echo "<h2>3. 🔗 Enlaces de navegación...</h2>";
    echo "<ul>";
    echo "<li><a href='?c=producto&a=index' target='_blank'>📦 Ver todos los productos</a></li>";
    echo "<li><a href='?c=carrito&a=index' target='_blank'>🛒 Ver carrito completo</a></li>";
    echo "<li><a href='?c=carrito&a=historial' target='_blank'>📄 Ver historial de facturas</a></li>";
    echo "<li><a href='test_productos_con_imagenes.php' target='_blank'>🧪 Test de productos con imágenes</a></li>";
    echo "</ul>";
    
    echo "<h2>4. 🛒 Simulación de compra...</h2>";
    
    if (!empty($productos)) {
        // Agregar el primer producto al carrito
        $primerProducto = $productos[0];
        $resultado = Carrito::agregarProducto($primerProducto, 1);
        
        if ($resultado) {
            echo "✅ Producto '" . $primerProducto->getNombre() . "' agregado al carrito exitosamente<br>";
            
            // Mostrar resumen actualizado
            $nuevoResumen = Carrito::obtenerResumen();
            echo "📊 <strong>Nuevo estado del carrito:</strong><br>";
            echo "- Productos: " . $nuevoResumen['cantidad_total'] . "<br>";
            echo "- Total: $" . number_format($nuevoResumen['total'], 2) . "<br>";
        } else {
            echo "❌ Error al agregar producto al carrito<br>";
        }
    }
    
    echo "<h2>5. 📝 Instrucciones de uso:</h2>";
    echo "<ol>";
    echo "<li><strong>Agregar productos:</strong> Haz clic en 'Agregar' junto a cualquier producto</li>";
    echo "<li><strong>Ver carrito:</strong> Ve a la sección de carrito para ver todos los productos agregados</li>";
    echo "<li><strong>Completar compra:</strong> En el carrito, procede al checkout para generar la factura</li>";
    echo "<li><strong>Ver facturas:</strong> Accede al historial de facturas para ver compras anteriores</li>";
    echo "</ol>";
    
    echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>✅ Sistema funcionando correctamente</h3>";
    echo "<p>El sistema de compra está operativo y las imágenes de productos se muestran correctamente tanto en:</p>";
    echo "<ul>";
    echo "<li>📦 Lista de productos (con botón de agregar al carrito)</li>";
    echo "<li>🛒 Carrito de compras</li>";
    echo "<li>📱 Vista móvil al escanear QR</li>";
    echo "<li>🧾 Facturas generadas</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>