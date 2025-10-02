<?php
require_once 'clases/Producto.php';
require_once 'clases/QRCodeGenerator.php';

echo "<h2>🔍 Análisis de URLs de Google Charts</h2>";

// Crear producto de prueba
$producto = new Producto(4, "mani", "23.00", 3);

echo "<h3>1. Datos del producto:</h3>";
echo "• ID: " . $producto->getIdProducto() . "<br>";
echo "• Nombre: " . $producto->getNombre() . "<br>";

echo "<h3>2. Datos generados para QR:</h3>";
$data = QRCodeGenerator::generateProductData($producto);
echo "• Datos: <code>" . htmlspecialchars($data) . "</code><br>";
echo "• Longitud: " . strlen($data) . " caracteres<br>";

echo "<h3>3. URL generada:</h3>";
$qrUrl = QRCodeGenerator::generateProductQR($producto);
echo "• URL completa: <code>" . htmlspecialchars($qrUrl) . "</code><br>";

echo "<h3>4. Pruebas con diferentes URLs:</h3>";

// Test 1: URL simple
$testData1 = "test";
$testUrl1 = "https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=" . urlencode($testData1);
echo "• Test simple: <a href='" . $testUrl1 . "' target='_blank'>" . htmlspecialchars($testUrl1) . "</a><br>";

// Test 2: URL con datos del producto simplificados
$testData2 = "Producto: " . $producto->getNombre() . " - $" . $producto->getPrecio();
$testUrl2 = "https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=" . urlencode($testData2);
echo "• Test producto simple: <a href='" . $testUrl2 . "' target='_blank'>" . htmlspecialchars($testUrl2) . "</a><br>";

// Test 3: URL con nuestros datos actuales
echo "• Test datos actuales: <a href='" . $qrUrl . "' target='_blank'>" . htmlspecialchars($qrUrl) . "</a><br>";

echo "<h3>5. Verificación manual:</h3>";
echo "Haz clic en cada URL arriba para ver si Google Charts responde correctamente en el navegador.<br>";
echo "Si alguna funciona, usaremos ese formato.<br>";

echo "<h3>6. APIs alternativas:</h3>";
echo "Si Google Charts no funciona, podemos usar:<br>";
echo "• QR Server: <a href='https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($testData2) . "' target='_blank'>QR Server API</a><br>";
echo "• QuickChart: <a href='https://quickchart.io/qr?text=" . urlencode($testData2) . "&size=200' target='_blank'>QuickChart API</a><br>";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
code { background: #f4f4f4; padding: 2px 5px; border-radius: 3px; }
a { color: #007cba; }
</style>