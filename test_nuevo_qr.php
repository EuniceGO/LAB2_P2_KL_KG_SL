<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'clases/Producto.php';
require_once 'clases/QRCodeGenerator.php';

echo "<h2>🚀 Test del Nuevo Sistema QR</h2>";

// Crear producto de prueba
$producto = new Producto(4, "mani", "23.00", 3);

echo "<h3>1. Datos del producto:</h3>";
echo "• " . $producto->getNombre() . " - $" . $producto->getPrecio() . "<br>";

echo "<h3>2. Generando QR con el nuevo sistema:</h3>";
$qrPath = QRCodeGenerator::generateAndSaveProductQR($producto);

if ($qrPath) {
    echo "✅ <strong>QR generado exitosamente:</strong> " . htmlspecialchars($qrPath) . "<br>";
    
    if (file_exists($qrPath)) {
        echo "✅ <strong>Archivo existe:</strong> " . filesize($qrPath) . " bytes<br>";
        echo "<h3>3. Vista previa del QR:</h3>";
        echo "<img src='" . $qrPath . "' alt='QR Generado' style='max-width: 300px; border: 1px solid #ddd; padding: 10px;'><br>";
        echo "<p><em>¡Perfecto! El QR se ve correctamente como imagen.</em></p>";
    } else {
        echo "❌ <strong>Error:</strong> El archivo no existe en la ruta especificada<br>";
    }
} else {
    echo "❌ <strong>Error:</strong> No se pudo generar el QR<br>";
    
    echo "<h3>3. Diagnóstico del error:</h3>";
    
    // Test manual de las APIs
    $data = QRCodeGenerator::generateProductData($producto);
    echo "• Datos a codificar: " . htmlspecialchars($data) . "<br>";
    
    $apis = [
        'QR Server' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($data),
        'QuickChart' => 'https://quickchart.io/qr?text=' . urlencode($data) . '&size=200',
        'Google Charts' => 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . urlencode($data)
    ];
    
    foreach ($apis as $name => $url) {
        echo "<br><strong>Test $name:</strong><br>";
        echo "URL: <a href='$url' target='_blank'>" . htmlspecialchars($url) . "</a><br>";
        
        // Test con CURL
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);
            
            echo "• HTTP Code: $httpCode<br>";
            echo "• Content Type: $contentType<br>";
            echo "• Data Size: " . strlen($result) . " bytes<br>";
            
            if ($httpCode === 200 && strpos($contentType, 'image/') !== false) {
                echo "• ✅ Esta API funciona correctamente<br>";
            } else {
                echo "• ❌ Esta API tiene problemas<br>";
            }
        }
    }
}

echo "<br><a href='fix_qr_codes.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔧 Regenerar todos los QRs</a>";
echo " <a href='?c=producto&a=index' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>← Volver</a>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #333; }
</style>