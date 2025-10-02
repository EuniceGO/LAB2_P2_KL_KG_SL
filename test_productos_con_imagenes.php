<?php
echo "<h2>🧪 Prueba de Funcionalidad de Imágenes en Productos</h2>";

require_once 'modelos/ProductoModel.php';
require_once 'modelos/CategoriaModel.php';

try {
    echo "<h3>1. ✅ Verificando productos con imágenes...</h3>";
    
    $productoModel = new ProductoModel();
    $productos = $productoModel->getAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th>ID</th><th>Nombre</th><th>Precio</th><th>Imagen URL</th><th>Preview</th><th>Acciones</th>";
    echo "</tr>";
    
    foreach ($productos as $producto) {
        echo "<tr>";
        echo "<td>" . $producto->getIdProducto() . "</td>";
        echo "<td>" . htmlspecialchars($producto->getNombre()) . "</td>";
        echo "<td>$" . $producto->getPrecio() . "</td>";
        echo "<td>";
        if ($producto->getImagenUrl()) {
            echo "<small>" . substr($producto->getImagenUrl(), 0, 50) . "...</small>";
        } else {
            echo "<em>Sin imagen</em>";
        }
        echo "</td>";
        echo "<td>";
        if ($producto->getImagenUrl()) {
            echo "<img src='" . htmlspecialchars($producto->getImagenUrl()) . "' alt='Preview' style='max-width: 50px; max-height: 40px; object-fit: cover;' onerror='this.src=\"data:image/svg+xml,%3Csvg xmlns=\"http://www.w3.org/2000/svg\" width=\"50\" height=\"40\"%3E%3Crect width=\"50\" height=\"40\" fill=\"%23ddd\"/%3E%3Ctext x=\"25\" y=\"20\" text-anchor=\"middle\" fill=\"%23999\" font-size=\"8\"%3EError%3C/text%3E%3C/svg%3E\"'>";
        } else {
            echo "<span style='color: #999;'>N/A</span>";
        }
        echo "</td>";
        echo "<td>";
        echo "<a href='?c=producto&a=viewQR&id=" . $producto->getIdProducto() . "' target='_blank' style='margin-right: 5px;'>Ver QR</a>";
        echo "<a href='?c=producto&a=viewMobile&id=" . $producto->getIdProducto() . "' target='_blank'>Vista Móvil</a>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>2. ✅ Creando producto de prueba con imagen...</h3>";
    
    // Obtener primera categoría disponible
    $categoriaModel = new CategoriaModel();
    $categorias = $categoriaModel->getAll();
    
    if (!empty($categorias)) {
        $nuevoProducto = new Producto(
            null,
            "Producto con Imagen de Prueba",
            99.99,
            $categorias[0]->getIdCategoria(),
            null, // QR se genera automáticamente
            "https://img.freepik.com/vector-gratis/ilustracion-sol-dibujos-animados-sonrisa-feliz_1308-179974.jpg?semt=ais_hybrid&w=740&q=80"
        );
        
        $resultado = $productoModel->insert($nuevoProducto);
        
        if ($resultado) {
            echo "✅ Producto de prueba creado con ID: $resultado<br>";
            echo "🔗 <a href='?c=producto&a=viewQR&id=$resultado' target='_blank'>Ver código QR del producto de prueba</a><br>";
            echo "📱 <a href='?c=producto&a=viewMobile&id=$resultado' target='_blank'>Ver vista móvil del producto de prueba</a><br>";
        } else {
            echo "❌ Error al crear producto de prueba<br>";
        }
    }
    
    echo "<h3>3. ✅ Enlaces rápidos de prueba:</h3>";
    echo "<ul>";
    echo "<li><a href='?c=producto&a=index' target='_blank'>📋 Lista de productos</a></li>";
    echo "<li><a href='?c=producto&a=create' target='_blank'>➕ Crear nuevo producto</a></li>";
    echo "<li><a href='agregar_imagen_productos.php' target='_blank'>🔧 Script de configuración de base de datos</a></li>";
    echo "</ul>";
    
    echo "<h3>4. 📋 Instrucciones de uso:</h3>";
    echo "<ol>";
    echo "<li><strong>Crear producto:</strong> Ve a la sección de productos y haz clic en 'Nuevo Producto'</li>";
    echo "<li><strong>Agregar imagen:</strong> En el formulario, pega una URL de imagen válida (debe empezar con http:// o https://)</li>";
    echo "<li><strong>Ver resultado:</strong> Después de guardar, ve al código QR del producto para ver la imagen</li>";
    echo "<li><strong>Probar desde móvil:</strong> Escanea el código QR con tu celular para ver la vista móvil con imagen</li>";
    echo "</ol>";
    
    echo "<h3>5. 🌐 URLs de imágenes de ejemplo:</h3>";
    echo "<ul>";
    echo "<li>Sol feliz: <code>https://img.freepik.com/vector-gratis/ilustracion-sol-dibujos-animados-sonrisa-feliz_1308-179974.jpg?semt=ais_hybrid&w=740&q=80</code></li>";
    echo "<li>Luna durmiendo: <code>https://img.freepik.com/vector-premium/dibujos-animados-luna-durmiendo-nubes_29190-7932.jpg</code></li>";
    echo "<li>Estrella: <code>https://img.freepik.com/vector-gratis/ilustracion-estrella-dibujos-animados_1308-177539.jpg</code></li>";
    echo "<li>Arcoíris: <code>https://img.freepik.com/vector-premium/dibujos-animados-arco-iris-lindo_29190-7940.jpg</code></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>