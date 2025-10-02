<?php
// Script de diagnóstico para códigos QR
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/cn.php';
require_once 'clases/Producto.php';
require_once 'clases/QRCodeGenerator.php';

echo "<h2>🔍 Diagnóstico del Sistema de Códigos QR</h2>";

// Test 1: Verificar configuración de PHP
echo "<h3>1. Configuración de PHP:</h3>";
echo "✅ allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'Habilitado' : '❌ DESHABILITADO') . "<br>";
echo "✅ file_get_contents disponible: " . (function_exists('file_get_contents') ? 'Sí' : '❌ No') . "<br>";
echo "✅ curl disponible: " . (function_exists('curl_init') ? 'Sí' : 'No') . "<br>";

// Test 2: Verificar directorio
echo "<h3>2. Directorio assets/qr/:</h3>";
$qrDir = 'assets/qr/';
if (!file_exists($qrDir)) {
    if (mkdir($qrDir, 0777, true)) {
        echo "✅ Directorio creado: $qrDir<br>";
    } else {
        echo "❌ Error al crear directorio: $qrDir<br>";
    }
} else {
    echo "✅ Directorio existe: $qrDir<br>";
}

echo "✅ Permisos de escritura: " . (is_writable($qrDir) ? 'Sí' : '❌ No') . "<br>";

// Test 3: Crear producto de prueba
echo "<h3>3. Producto de Prueba:</h3>";
$producto = new Producto(999, 'Producto de Prueba', 99.99, 1);
echo "✅ Producto creado: ID={$producto->getIdProducto()}, Nombre={$producto->getNombre()}<br>";

// Test 4: Generar URL del QR
echo "<h3>4. Generación de URL del QR:</h3>";
try {
    $qrUrl = QRCodeGenerator::generateProductQR($producto);
    echo "✅ URL generada: <a href='$qrUrl' target='_blank'>$qrUrl</a><br>";
    echo "✅ Datos del QR: " . QRCodeGenerator::generateProductData($producto) . "<br>";
} catch (Exception $e) {
    echo "❌ Error al generar URL: " . $e->getMessage() . "<br>";
}

// Test 5: Descargar imagen de prueba
echo "<h3>5. Descarga de Imagen:</h3>";
try {
    if (isset($qrUrl)) {
        echo "🔄 Intentando descargar imagen...<br>";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);
        
        $imageData = file_get_contents($qrUrl, false, $context);
        
        if ($imageData !== false && !empty($imageData)) {
            echo "✅ Imagen descargada correctamente. Tamaño: " . strlen($imageData) . " bytes<br>";
            
            $testFile = $qrDir . 'test_qr.png';
            if (file_put_contents($testFile, $imageData)) {
                echo "✅ Imagen guardada en: $testFile<br>";
                echo "✅ Vista previa: <img src='$testFile' style='width:100px;height:100px;'><br>";
            } else {
                echo "❌ Error al guardar imagen<br>";
            }
        } else {
            echo "❌ No se pudo descargar la imagen<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error en descarga: " . $e->getMessage() . "<br>";
}

// Test 6: Método completo
echo "<h3>6. Método Completo:</h3>";
try {
    $resultado = QRCodeGenerator::generateAndSaveProductQR($producto);
    if ($resultado) {
        echo "✅ QR generado exitosamente: $resultado<br>";
        if (file_exists($resultado)) {
            if (strpos($resultado, 'http') === 0) {
                echo "ℹ️ Usando URL directa (no archivo local)<br>";
                echo "✅ Vista previa: <img src='$resultado' style='width:100px;height:100px;'><br>";
            } else {
                echo "✅ Archivo local creado<br>";
                echo "✅ Vista previa: <img src='$resultado' style='width:100px;height:100px;'><br>";
            }
        }
    } else {
        echo "❌ Error al generar QR<br>";
    }
} catch (Exception $e) {
    echo "❌ Error en método completo: " . $e->getMessage() . "<br>";
}

// Test 7: Conexión a Google Chart API
echo "<h3>7. Test de Conectividad:</h3>";
$testUrl = 'https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl=test';
echo "🔄 Probando conexión a Google Chart API...<br>";

$headers = @get_headers($testUrl);
if ($headers && strpos($headers[0], '200') !== false) {
    echo "✅ Conexión a Google Chart API exitosa<br>";
    echo "✅ Test QR: <img src='$testUrl' style='width:50px;height:50px;'><br>";
} else {
    echo "❌ No se puede conectar a Google Chart API<br>";
    echo "🔧 Intenta con CURL...<br>";
    
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $testUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200) {
            echo "✅ CURL funciona correctamente<br>";
        } else {
            echo "❌ CURL también falla. Código: $httpCode<br>";
        }
    }
}

echo "<h3>📋 Resumen:</h3>";
echo "<p><strong>Si ves errores arriba, revisa:</strong></p>";
echo "<ul>";
echo "<li>Que allow_url_fopen esté habilitado en PHP</li>";
echo "<li>Que tengas conexión a internet</li>";
echo "<li>Que el directorio assets/qr/ tenga permisos de escritura</li>";
echo "<li>Que no haya firewall bloqueando Google Chart API</li>";
echo "</ul>";

echo "<p><a href='?c=producto&a=index' class='btn btn-primary'>🔙 Volver a Productos</a></p>";
?>