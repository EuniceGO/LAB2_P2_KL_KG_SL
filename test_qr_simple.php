<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/cn.php';
require_once 'clases/Producto.php';
require_once 'clases/QRCodeGenerator.php';

echo "<h2>🔧 Test Rápido de QR</h2>";

// Crear un producto de prueba
$producto = new Producto(4, "mani", "23.00", 3);

echo "<h3>1. Datos del producto:</h3>";
echo "ID: " . $producto->getIdProducto() . "<br>";
echo "Nombre: " . $producto->getNombre() . "<br>";
echo "Precio: " . $producto->getPrecio() . "<br>";
echo "Categoría: " . $producto->getIdCategoria() . "<br>";

echo "<h3>2. Generando datos para QR:</h3>";
$data = QRCodeGenerator::generateProductData($producto);
echo "Datos: " . htmlspecialchars($data) . "<br>";

echo "<h3>3. Generando URL del QR:</h3>";
$qrUrl = QRCodeGenerator::generateProductQR($producto);
echo "URL: " . htmlspecialchars($qrUrl) . "<br>";

echo "<h3>4. Verificando conectividad:</h3>";
$headers = @get_headers($qrUrl);
if ($headers && strpos($headers[0], '200') !== false) {
    echo "✅ La URL del QR responde correctamente<br>";
    
    echo "<h3>5. Intentando descargar imagen:</h3>";
    
    // Test con file_get_contents
    if (ini_get('allow_url_fopen')) {
        echo "✅ allow_url_fopen está habilitado<br>";
        
        $imageData = @file_get_contents($qrUrl);
        if ($imageData !== false && !empty($imageData)) {
            echo "✅ Imagen descargada con file_get_contents (" . strlen($imageData) . " bytes)<br>";
            
            // Intentar guardar
            $fileName = QRCodeGenerator::generateQRFileName($producto->getIdProducto());
            $fullPath = 'assets/qr/' . $fileName;
            
            if (file_put_contents($fullPath, $imageData)) {
                echo "✅ Imagen guardada en: " . $fullPath . "<br>";
                echo "<img src='" . $fullPath . "' alt='QR Test' style='max-width: 200px;'><br>";
            } else {
                echo "❌ Error al guardar la imagen<br>";
            }
        } else {
            echo "❌ Error al descargar con file_get_contents<br>";
        }
    } else {
        echo "❌ allow_url_fopen está deshabilitado<br>";
    }
    
    // Test con CURL
    if (function_exists('curl_init')) {
        echo "<br>🔄 Probando con CURL...<br>";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $qrUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 QR Generator');
        
        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        echo "HTTP Code: " . $httpCode . "<br>";
        if ($error) {
            echo "CURL Error: " . $error . "<br>";
        }
        
        if ($httpCode === 200 && $imageData !== false && !empty($imageData)) {
            echo "✅ Imagen descargada con CURL (" . strlen($imageData) . " bytes)<br>";
            
            $fileName2 = 'qr_curl_test_' . time() . '.png';
            $fullPath2 = 'assets/qr/' . $fileName2;
            
            if (file_put_contents($fullPath2, $imageData)) {
                echo "✅ Imagen CURL guardada en: " . $fullPath2 . "<br>";
                echo "<img src='" . $fullPath2 . "' alt='QR CURL Test' style='max-width: 200px;'><br>";
            } else {
                echo "❌ Error al guardar la imagen CURL<br>";
            }
        } else {
            echo "❌ Error al descargar con CURL<br>";
        }
    } else {
        echo "❌ CURL no está disponible<br>";
    }
    
} else {
    echo "❌ La URL del QR no responde: " . htmlspecialchars($qrUrl) . "<br>";
}

echo "<h3>6. Información del directorio:</h3>";
$qrDir = 'assets/qr/';
echo "Directorio existe: " . (is_dir($qrDir) ? 'SÍ' : 'NO') . "<br>";
echo "Directorio escribible: " . (is_writable($qrDir) ? 'SÍ' : 'NO') . "<br>";

// Listar archivos en el directorio
$files = glob($qrDir . '*');
echo "Archivos en directorio (" . count($files) . "):<br>";
foreach ($files as $file) {
    echo "- " . basename($file) . " (" . filesize($file) . " bytes)<br>";
}

?>

<a href="?c=producto&a=index">← Volver a productos</a>