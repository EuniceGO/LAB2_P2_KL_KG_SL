<?php
/**
 * Test específico para diagnosticar el error de FacturaModel
 */

echo "<h1>🔧 Diagnóstico del Error en FacturaModel</h1>";

try {
    require_once 'modelos/FacturaModel.php';
    
    echo "<h2>1. Creando FacturaModel...</h2>";
    $facturaModel = new FacturaModel();
    echo "✅ FacturaModel creado exitosamente<br>";
    
    echo "<h2>2. Preparando datos de prueba para factura...</h2>";
    $datosFactura = [
        'numero_factura' => 'FAC-' . time(),
        'fecha_factura' => date('Y-m-d H:i:s'),
        'id_cliente' => 1,
        'cliente_nombre' => 'Cliente Test',
        'cliente_email' => 'test@ejemplo.com',
        'cliente_telefono' => '12345678',
        'cliente_direccion' => 'Dirección test',
        'subtotal' => 100.00,
        'impuesto' => 15.00,
        'total' => 115.00,
        'metodo_pago' => 'efectivo',
        'estado' => 'completada',
        'notas' => 'Factura de prueba'
    ];
    
    echo "📋 Datos preparados:<br>";
    foreach ($datosFactura as $campo => $valor) {
        echo "- $campo: $valor<br>";
    }
    
    echo "<h2>3. Intentando insertar factura...</h2>";
    
    $idFactura = $facturaModel->insertarFactura($datosFactura);
    
    if ($idFactura) {
        echo "✅ Factura insertada exitosamente con ID: $idFactura<br>";
    } else {
        echo "❌ Error al insertar factura<br>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Error capturado:</h2>";
    echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>Mensaje de error:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Archivo:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Línea:</strong> " . $e->getLine() . "<br>";
    echo "</div>";
    
    echo "<h3>💡 Posibles soluciones:</h3>";
    echo "<ul>";
    echo "<li>Verificar que la tabla 'facturas' existe</li>";
    echo "<li>Comprobar que todas las columnas requeridas existen en la tabla</li>";
    echo "<li>Revisar los tipos de datos de las columnas</li>";
    echo "<li>Verificar permisos de base de datos</li>";
    echo "</ul>";
    
    echo "<h3>🔧 Acción recomendada:</h3>";
    echo "<p>Ejecutar: <a href='verificar_tabla_facturas.php' target='_blank'>verificar_tabla_facturas.php</a> para ver la estructura de la tabla.</p>";
}

?>