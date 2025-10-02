<?php
/**
 * Script para regenerar códigos QR que se guardaron como URLs en lugar de archivos
 */

require_once 'config/cn.php';
require_once 'clases/Producto.php';
require_once 'clases/QRCodeGenerator.php';
require_once 'modelos/ProductoModel.php';

echo "<h2>Regenerando códigos QR...</h2>";
echo "<pre>";

try {
    $productoModel = new ProductoModel();
    
    // Obtener todos los productos
    $productos = $productoModel->getAll();
    
    echo "Productos encontrados: " . count($productos) . "\n\n";
    
    foreach ($productos as $producto) {
        echo "Procesando producto ID: " . $producto->getIdProducto() . " - " . $producto->getNombre() . "\n";
        
        $qrActual = $producto->getCodigoQr();
        
        // Si el QR actual es una URL (contiene http) o contiene JSON, regenerarlo
        if ($qrActual && (strpos($qrActual, 'http') === 0 || strpos($qrActual, '{') !== false)) {
            echo "  - QR actual es URL/JSON, regenerando...\n";
            
            // Regenerar QR
            $nuevoQrPath = QRCodeGenerator::generateAndSaveProductQR($producto);
            
            if ($nuevoQrPath) {
                // Actualizar en base de datos
                $productoModel->updateQRCode($producto->getIdProducto(), $nuevoQrPath);
                echo "  - ✓ QR regenerado: " . $nuevoQrPath . "\n";
            } else {
                echo "  - ✗ Error al regenerar QR\n";
            }
        } elseif ($qrActual && file_exists($qrActual)) {
            echo "  - QR ya existe como archivo: " . $qrActual . "\n";
        } elseif (!$qrActual) {
            echo "  - Generando QR por primera vez...\n";
            
            // Generar QR por primera vez
            $nuevoQrPath = QRCodeGenerator::generateAndSaveProductQR($producto);
            
            if ($nuevoQrPath) {
                // Actualizar en base de datos
                $productoModel->updateQRCode($producto->getIdProducto(), $nuevoQrPath);
                echo "  - ✓ QR generado: " . $nuevoQrPath . "\n";
            } else {
                echo "  - ✗ Error al generar QR\n";
            }
        }
        
        echo "\n";
    }
    
    echo "\n✓ Proceso completado\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>

<a href="?c=producto&a=index">← Volver a productos</a>