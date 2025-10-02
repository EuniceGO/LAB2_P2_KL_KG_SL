<?php
session_start();
require_once 'config/cn.php';

echo "<h2>🖼️ Prueba de Imágenes en Vista Móvil QR</h2>";

// Simular cliente logueado
if (!isset($_SESSION['user_id'])) {
    try {
        $cn = new CNpdo();
        $sql = "SELECT u.id_usuario, u.nombre, u.email, u.role_id 
                FROM Usuarios u 
                WHERE u.role_id = 2 
                LIMIT 1";
        $results = $cn->consulta($sql);
        
        if (!empty($results)) {
            $cliente = $results[0];
            $_SESSION['user_id'] = $cliente['id_usuario'];
            $_SESSION['user_name'] = $cliente['nombre'];
            $_SESSION['user_email'] = $cliente['email'];
            $_SESSION['user_role_id'] = $cliente['role_id'];
            
            echo "<p>✅ Cliente simulado logueado: " . $cliente['nombre'] . "</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    }
}

// Obtener productos con sus imágenes
try {
    $cn = new CNpdo();
    $sql = "SELECT p.id_producto, p.nombre, p.precio, p.imagen_url, p.codigo_qr, c.nombre as categoria_nombre
            FROM Productos p
            LEFT JOIN Categorias c ON p.id_categoria = c.id_categoria
            ORDER BY p.id_producto DESC";
    $productos = $cn->consulta($sql);
    
    echo "<h3>📦 Productos disponibles:</h3>";
    echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;'>";
    
    foreach ($productos as $producto) {
        $tieneImagen = !empty($producto['imagen_url']);
        $tieneQR = !empty($producto['codigo_qr']);
        
        echo "<div style='border: 2px solid " . ($tieneImagen ? '#28a745' : '#dc3545') . "; padding: 15px; border-radius: 10px; background: " . ($tieneImagen ? '#f8fff9' : '#fff8f8') . ";'>";
        echo "<h5>" . htmlspecialchars($producto['nombre']) . " (ID: " . $producto['id_producto'] . ")</h5>";
        echo "<p><strong>Precio:</strong> $" . number_format($producto['precio'], 2) . "</p>";
        echo "<p><strong>Categoría:</strong> " . ($producto['categoria_nombre'] ?? 'Sin categoría') . "</p>";
        
        if ($tieneImagen) {
            echo "<p style='color: #28a745;'><i class='fas fa-check-circle'></i> <strong>Tiene imagen:</strong></p>";
            echo "<img src='" . htmlspecialchars($producto['imagen_url']) . "' style='width: 100%; max-width: 200px; height: 150px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;'>";
        } else {
            echo "<p style='color: #dc3545;'><i class='fas fa-times-circle'></i> <strong>Sin imagen</strong></p>";
            echo "<div style='width: 200px; height: 150px; background: #f8f9fa; border: 2px dashed #6c757d; display: flex; align-items: center; justify-content: center; border-radius: 5px;'>Sin imagen</div>";
        }
        
        if ($tieneQR) {
            echo "<p style='color: #17a2b8; margin-top: 10px;'><i class='fas fa-qrcode'></i> <strong>QR disponible</strong></p>";
            echo "<p><a href='?c=producto&a=mobileView&id=" . $producto['id_producto'] . "' target='_blank' class='btn btn-primary' style='background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 5px;'>🔍 Ver en Móvil</a></p>";
        } else {
            echo "<p style='color: #6c757d; margin-top: 10px;'><i class='fas fa-exclamation-triangle'></i> Sin QR</p>";
        }
        
        echo "</div>";
    }
    
    echo "</div>";
    
    // Estadísticas
    $conImagen = array_filter($productos, function($p) { return !empty($p['imagen_url']); });
    $conQR = array_filter($productos, function($p) { return !empty($p['codigo_qr']); });
    
    echo "<h3>📊 Estadísticas:</h3>";
    echo "<ul>";
    echo "<li><strong>Total productos:</strong> " . count($productos) . "</li>";
    echo "<li><strong>Con imagen:</strong> " . count($conImagen) . " (" . round((count($conImagen) / count($productos)) * 100, 1) . "%)</li>";
    echo "<li><strong>Con QR:</strong> " . count($conQR) . " (" . round((count($conQR) / count($productos)) * 100, 1) . "%)</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<p>❌ Error al consultar productos: " . $e->getMessage() . "</p>";
}

echo "<h3>🧪 Productos de prueba específicos:</h3>";

// Buscar un producto con imagen
try {
    $sql = "SELECT id_producto, nombre, imagen_url FROM Productos WHERE imagen_url IS NOT NULL AND imagen_url != '' LIMIT 1";
    $productoConImagen = $cn->consulta($sql);
    
    if (!empty($productoConImagen)) {
        $prod = $productoConImagen[0];
        echo "<p>✅ <strong>Producto con imagen encontrado:</strong> " . $prod['nombre'] . " (ID: " . $prod['id_producto'] . ")</p>";
        echo "<p><a href='?c=producto&a=mobileView&id=" . $prod['id_producto'] . "' target='_blank' class='btn btn-success' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>📱 Probar Vista Móvil CON Imagen</a></p>";
    } else {
        echo "<p>⚠️ No se encontraron productos con imagen</p>";
    }
    
    // Buscar un producto sin imagen  
    $sql = "SELECT id_producto, nombre FROM Productos WHERE imagen_url IS NULL OR imagen_url = '' LIMIT 1";
    $productoSinImagen = $cn->consulta($sql);
    
    if (!empty($productoSinImagen)) {
        $prod = $productoSinImagen[0];
        echo "<p>ℹ️ <strong>Producto sin imagen encontrado:</strong> " . $prod['nombre'] . " (ID: " . $prod['id_producto'] . ")</p>";
        echo "<p><a href='?c=producto&a=mobileView&id=" . $prod['id_producto'] . "' target='_blank' class='btn btn-warning' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>📱 Probar Vista Móvil SIN Imagen</a></p>";
    } else {
        echo "<p>✅ Todos los productos tienen imagen</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error en búsqueda: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php' class='btn btn-secondary' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>🏠 Volver al Inicio</a></p>";
?>

<style>
.btn {
    margin: 5px;
    font-weight: bold;
}
</style>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">