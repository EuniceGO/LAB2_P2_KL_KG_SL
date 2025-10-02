<?php
echo "<h2>Configuración PHP para QR</h2>";
echo "<pre>";
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'HABILITADO' : 'DESHABILITADO') . "\n";
echo "CURL disponible: " . (function_exists('curl_init') ? 'SÍ' : 'NO') . "\n";
echo "file_get_contents disponible: " . (function_exists('file_get_contents') ? 'SÍ' : 'NO') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "</pre>";

if (!ini_get('allow_url_fopen')) {
    echo "<div style='background: #ffe6e6; padding: 10px; border: 1px solid #ff9999;'>";
    echo "<strong>⚠️ PROBLEMA ENCONTRADO:</strong><br>";
    echo "allow_url_fopen está DESHABILITADO. Para habilitarlo:<br><br>";
    echo "1. Abrir el archivo php.ini (normalmente en C:\\xampp\\php\\php.ini)<br>";
    echo "2. Buscar la línea: <code>;allow_url_fopen = Off</code><br>";
    echo "3. Cambiarla por: <code>allow_url_fopen = On</code><br>";
    echo "4. Reiniciar Apache desde XAMPP<br>";
    echo "</div>";
}
?>