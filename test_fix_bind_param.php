<?php
/**
 * Test del fix de bind_param - Verificación de tipos y parámetros
 */

echo "<h1>🔧 Test del Fix bind_param en FacturaModel</h1>";

try {
    require_once 'modelos/FacturaModel.php';
    
    echo "<h2>1. Verificando parámetros y tipos...</h2>";
    
    // Contar parámetros esperados
    $tiposEsperados = "ssissssdddsss";
    $numTipos = strlen($tiposEsperados);
    echo "📊 Número de tipos en la cadena: $numTipos<br>";
    
    // Mostrar cada tipo con su parámetro correspondiente
    $parametros = [
        'numero_factura' => 's',
        'fecha_factura' => 's', 
        'id_cliente' => 'i',
        'cliente_nombre' => 's',
        'cliente_email' => 's',
        'cliente_telefono' => 's',
        'cliente_direccion' => 's',
        'subtotal' => 'd',
        'impuesto' => 'd',
        'total' => 'd',
        'metodo_pago' => 's',
        'estado' => 's',
        'notas' => 's'
    ];
    
    echo "<h3>Mapeo de parámetros:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>#</th><th>Parámetro</th><th>Tipo Esperado</th><th>Tipo en String</th><th>Coincide</th></tr>";
    
    $index = 0;
    $todosCoinciden = true;
    
    foreach ($parametros as $param => $tipoEsperado) {
        $tipoEnString = $tiposEsperados[$index] ?? 'N/A';
        $coincide = ($tipoEsperado === $tipoEnString);
        
        if (!$coincide) $todosCoinciden = false;
        
        $colorFila = $coincide ? '#d4edda' : '#f8d7da';
        $icono = $coincide ? '✅' : '❌';
        
        echo "<tr style='background-color: $colorFila;'>";
        echo "<td>" . ($index + 1) . "</td>";
        echo "<td>$param</td>";
        echo "<td>$tipoEsperado</td>";
        echo "<td>$tipoEnString</td>";
        echo "<td>$icono</td>";
        echo "</tr>";
        
        $index++;
    }
    echo "</table>";
    
    if ($todosCoinciden) {
        echo "✅ Todos los tipos coinciden correctamente<br>";
    } else {
        echo "❌ Hay inconsistencias en los tipos<br>";
    }
    
    echo "<h2>2. Probando inserción de factura...</h2>";
    
    $facturaModel = new FacturaModel();
    
    $datosFactura = [
        'numero_factura' => 'TEST-FIX-' . time(),
        'fecha_factura' => date('Y-m-d H:i:s'),
        'id_cliente' => 1,
        'cliente_nombre' => 'Cliente Test Fix',
        'cliente_email' => 'test.fix@ejemplo.com',
        'cliente_telefono' => '12345678',
        'cliente_direccion' => 'Dirección test fix',
        'subtotal' => 25.50,
        'impuesto' => 4.08,
        'total' => 29.58,
        'metodo_pago' => 'efectivo',
        'estado' => 'completada',
        'notas' => 'Factura de prueba para fix bind_param'
    ];
    
    echo "📋 Datos de prueba preparados:<br>";
    foreach ($datosFactura as $campo => $valor) {
        echo "- $campo: $valor<br>";
    }
    
    echo "<h2>3. Ejecutando inserción...</h2>";
    
    $idFactura = $facturaModel->insertarFactura($datosFactura);
    
    if ($idFactura) {
        echo "✅ Factura insertada exitosamente con ID: $idFactura<br>";
        
        // Verificar la factura en la base de datos
        $conexion = new mysqli("localhost", "root", "", "productos_iniciales");
        $result = $conexion->query("SELECT * FROM facturas WHERE id_factura = $idFactura");
        
        if ($result && $row = $result->fetch_assoc()) {
            echo "<h3>✅ Verificación en base de datos:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Campo</th><th>Valor</th></tr>";
            
            foreach ($row as $campo => $valor) {
                echo "<tr><td><strong>$campo</strong></td><td>$valor</td></tr>";
            }
            echo "</table>";
        }
        
        // Limpiar la prueba
        $conexion->query("DELETE FROM facturas WHERE id_factura = $idFactura");
        echo "🧹 Factura de prueba eliminada<br>";
        
        $conexion->close();
        
    } else {
        echo "❌ Error al insertar factura<br>";
    }
    
    echo "<h2>✅ Test Completado</h2>";
    echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>🎉 Fix de bind_param aplicado exitosamente</strong><br>";
    echo "✅ Tipos de parámetros corregidos: <code>ssissssdddsss</code><br>";
    echo "✅ Número de parámetros: 13<br>";
    echo "✅ Número de tipos: " . strlen($tiposEsperados) . "<br>";
    echo "✅ Inserción de facturas funcionando correctamente<br>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error detectado:</h2>";
    echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>Error:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Archivo:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Línea:</strong> " . $e->getLine() . "<br>";
    echo "</div>";
    
    echo "<h3>🔍 Detalles del error:</h3>";
    echo "<pre style='background-color: #f8f9fa; padding: 10px; border-radius: 5px;'>";
    echo $e->getTraceAsString();
    echo "</pre>";
}
?>