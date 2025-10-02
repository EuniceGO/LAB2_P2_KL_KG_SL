<?php
/**
 * Script de prueba para códigos QR móviles
 * Genera un código QR de prueba y verifica la conectividad
 */

require_once 'config/cn.php';
require_once 'modelos/ProductoModel.php';
require_once 'clases/QRCodeGenerator.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Prueba de Códigos QR Móviles</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
    <style>
        body { background-color: #f8f9fa; }
        .card { margin: 20px auto; max-width: 800px; }
        .qr-display { text-align: center; padding: 20px; background: white; border-radius: 10px; margin: 20px 0; }
        .qr-display img { max-width: 300px; border: 2px solid #ddd; border-radius: 10px; }
        .info-box { background: #e9ecef; padding: 15px; border-radius: 8px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='card'>
            <div class='card-header'>
                <h3><i class='fas fa-mobile-alt'></i> Prueba de Códigos QR para Móvil</h3>
            </div>
            <div class='card-body'>";

try {
    // Información del sistema
    echo "<div class='info-box'>
        <h5><i class='fas fa-server'></i> Información del Servidor</h5>
        <p><strong>IP del servidor:</strong> " . ($_SERVER['SERVER_ADDR'] ?? 'N/A') . "</p>
        <p><strong>Host:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "</p>
        <p><strong>Puerto:</strong> " . ($_SERVER['SERVER_PORT'] ?? 'N/A') . "</p>
        <p><strong>Protocolo:</strong> " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'HTTPS' : 'HTTP') . "</p>
    </div>";
    
    // Obtener un producto de prueba
    $productoModel = new ProductoModel();
    $productos = $productoModel->getAll();
    
    if (empty($productos)) {
        echo "<div class='alert alert-warning'>No hay productos en la base de datos para probar.</div>";
    } else {
        $producto = $productos[0]; // Usar el primer producto
        
        echo "<div class='info-box'>
            <h5><i class='fas fa-box'></i> Producto de Prueba</h5>
            <p><strong>ID:</strong> {$producto->getIdProducto()}</p>
            <p><strong>Nombre:</strong> " . htmlspecialchars($producto->getNombre()) . "</p>
            <p><strong>Precio:</strong> $" . number_format($producto->getPrecio(), 2) . "</p>
        </div>";
        
        // Generar URL para móvil
        $mobileUrl = QRCodeGenerator::generateProductData($producto);
        echo "<div class='info-box'>
            <h5><i class='fas fa-link'></i> URL Generada para Móvil</h5>
            <p><code>" . htmlspecialchars($mobileUrl) . "</code></p>
            <a href='" . htmlspecialchars($mobileUrl) . "' target='_blank' class='btn btn-primary btn-sm'>
                <i class='fas fa-external-link-alt'></i> Probar URL
            </a>
        </div>";
        
        // Generar código QR
        $qrUrl = QRCodeGenerator::generateProductQR($producto, 300);
        echo "<div class='qr-display'>
            <h5><i class='fas fa-qrcode'></i> Código QR Generado</h5>
            <img src='" . htmlspecialchars($qrUrl) . "' alt='Código QR' class='img-fluid'>
            <p class='mt-3'><small class='text-muted'>Escanea este código con la cámara de tu celular</small></p>
        </div>";
        
        // Instrucciones
        echo "<div class='alert alert-info'>
            <h6><i class='fas fa-info-circle'></i> Instrucciones para probar:</h6>
            <ol>
                <li>Asegúrate de que tu celular esté conectado a la misma red WiFi que este servidor</li>
                <li>Abre la cámara de tu celular</li>
                <li>Apunta la cámara al código QR de arriba</li>
                <li>Toca la notificación que aparece para abrir el enlace</li>
                <li>Deberías ver la información del producto en una vista optimizada para móvil</li>
            </ol>
        </div>";
        
        // Información de red
        echo "<div class='alert alert-warning'>
            <h6><i class='fas fa-wifi'></i> Configuración de Red</h6>
            <p>Para que funcione desde tu celular, asegúrate de:</p>
            <ul>
                <li>Estar conectado a la misma red WiFi que el servidor</li>
                <li>Que el firewall no bloquee las conexiones</li>
                <li>Que XAMPP esté configurado para aceptar conexiones externas</li>
            </ul>
            <p><small>Si tienes problemas, puedes cambiar manualmente la IP en el archivo <code>clases/QRCodeGenerator.php</code></small></p>
        </div>";
        
        // Test de conectividad
        echo "<div class='info-box'>
            <h5><i class='fas fa-network-wired'></i> Test de Conectividad</h5>";
        
        // Intentar obtener la IP local
        $reflection = new ReflectionClass('QRCodeGenerator');
        $method = $reflection->getMethod('getLocalIP');
        $method->setAccessible(true);
        $localIP = $method->invoke(null);
        
        if ($localIP) {
            echo "<p><strong>IP Local detectada:</strong> {$localIP}</p>";
            echo "<p class='text-success'><i class='fas fa-check'></i> IP local encontrada automáticamente</p>";
        } else {
            echo "<p class='text-warning'><i class='fas fa-exclamation-triangle'></i> No se pudo detectar la IP local automáticamente</p>";
            echo "<p>Puede que necesites configurar manualmente la IP en el código</p>";
        }
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "
            </div>
            <div class='card-footer text-center'>
                <a href='?c=producto&a=index' class='btn btn-primary'>Ver Productos</a>
                <a href='regenerar_qr_mobile.php' class='btn btn-success'>Regenerar Todos los QR</a>
                <a href='javascript:location.reload()' class='btn btn-secondary'>Refrescar Test</a>
            </div>
        </div>
    </div>
</body>
</html>";
?>