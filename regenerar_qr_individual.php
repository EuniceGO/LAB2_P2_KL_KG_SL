<?php
require_once 'config/cn.php';
require_once 'clases/Producto.php';
require_once 'clases/QRCodeGenerator.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    echo "<h2>🔧 Regenerando QR para Producto ID: $id</h2>";
    
    try {
        // Obtener el producto
        $cn = new CN();
        $sql = "SELECT * FROM Productos WHERE id_producto = ?";
        $results = $cn->consulta($sql, [$id]);
        
        if (!empty($results)) {
            $row = $results[0];
            $producto = new Producto(
                $row['id_producto'],
                $row['nombre'],
                $row['precio'],
                $row['id_categoria']
            );
            
            echo "<p>Producto: " . htmlspecialchars($producto->getNombre()) . "</p>";
            
            // Generar nuevo QR
            $nuevoQrPath = QRCodeGenerator::generateAndSaveProductQR($producto);
            
            if ($nuevoQrPath) {
                // Actualizar en base de datos
                $sqlUpdate = "UPDATE Productos SET codigo_qr = ? WHERE id_producto = ?";
                $cn->ejecutar($sqlUpdate, [$nuevoQrPath, $id]);
                
                echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
                echo "✅ QR regenerado exitosamente: " . htmlspecialchars($nuevoQrPath) . "<br>";
                echo "<img src='" . $nuevoQrPath . "' alt='Nuevo QR' style='max-width: 200px; margin-top: 10px;'>";
                echo "</div>";
            } else {
                echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
                echo "❌ Error al regenerar el QR";
                echo "</div>";
            }
        } else {
            echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
            echo "❌ Producto no encontrado";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
        echo "❌ Error: " . htmlspecialchars($e->getMessage());
        echo "</div>";
    }
    
    echo "<br><a href='revisar_qr_db.php'>← Volver a revisión</a>";
} else {
    echo "ID de producto no proporcionado";
}
?>