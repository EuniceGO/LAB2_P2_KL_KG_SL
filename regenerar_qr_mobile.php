<?php
/**
 * Script para regenerar códigos QR con URLs para móvil
 * Este script actualiza todos los códigos QR existentes para que contengan URLs accesibles desde móvil
 */

require_once 'config/cn.php';
require_once 'modelos/ProductoModel.php';
require_once 'clases/QRCodeGenerator.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Regenerar Códigos QR para Móvil</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background-color: #f8f9fa; }
        .card { margin: 20px auto; max-width: 800px; }
        .progress { margin: 10px 0; }
        .log-item { margin: 5px 0; padding: 10px; border-radius: 5px; }
        .log-success { background-color: #d4edda; border-left: 4px solid #28a745; }
        .log-error { background-color: #f8d7da; border-left: 4px solid #dc3545; }
        .log-info { background-color: #d1ecf1; border-left: 4px solid #17a2b8; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='card'>
            <div class='card-header'>
                <h3><i class='fas fa-qrcode'></i> Regenerando Códigos QR para Móvil</h3>
            </div>
            <div class='card-body'>";

try {
    $productoModel = new ProductoModel();
    $productos = $productoModel->getAll();
    
    if (empty($productos)) {
        echo "<div class='log-info log-item'>No hay productos en la base de datos.</div>";
    } else {
        $total = count($productos);
        $exitosos = 0;
        $errores = 0;
        
        echo "<div class='log-info log-item'>Iniciando regeneración de {$total} códigos QR...</div>";
        echo "<div class='progress'><div class='progress-bar' role='progressbar' style='width: 0%'></div></div>";
        
        foreach ($productos as $index => $producto) {
            $porcentaje = (($index + 1) / $total) * 100;
            
            echo "<script>
                document.querySelector('.progress-bar').style.width = '{$porcentaje}%';
                document.querySelector('.progress-bar').textContent = '" . round($porcentaje, 1) . "%';
            </script>";
            
            try {
                echo "<div class='log-info log-item'>Procesando producto #{$producto->getIdProducto()}: {$producto->getNombre()}</div>";
                
                // Generar nuevo código QR con URL para móvil
                $qrPath = QRCodeGenerator::generateAndSaveProductQR($producto);
                
                if ($qrPath) {
                    // Actualizar la base de datos con la nueva ruta del QR
                    $producto->setCodigoQr($qrPath);
                    $productoModel->update($producto);
                    
                    echo "<div class='log-success log-item'>✓ QR generado exitosamente para: {$producto->getNombre()} (Archivo: " . basename($qrPath) . ")</div>";
                    $exitosos++;
                } else {
                    echo "<div class='log-error log-item'>✗ Error al generar QR para: {$producto->getNombre()}</div>";
                    $errores++;
                }
                
                // Flush para mostrar progreso en tiempo real
                ob_flush();
                flush();
                
                // Pequeña pausa para evitar saturar las APIs
                usleep(500000); // 0.5 segundos
                
            } catch (Exception $e) {
                echo "<div class='log-error log-item'>✗ Error en producto {$producto->getNombre()}: " . htmlspecialchars($e->getMessage()) . "</div>";
                $errores++;
            }
        }
        
        echo "<div class='progress'><div class='progress-bar bg-success' role='progressbar' style='width: 100%'>100%</div></div>";
        
        // Resumen final
        echo "<div class='mt-4'>";
        echo "<h5>Resumen de la regeneración:</h5>";
        echo "<div class='log-success log-item'>✓ Códigos QR generados exitosamente: {$exitosos}</div>";
        if ($errores > 0) {
            echo "<div class='log-error log-item'>✗ Errores: {$errores}</div>";
        }
        echo "<div class='log-info log-item'>Total procesados: {$total}</div>";
        echo "</div>";
        
        // Mostrar información sobre cómo acceder desde móvil
        echo "<div class='alert alert-info mt-4'>
            <h6><i class='fas fa-mobile-alt'></i> Instrucciones para acceso móvil:</h6>
            <ul>
                <li>Los códigos QR ahora contienen URLs accesibles desde tu red WiFi</li>
                <li>Escanea cualquier código QR con la cámara de tu celular</li>
                <li>La URL te llevará directamente a la información del producto</li>
                <li>Asegúrate de que tu celular esté conectado a la misma red WiFi que el servidor</li>
            </ul>
        </div>";
        
        // Información técnica
        $baseUrl = QRCodeGenerator::generateProductData(new Producto(1, 'test', 0, 1));
        echo "<div class='alert alert-secondary mt-3'>
            <h6><i class='fas fa-info-circle'></i> Información técnica:</h6>
            <small>
                <strong>URL base:</strong> " . htmlspecialchars(str_replace('&id=1', '', $baseUrl)) . "<br>
                <strong>IP del servidor:</strong> " . ($_SERVER['SERVER_ADDR'] ?? 'N/A') . "<br>
                <strong>Host:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "
            </small>
        </div>";
    }
    
} catch (Exception $e) {
    echo "<div class='log-error log-item'>Error general: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "
            </div>
            <div class='card-footer text-center'>
                <a href='?c=producto&a=index' class='btn btn-primary'>Volver a Productos</a>
                <a href='javascript:location.reload()' class='btn btn-secondary'>Regenerar Nuevamente</a>
            </div>
        </div>
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>