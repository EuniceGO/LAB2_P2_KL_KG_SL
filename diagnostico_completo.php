<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 Diagnóstico Avanzado de QR</h2>";

// Test básico de configuración
echo "<h3>1. Configuración PHP:</h3>";
echo "• allow_url_fopen: " . (ini_get('allow_url_fopen') ? '✅ HABILITADO' : '❌ DESHABILITADO') . "<br>";
echo "• CURL disponible: " . (function_exists('curl_init') ? '✅ SÍ' : '❌ NO') . "<br>";
echo "• file_get_contents disponible: " . (function_exists('file_get_contents') ? '✅ SÍ' : '❌ NO') . "<br>";

// Test de directorio
echo "<h3>2. Directorio assets/qr/:</h3>";
$qrDir = 'assets/qr/';
echo "• Existe: " . (file_exists($qrDir) ? '✅ SÍ' : '❌ NO') . "<br>";
echo "• Es directorio: " . (is_dir($qrDir) ? '✅ SÍ' : '❌ NO') . "<br>";
echo "• Escribible: " . (is_writable($qrDir) ? '✅ SÍ' : '❌ NO') . "<br>";

// Test de conectividad simple
echo "<h3>3. Test de Conectividad:</h3>";
$testUrl = 'https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl=test';
echo "URL de prueba: " . htmlspecialchars($testUrl) . "<br>";

$headers = @get_headers($testUrl);
if ($headers && strpos($headers[0], '200') !== false) {
    echo "• get_headers(): ✅ Respuesta 200 OK<br>";
} else {
    echo "• get_headers(): ❌ Sin respuesta o error<br>";
}

// Test con CURL detallado
if (function_exists('curl_init')) {
    echo "<h3>4. Test CURL Detallado:</h3>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 Test');
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    
    echo "• HTTP Code: " . $httpCode . "<br>";
    echo "• Error: " . ($error ? $error : 'Ninguno') . "<br>";
    echo "• Contenido descargado: " . ($result ? strlen($result) . ' bytes' : 'Ninguno') . "<br>";
    echo "• Content Type: " . $info['content_type'] . "<br>";
    
    if ($result && strlen($result) > 0) {
        // Intentar guardar archivo de prueba
        $testFile = $qrDir . 'test_' . time() . '.png';
        if (file_put_contents($testFile, $result)) {
            echo "• ✅ Archivo de prueba guardado: " . $testFile . "<br>";
            echo "<img src='" . $testFile . "' style='max-width: 100px;' alt='Test QR'><br>";
        } else {
            echo "• ❌ Error al guardar archivo de prueba<br>";
        }
    }
}

// Test con file_get_contents
if (ini_get('allow_url_fopen')) {
    echo "<h3>5. Test file_get_contents:</h3>";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 Test'
        ]
    ]);
    
    $result = @file_get_contents($testUrl, false, $context);
    
    if ($result) {
        echo "• ✅ Descarga exitosa: " . strlen($result) . " bytes<br>";
        
        $testFile2 = $qrDir . 'test_fgc_' . time() . '.png';
        if (file_put_contents($testFile2, $result)) {
            echo "• ✅ Archivo guardado: " . $testFile2 . "<br>";
            echo "<img src='" . $testFile2 . "' style='max-width: 100px;' alt='Test QR FGC'><br>";
        } else {
            echo "• ❌ Error al guardar<br>";
        }
    } else {
        echo "• ❌ Error en descarga<br>";
    }
} else {
    echo "<h3>5. file_get_contents: ❌ DESHABILITADO</h3>";
}

// Información adicional
echo "<h3>6. Información del Sistema:</h3>";
echo "• PHP Version: " . phpversion() . "<br>";
echo "• Usuario del servidor: " . (function_exists('exec') ? exec('whoami') : 'Desconocido') . "<br>";
echo "• Directorio actual: " . __DIR__ . "<br>";
echo "• Permisos directorio actual: " . substr(sprintf('%o', fileperms(__DIR__)), -4) . "<br>";

if (file_exists($qrDir)) {
    echo "• Permisos directorio QR: " . substr(sprintf('%o', fileperms($qrDir)), -4) . "<br>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #333; }
h3 { color: #666; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
</style>