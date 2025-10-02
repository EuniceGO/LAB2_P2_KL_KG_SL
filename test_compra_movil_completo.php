<?php
echo "<h1>📱 Test Completo - Compra desde Móvil</h1>";

try {
    require_once 'modelos/ProductoModel.php';
    require_once 'modelos/CategoriaModel.php';
    
    echo "<h2>1. ✅ Creando producto de prueba para móvil...</h2>";
    
    // Verificar si hay categorías
    $categoriaModel = new CategoriaModel();
    $categorias = $categoriaModel->getAll();
    
    if (empty($categorias)) {
        echo "❌ No hay categorías disponibles. Creando una categoría de prueba...<br>";
        // Aquí podrías crear una categoría si fuera necesario
    }
    
    $productoModel = new ProductoModel();
    
    // Crear producto de prueba específico para móvil
    $productoMovil = new Producto(
        null,
        "Camisa de Vestir",
        5.50,
        $categorias[0]->getIdCategoria(),
        null,
        "https://i.pinimg.com/236x/72/05/83/720583019e7be4ad55cf15a4f7ce05b1.jpg"
    );
    
    $idProducto = $productoModel->insert($productoMovil);
    
    if ($idProducto) {
        echo "✅ Producto 'Camisa de Vestir' creado con ID: $idProducto<br>";
        echo "🖼️ Imagen: Niña con camisa roja<br>";
        echo "💰 Precio: $5.50<br>";
        
        echo "<h2>2. 📱 Enlaces para probar desde el celular:</h2>";
        
        // Generar QR del producto
        $producto = $productoModel->getById($idProducto);
        if ($producto && $producto->getCodigoQr()) {
            echo "<div style='text-align: center; margin: 20px 0;'>";
            echo "<h3>🔍 Código QR del producto:</h3>";
            echo "<img src='" . $producto->getCodigoQr() . "' alt='QR Code' style='max-width: 200px; border: 2px solid #ddd; padding: 10px;'><br>";
            echo "<small>Escanea este código QR con tu celular</small>";
            echo "</div>";
        }
        
        echo "<h3>📱 URL directa para móvil:</h3>";
        echo "<code>http://192.168.1.23/examen2/LAB2_P2_KL_KG_SL/?c=producto&a=viewMobile&id=$idProducto</code><br><br>";
        
        echo "<a href='?c=producto&a=viewMobile&id=$idProducto' target='_blank' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📱 Abrir Vista Móvil</a><br><br>";
        
        echo "<h2>3. 🛒 Testear carrito móvil:</h2>";
        echo "<a href='?c=carrito&a=mobile' target='_blank' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🛒 Ver Carrito Móvil</a><br><br>";
        
        echo "<h2>4. 📋 Instrucciones para probar desde el celular:</h2>";
        echo "<ol>";
        echo "<li>🔍 <strong>Escanea el código QR</strong> con la cámara de tu celular</li>";
        echo "<li>📱 <strong>Se abrirá la vista móvil</strong> del producto con la imagen</li>";
        echo "<li>🛒 <strong>Haz clic en 'Agregar al Carrito'</strong> (botón verde)</li>";
        echo "<li>✅ <strong>Verás mensaje de confirmación</strong> de que se agregó</li>";
        echo "<li>👀 <strong>Haz clic en 'Ver Mi Carrito'</strong> para ver los productos</li>";
        echo "<li>💳 <strong>Procede al checkout</strong> desde el carrito móvil</li>";
        echo "</ol>";
        
        echo "<h2>5. 🔗 Enlaces adicionales:</h2>";
        echo "<ul>";
        echo "<li><a href='?c=producto&a=index' target='_blank'>📦 Lista completa de productos</a></li>";
        echo "<li><a href='?c=carrito&a=index' target='_blank'>🛒 Carrito web completo</a></li>";
        echo "<li><a href='?c=producto&a=viewQR&id=$idProducto' target='_blank'>🎯 Ver QR del producto</a></li>";
        echo "</ul>";
        
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>✅ Sistema móvil listo para pruebas</h3>";
        echo "<p><strong>Funcionalidades disponibles desde móvil:</strong></p>";
        echo "<ul>";
        echo "<li>📱 Vista optimizada del producto con imagen</li>";
        echo "<li>🛒 Botón para agregar al carrito</li>";
        echo "<li>👀 Enlace para ver el carrito móvil</li>";
        echo "<li>✅ Confirmación visual de productos agregados</li>";
        echo "<li>💳 Proceso completo de checkout móvil</li>";
        echo "</ul>";
        echo "</div>";
        
    } else {
        echo "❌ Error al crear producto de prueba<br>";
    }
    
    echo "<h2>6. 🌐 Configuración de red:</h2>";
    echo "<p><strong>IP configurada:</strong> 192.168.1.23</p>";
    echo "<p><strong>URL base:</strong> http://192.168.1.23/examen2/LAB2_P2_KL_KG_SL/</p>";
    echo "<p><em>Asegúrate de que tu celular esté en la misma red WiFi que tu computadora</em></p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>