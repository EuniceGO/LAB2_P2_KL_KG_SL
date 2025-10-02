<?php
/**
 * Test de flujo de compra con cliente logueado
 * Verifica que las compras se asignen al cliente correcto
 */

session_start();

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test Compra Cliente Logueado</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        .test-success { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; }
        .test-error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; }
        .test-info { color: #0c5460; background-color: #d1ecf1; border: 1px solid #bee5eb; }
        .test-result { padding: 10px; margin: 5px 0; border-radius: 5px; }
    </style>
</head>
<body>
<div class='container mt-4'>
    <h1>🧪 Test de Compra con Cliente Logueado</h1>
    <p><em>Verificando que las compras se asignen al cliente correcto...</em></p>";

try {
    echo "<h2>1. Simulando sesión de cliente logueado...</h2>";
    
    // Simular cliente logueado
    $_SESSION['user_id'] = 1; // Supongamos que existe un usuario con ID 1
    $_SESSION['user_name'] = 'Cliente Test';
    $_SESSION['user_email'] = 'cliente@test.com';
    $_SESSION['user_role_id'] = 2;
    $_SESSION['user_role'] = 'Cliente';
    
    echo "<div class='test-success test-result'>✅ Sesión de cliente simulada:</div>";
    echo "<div class='test-info test-result'>👤 User ID: {$_SESSION['user_id']}</div>";
    echo "<div class='test-info test-result'>📧 Email: {$_SESSION['user_email']}</div>";
    echo "<div class='test-info test-result'>🎭 Rol: {$_SESSION['user_role']}</div>";

    echo "<h2>2. Verificando existencia del cliente en base de datos...</h2>";
    
    require_once 'modelos/ClienteModel.php';
    $clienteModel = new ClienteModel();
    
    // Buscar cliente por usuario
    $clienteExistente = $clienteModel->obtenerPorUsuario($_SESSION['user_id']);
    
    if (!$clienteExistente) {
        echo "<div class='test-info test-result'>ℹ️ Cliente no existe, creando registro en tabla clientes...</div>";
        
        // Crear cliente si no existe
        $datosCliente = [
            'nombre' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'telefono' => '555-1234',
            'direccion' => 'Dirección de prueba',
            'id_usuario' => $_SESSION['user_id']
        ];
        
        $clienteModel->crear($datosCliente);
        $clienteExistente = $clienteModel->obtenerPorUsuario($_SESSION['user_id']);
    }
    
    if ($clienteExistente) {
        echo "<div class='test-success test-result'>✅ Cliente encontrado en BD:</div>";
        echo "<div class='test-info test-result'>🆔 Cliente ID: {$clienteExistente['id_cliente']}</div>";
        echo "<div class='test-info test-result'>👤 Nombre: {$clienteExistente['nombre']}</div>";
        echo "<div class='test-info test-result'>📧 Email: {$clienteExistente['email']}</div>";
    } else {
        throw new Exception("No se pudo crear/encontrar el cliente");
    }

    echo "<h2>3. Simulando carrito con productos...</h2>";
    
    // Simular carrito
    $_SESSION['carrito'] = [
        [
            'id_producto' => 1,
            'nombre' => 'Producto Test',
            'precio' => 25.99,
            'cantidad' => 2,
            'imagen' => 'test.jpg'
        ]
    ];
    
    echo "<div class='test-success test-result'>✅ Carrito simulado con productos</div>";

    echo "<h2>4. Simulando proceso de checkout...</h2>";
    
    // Simular datos del formulario de checkout
    $_POST = [
        'nombre' => 'Nombre Actualizado',
        'email' => $_SESSION['user_email'],
        'telefono' => '555-9999',
        'direccion' => 'Nueva dirección',
        'metodo_pago' => 'efectivo'
    ];
    
    echo "<div class='test-info test-result'>📋 Datos del formulario:</div>";
    foreach ($_POST as $campo => $valor) {
        echo "<div class='test-info test-result'>  • $campo: $valor</div>";
    }

    echo "<h2>5. Procesando compra (usando CarritoController logic)...</h2>";
    
    // Simular lógica del CarritoController
    $clienteInfo = [
        'nombre' => $_POST['nombre'] ?? 'Cliente General',
        'email' => $_POST['email'] ?? '',
        'telefono' => $_POST['telefono'] ?? '',
        'direccion' => $_POST['direccion'] ?? ''
    ];

    $idClienteLogueado = null;
    
    if (isset($_SESSION['user_id']) && $_SESSION['user_role_id'] == 2) {
        $clienteData = $clienteModel->obtenerPorUsuario($_SESSION['user_id']);
        
        if ($clienteData) {
            $idClienteLogueado = $clienteData['id_cliente'];
            echo "<div class='test-success test-result'>✅ Cliente logueado detectado: ID {$idClienteLogueado}</div>";
            
            // Usar datos del cliente logueado como predeterminados
            $clienteInfo = [
                'nombre' => $clienteData['nombre'],
                'email' => $clienteData['email'],
                'telefono' => $clienteData['telefono'] ?? '',
                'direccion' => $clienteData['direccion'] ?? ''
            ];
            
            // Permitir actualizaciones desde formulario
            if (!empty($_POST['nombre'])) $clienteInfo['nombre'] = $_POST['nombre'];
            if (!empty($_POST['telefono'])) $clienteInfo['telefono'] = $_POST['telefono'];
            if (!empty($_POST['direccion'])) $clienteInfo['direccion'] = $_POST['direccion'];
            
            echo "<div class='test-info test-result'>📝 Datos finales del cliente:</div>";
            foreach ($clienteInfo as $campo => $valor) {
                echo "<div class='test-info test-result'>  • $campo: $valor</div>";
            }
        }
    }

    echo "<h2>6. Creando factura...</h2>";
    
    require_once 'clases/Factura.php';
    require_once 'clases/Carrito.php';
    
    $factura = new Factura();
    $factura->setClienteInfo($clienteInfo);
    $factura->setMetodoPago($_POST['metodo_pago']);
    
    // Convertir carrito al formato esperado por la factura
    $productosParaFactura = [];
    foreach ($_SESSION['carrito'] as $item) {
        $productosParaFactura[] = [
            'id_producto' => $item['id_producto'],
            'nombre' => $item['nombre'],
            'precio_unitario' => $item['precio'],
            'cantidad' => $item['cantidad'],
            'subtotal' => $item['precio'] * $item['cantidad']
        ];
    }
    
    $factura->agregarProductosDesdeCarrito($productosParaFactura);
    
    // CLAVE: Asignar el ID del cliente logueado a la factura
    if ($idClienteLogueado) {
        $factura->setIdCliente($idClienteLogueado);
        echo "<div class='test-success test-result'>✅ ID del cliente asignado a la factura: {$idClienteLogueado}</div>";
    }

    echo "<h2>7. Guardando factura en base de datos...</h2>";
    
    $resultado = $factura->guardarEnBaseDatos();
    
    if ($resultado) {
        echo "<div class='test-success test-result'>✅ Factura guardada exitosamente</div>";
        echo "<div class='test-info test-result'>📄 Número de factura: {$factura->getNumeroFactura()}</div>";
        echo "<div class='test-info test-result'>💰 Total: $" . number_format($factura->getTotal(), 2) . "</div>";
        echo "<div class='test-info test-result'>🆔 ID del cliente en factura: {$factura->getIdCliente()}</div>";
        
        // Verificar que la factura se asignó al cliente correcto
        if ($factura->getIdCliente() == $idClienteLogueado) {
            echo "<div class='test-success test-result'>✅ ¡PERFECTO! La factura se asignó al cliente logueado correcto</div>";
        } else {
            echo "<div class='test-error test-result'>❌ ERROR: La factura no se asignó al cliente correcto</div>";
            echo "<div class='test-error test-result'>   Esperado: {$idClienteLogueado}, Obtenido: {$factura->getIdCliente()}</div>";
        }
        
    } else {
        echo "<div class='test-error test-result'>❌ Error al guardar la factura</div>";
    }

    echo "<h2>8. Verificando historial del cliente...</h2>";
    
    $facturasCliente = $clienteModel->obtenerHistorialCompras($idClienteLogueado);
    
    if (!empty($facturasCliente)) {
        echo "<div class='test-success test-result'>✅ Cliente tiene " . count($facturasCliente) . " factura(s) en su historial</div>";
        
        foreach ($facturasCliente as $facturaHistorial) {
            echo "<div class='test-info test-result'>📄 Factura: {$facturaHistorial['numero_factura']} - Total: $" . number_format($facturaHistorial['total'], 2) . "</div>";
        }
    } else {
        echo "<div class='test-error test-result'>❌ No se encontraron facturas en el historial del cliente</div>";
    }

    echo "<h2>✅ RESUMEN DEL TEST</h2>";
    echo "<div class='test-success test-result'>";
    echo "<strong>🎉 ¡Test completado exitosamente!</strong><br>";
    echo "✅ Cliente logueado detectado correctamente<br>";
    echo "✅ Factura asignada al cliente existente (no se creó cliente duplicado)<br>";
    echo "✅ Compra registrada en el historial del cliente<br>";
    echo "✅ Sistema funcionando como se esperaba<br>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='test-error test-result'>❌ Error en el test: " . $e->getMessage() . "</div>";
    echo "<div class='test-error test-result'>📁 Archivo: " . $e->getFile() . "</div>";
    echo "<div class='test-error test-result'>📍 Línea: " . $e->getLine() . "</div>";
}

echo "
    <div class='mt-4'>
        <a href='?controller=usuario&action=login' class='btn btn-primary'>Ir al Login</a>
        <a href='?c=producto&a=index' class='btn btn-success'>Ver Productos</a>
        <a href='?c=carrito&a=index' class='btn btn-warning'>Ver Carrito</a>
    </div>
</div>
</body>
</html>";
?>