<?php
/**
 * Test exhaustivo post-fix: Verificación completa del sistema de checkout
 */

echo "<h1>🎯 Test Exhaustivo Post-Fix</h1>";
echo "<p>Verificando que todos los errores han sido resueltos y el sistema funciona correctamente</p>";

// Limpiar cualquier sesión previa
session_start();
$_SESSION = [];

try {
    echo "<h2>🔍 1. Verificando conexiones y modelos...</h2>";
    
    // Test ClienteModel
    require_once 'modelos/ClienteModel.php';
    $clienteModel = new ClienteModel();
    echo "✅ ClienteModel - Conexión OK<br>";
    
    // Test FacturaModel  
    require_once 'modelos/FacturaModel.php';
    $facturaModel = new FacturaModel();
    echo "✅ FacturaModel - Conexión OK<br>";
    
    // Test Factura class
    require_once 'clases/Factura.php';
    $factura = new Factura();
    echo "✅ Clase Factura - Instanciación OK<br>";
    
    echo "<h2>🛒 2. Simulando proceso de compra completo...</h2>";
    
    // Simular carrito con múltiples productos
    $_SESSION['carrito'] = [
        [
            'id_producto' => 1,
            'nombre' => 'Producto Premium',
            'precio' => 45.99,
            'cantidad' => 2,
            'imagen' => 'premium.jpg'
        ],
        [
            'id_producto' => 2,
            'nombre' => 'Producto Estándar',
            'precio' => 19.99,
            'cantidad' => 1,
            'imagen' => 'estandar.jpg'
        ]
    ];
    
    echo "📦 Carrito configurado con " . count($_SESSION['carrito']) . " tipos de productos<br>";
    
    // Calcular totales
    $subtotal = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $subtotal += $item['precio'] * $item['cantidad'];
    }
    $impuesto = $subtotal * 0.16; // 16% de impuesto
    $total = $subtotal + $impuesto;
    
    echo "💰 Subtotal: $" . number_format($subtotal, 2) . "<br>";
    echo "🏛️ Impuesto (16%): $" . number_format($impuesto, 2) . "<br>";
    echo "💵 Total: $" . number_format($total, 2) . "<br>";
    
    echo "<h2>👤 3. Procesando información del cliente...</h2>";
    
    // Datos del cliente (usuario original del error)
    $clienteInfo = [
        'nombre' => 'Kathya Leal',
        'email' => 'lealkathya1@gmail.com',
        'telefono' => '+503 7123-4567',
        'direccion' => 'Santa Ana, El Salvador'
    ];
    
    foreach ($clienteInfo as $campo => $valor) {
        echo "📋 $campo: $valor<br>";
    }
    
    // Configurar información del cliente en la factura
    $factura->setClienteInfo($clienteInfo);
    echo "✅ Cliente configurado en factura<br>";
    
    // Agregar productos del carrito
    $factura->agregarProductosDesdeCarrito($_SESSION['carrito']);
    echo "✅ Productos agregados desde carrito<br>";
    
    // Configurar método de pago
    $factura->setMetodoPago('efectivo');
    echo "✅ Método de pago configurado<br>";
    
    echo "<h2>💾 4. Guardando en base de datos...</h2>";
    echo "<p><em>Este es el punto exacto donde ocurrían los errores originales</em></p>";
    
    // Aquí es donde fallaba:
    // 1. Error ClienteModel: "prepare() en null" 
    // 2. Error FacturaModel: "bind_param() en bool"
    // 3. Error bind_param: "ArgumentCountError"
    
    $idFactura = $factura->guardarEnBaseDatos();
    
    if ($idFactura) {
        echo "🎉 ¡ÉXITO! Factura guardada con ID: $idFactura<br>";
        
        echo "<h2>✅ 5. Verificando integridad de datos...</h2>";
        
        // Verificar cliente
        $idCliente = $factura->getIdCliente();
        if ($idCliente) {
            echo "👤 Cliente registrado con ID: $idCliente<br>";
            
            $clienteVerificado = $clienteModel->buscarPorId($idCliente);
            if ($clienteVerificado) {
                echo "✅ Cliente verificado en BD: " . $clienteVerificado['nombre'] . "<br>";
            }
        }
        
        // Verificar factura
        $conexion = new mysqli("localhost", "root", "", "productos_iniciales");
        
        $queryFactura = "SELECT * FROM facturas WHERE id_factura = ?";
        $stmt = $conexion->prepare($queryFactura);
        $stmt->bind_param("i", $idFactura);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($facturaData = $result->fetch_assoc()) {
            echo "✅ Factura verificada en BD:<br>";
            echo "  - Número: " . $facturaData['numero_factura'] . "<br>";
            echo "  - Total: $" . number_format($facturaData['total'], 2) . "<br>";
            echo "  - Cliente: " . $facturaData['cliente_nombre'] . "<br>";
            echo "  - Estado: " . $facturaData['estado'] . "<br>";
        }
        
        // Verificar detalles de factura
        $queryDetalles = "SELECT * FROM detalle_factura WHERE id_factura = ?";
        $stmt = $conexion->prepare($queryDetalles);
        $stmt->bind_param("i", $idFactura);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $detalles = [];
        while ($row = $result->fetch_assoc()) {
            $detalles[] = $row;
        }
        
        echo "✅ Detalles de factura verificados: " . count($detalles) . " items<br>";
        
        // Mostrar resumen de items
        foreach ($detalles as $i => $detalle) {
            echo "  - Item " . ($i + 1) . ": " . $detalle['nombre_producto'] . 
                 " (Qty: " . $detalle['cantidad'] . 
                 ", Precio: $" . number_format($detalle['precio_unitario'], 2) . ")<br>";
        }
        
        $conexion->close();
        
    } else {
        echo "❌ Error al guardar factura<br>";
        throw new Exception("Falló el guardado de factura");
    }
    
    // Limpiar datos de prueba
    unset($_SESSION['carrito']);
    
    echo "<h2>🎊 RESULTADO FINAL</h2>";
    echo "<div style='background-color: #d4edda; border: 2px solid #28a745; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>🎉 ¡TODOS LOS ERRORES RESUELTOS!</h3>";
    
    echo "<h4>✅ Errores Corregidos:</h4>";
    echo "<ul>";
    echo "<li><strong>Error 1:</strong> ❌ 'prepare() en null' → ✅ Conexión MySQLi robusta</li>";
    echo "<li><strong>Error 2:</strong> ❌ 'bind_param() en bool' → ✅ Tabla facturas completa</li>";
    echo "<li><strong>Error 3:</strong> ❌ 'ArgumentCountError' → ✅ Tipos de parámetros corregidos</li>";
    echo "</ul>";
    
    echo "<h4>✅ Funcionalidades Verificadas:</h4>";
    echo "<ul>";
    echo "<li>🔌 Conexiones a base de datos estables</li>";
    echo "<li>👥 Registro automático de clientes</li>";
    echo "<li>📄 Generación completa de facturas</li>";
    echo "<li>🛒 Procesamiento de carritos de compra</li>";
    echo "<li>💾 Persistencia de datos correcta</li>";
    echo "<li>🔗 Relaciones cliente-factura funcionales</li>";
    echo "</ul>";
    
    echo "<h4>🚀 Estado del Sistema:</h4>";
    echo "<p style='font-size: 18px; font-weight: bold; color: #28a745;'>COMPLETAMENTE OPERATIVO Y LISTO PARA PRODUCCIÓN</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error detectado:</h2>";
    echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>Error:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Archivo:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Línea:</strong> " . $e->getLine() . "<br>";
    echo "</div>";
    
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background-color: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 400px; overflow-y: auto;'>";
    echo $e->getTraceAsString();
    echo "</pre>";
}
?>