<?php
/**
 * Script de Prueba Completo - Sistema de Carrito y Facturas
 * Simula una compra completa para verificar que todo funciona
 */

session_start();
require_once 'clases/Carrito.php';
require_once 'clases/Factura.php';
require_once 'modelos/ProductoModel.php';
require_once 'modelos/ClienteModel.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Prueba Completa - Sistema de Carrito y Facturación</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
    <style>
        body { background-color: #f8f9fa; }
        .test-card { margin: 20px auto; max-width: 1200px; }
        .test-section { margin: 30px 0; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .test-result { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .test-success { background-color: #d4edda; border-left: 4px solid #28a745; }
        .test-error { background-color: #f8d7da; border-left: 4px solid #dc3545; }
        .test-info { background-color: #d1ecf1; border-left: 4px solid #17a2b8; }
        .qr-test { text-align: center; padding: 20px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='test-card'>
            <div class='card'>
                <div class='card-header bg-primary text-white'>
                    <h2><i class='fas fa-flask'></i> Prueba Completa - Sistema de Carrito y Facturación</h2>
                </div>
                <div class='card-body'>";

try {
    // Test 1: Verificar productos disponibles
    echo "<div class='test-section'>
        <h4><i class='fas fa-box'></i> Test 1: Productos Disponibles</h4>";
    
    $productoModel = new ProductoModel();
    $productos = $productoModel->getAll();
    
    if (empty($productos)) {
        echo "<div class='test-error test-result'>❌ No hay productos en la base de datos.</div>";
    } else {
        echo "<div class='test-success test-result'>✅ Se encontraron " . count($productos) . " productos.</div>";
        echo "<div class='row'>";
        foreach (array_slice($productos, 0, 3) as $producto) {
            echo "<div class='col-md-4 mb-3'>
                <div class='card'>
                    <div class='card-body'>
                        <h6>{$producto->getNombre()}</h6>
                        <p class='text-muted'>ID: {$producto->getIdProducto()} | Precio: $" . number_format($producto->getPrecio(), 2) . "</p>
                        <a href='?c=carrito&a=agregar&id={$producto->getIdProducto()}' class='btn btn-sm btn-primary'>
                            <i class='fas fa-cart-plus'></i> Agregar al Carrito
                        </a>
                    </div>
                </div>
            </div>";
        }
        echo "</div>";
    }
    echo "</div>";
    
    // Test 2: Estado actual del carrito
    echo "<div class='test-section'>
        <h4><i class='fas fa-shopping-cart'></i> Test 2: Estado Actual del Carrito</h4>";
    
    $resumenCarrito = Carrito::obtenerResumen();
    
    if ($resumenCarrito['esta_vacio']) {
        echo "<div class='test-info test-result'>📋 El carrito está vacío.</div>";
    } else {
        echo "<div class='test-success test-result'>✅ Carrito con {$resumenCarrito['cantidad_total']} productos.</div>";
        echo "<div class='card bg-light mt-3'>
            <div class='card-body'>
                <h6>Resumen del Carrito:</h6>
                <ul class='list-unstyled'>";
        foreach ($resumenCarrito['productos'] as $item) {
            echo "<li>• {$item['nombre']} - Cantidad: {$item['cantidad']} - Subtotal: $" . number_format($item['precio'] * $item['cantidad'], 2) . "</li>";
        }
        echo "</ul>
                <hr>
                <strong>Total: $" . number_format($resumenCarrito['total'], 2) . "</strong>
            </div>
        </div>";
    }
    
    echo "<div class='mt-3'>
        <a href='?c=carrito&a=index' class='btn btn-outline-primary'>
            <i class='fas fa-shopping-cart'></i> Ver Carrito Completo
        </a>
        <a href='?c=carrito&a=mobile' class='btn btn-outline-info'>
            <i class='fas fa-mobile-alt'></i> Vista Móvil del Carrito
        </a>";
    
    if (!$resumenCarrito['esta_vacio']) {
        echo "<a href='?c=carrito&a=checkout' class='btn btn-success'>
            <i class='fas fa-credit-card'></i> Ir al Checkout
        </a>";
    }
    
    echo "</div></div>";
    
    // Test 3: Generación de códigos QR para móvil
    if (!empty($productos)) {
        echo "<div class='test-section'>
            <h4><i class='fas fa-qrcode'></i> Test 3: Códigos QR para Móvil</h4>";
        
        $productoTest = $productos[0];
        $qrUrl = QRCodeGenerator::generateProductQR($productoTest, 200);
        $mobileUrl = QRCodeGenerator::generateProductData($productoTest);
        
        echo "<div class='test-success test-result'>✅ QR generado para: {$productoTest->getNombre()}</div>";
        echo "<div class='qr-test'>
            <img src='{$qrUrl}' alt='Código QR' style='max-width: 200px; border: 2px solid #ddd; border-radius: 10px;'>
            <p class='mt-3'><strong>URL del móvil:</strong><br>
            <code>" . htmlspecialchars($mobileUrl) . "</code></p>
            <a href='{$mobileUrl}' target='_blank' class='btn btn-primary'>
                <i class='fas fa-mobile-alt'></i> Probar Vista Móvil
            </a>
        </div>";
        echo "</div>";
    }
    
    // Test 4: Simulación de factura
    if (!$resumenCarrito['esta_vacio']) {
        echo "<div class='test-section'>
            <h4><i class='fas fa-receipt'></i> Test 4: Simulación de Factura</h4>";
        
        try {
            $factura = new Factura();
            $factura->setClienteInfo([
                'nombre' => 'Cliente de Prueba',
                'email' => 'test@ejemplo.com',
                'telefono' => '(123) 456-7890'
            ]);
            $factura->agregarProductosDesdeCarrito(Carrito::obtenerDatosParaFactura());
            
            echo "<div class='test-success test-result'>✅ Factura generada: {$factura->getNumeroFactura()}</div>";
            echo "<div class='test-info test-result'>💰 Total de la factura: $" . number_format($factura->getTotal(), 2) . "</div>";
            
            echo "<div class='mt-3'>
                <form method='POST' action='?c=carrito&a=procesar' style='display: inline;'>
                    <input type='hidden' name='nombre' value='Cliente de Prueba'>
                    <input type='hidden' name='email' value='test@ejemplo.com'>
                    <input type='hidden' name='telefono' value='(123) 456-7890'>
                    <button type='submit' class='btn btn-success' onclick='return confirm(\"¿Procesar compra y generar factura real?\")'>
                        <i class='fas fa-receipt'></i> Generar Factura Real
                    </button>
                </form>
            </div>";
            
        } catch (Exception $e) {
            echo "<div class='test-error test-result'>❌ Error al simular factura: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        
        echo "</div>";
    }
    
    // Test 5: URLs y navegación
    echo "<div class='test-section'>
        <h4><i class='fas fa-link'></i> Test 5: Enlaces del Sistema</h4>";
    
    $enlaces = [
        'Productos' => '?c=producto&a=index',
        'Carrito Web' => '?c=carrito&a=index',
        'Carrito Móvil' => '?c=carrito&a=mobile',
        'Regenerar QR' => 'regenerar_qr_mobile.php',
        'Test QR Móvil' => 'test_qr_mobile.php'
    ];
    
    echo "<div class='row'>";
    foreach ($enlaces as $nombre => $url) {
        echo "<div class='col-md-4 mb-2'>
            <a href='{$url}' class='btn btn-outline-secondary w-100'>
                <i class='fas fa-external-link-alt'></i> {$nombre}
            </a>
        </div>";
    }
    echo "</div></div>";
    
    // Información del sistema
    echo "<div class='test-section'>
        <h4><i class='fas fa-info-circle'></i> Información del Sistema</h4>";
    
    echo "<div class='row'>
        <div class='col-md-6'>
            <h6>🌐 Configuración de Red:</h6>
            <ul class='list-unstyled'>
                <li><strong>IP del servidor:</strong> " . ($_SERVER['SERVER_ADDR'] ?? 'N/A') . "</li>
                <li><strong>Host:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "</li>
                <li><strong>Puerto:</strong> " . ($_SERVER['SERVER_PORT'] ?? 'N/A') . "</li>
                <li><strong>Protocolo:</strong> " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'HTTPS' : 'HTTP') . "</li>
            </ul>
        </div>
        <div class='col-md-6'>
            <h6>📊 Estado de Sesiones:</h6>
            <ul class='list-unstyled'>
                <li><strong>Session ID:</strong> " . (session_id() ?: 'No iniciada') . "</li>
                <li><strong>Productos en carrito:</strong> {$resumenCarrito['cantidad_total']}</li>
                <li><strong>Total en carrito:</strong> $" . number_format($resumenCarrito['total'], 2) . "</li>
                <li><strong>Timestamp:</strong> " . date('Y-m-d H:i:s') . "</li>
            </ul>
        </div>
    </div></div>";
    
    // Test 7: Sistema de Clientes
    echo "<div class='test-section'>
        <h4><i class='fas fa-users'></i> Test 7: Sistema de Gestión de Clientes</h4>";
    
    try {
        $clienteModel = new ClienteModel();
        
        // Prueba 1: Validar datos
        $datosCliente = [
            'nombre' => 'Cliente de Prueba Sistema',
            'email' => 'test.sistema@correo.com',
            'telefono' => '555-0199',
            'direccion' => 'Calle de Prueba 456'
        ];
        
        $errores = $clienteModel->validarDatos($datosCliente);
        if (empty($errores)) {
            echo "<div class='test-success test-result'>✅ Validación de datos de cliente correcta.</div>";
        } else {
            echo "<div class='test-error test-result'>❌ Error en validación: " . implode(', ', $errores) . "</div>";
        }
        
        // Prueba 2: Insertar cliente
        $idCliente = $clienteModel->insertarOActualizar($datosCliente);
        if ($idCliente) {
            echo "<div class='test-success test-result'>✅ Cliente guardado con ID: $idCliente</div>";
            
            // Prueba 3: Buscar cliente
            $clienteBuscado = $clienteModel->buscarPorEmail($datosCliente['email']);
            if ($clienteBuscado) {
                echo "<div class='test-success test-result'>✅ Cliente encontrado por email.</div>";
            } else {
                echo "<div class='test-error test-result'>❌ No se pudo encontrar cliente por email.</div>";
            }
            
            // Prueba 4: Contar clientes
            $totalClientes = $clienteModel->contarTotal();
            echo "<div class='test-info test-result'>📊 Total de clientes en sistema: $totalClientes</div>";
            
        } else {
            echo "<div class='test-error test-result'>❌ Error al guardar cliente.</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='test-error test-result'>❌ Error en prueba de clientes: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='test-error test-result'>❌ Error general: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "
            </div>
            <div class='card-footer text-center'>
                <div class='btn-group' role='group'>
                    <a href='?c=producto&a=index' class='btn btn-primary'>Ver Productos</a>
                    <a href='?c=carrito&a=index' class='btn btn-success'>Ver Carrito</a>
                    <a href='regenerar_qr_mobile.php' class='btn btn-info'>Regenerar QR</a>
                    <a href='javascript:location.reload()' class='btn btn-secondary'>Refrescar Test</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>";
?>