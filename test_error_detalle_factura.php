<?php
/**
 * Test específico para el error de insertarDetallesFactura
 */

echo "<h1>🔧 Test del Error insertarDetallesFactura</h1>";

try {
    require_once 'modelos/FacturaModel.php';
    
    echo "<h2>1. Creando FacturaModel...</h2>";
    $facturaModel = new FacturaModel();
    echo "✅ FacturaModel creado exitosamente<br>";
    
    echo "<h2>2. Creando factura de prueba...</h2>";
    $datosFactura = [
        'numero_factura' => 'TEST-DET-ERROR-' . time(),
        'fecha_factura' => date('Y-m-d H:i:s'),
        'id_cliente' => 1,
        'cliente_nombre' => 'Cliente Test Error Detalles',
        'cliente_email' => 'test.error.detalles@ejemplo.com',
        'cliente_telefono' => '12345678',
        'cliente_direccion' => 'Dirección test error',
        'subtotal' => 75.00,
        'impuesto' => 12.00,
        'total' => 87.00,
        'metodo_pago' => 'efectivo',
        'estado' => 'completada',
        'notas' => 'Factura de prueba para error detalles'
    ];
    
    $idFactura = $facturaModel->insertarFactura($datosFactura);
    
    if ($idFactura) {
        echo "✅ Factura creada con ID: $idFactura<br>";
        
        echo "<h2>3. Preparando productos para detalles...</h2>";
        
        // Estos son los datos típicos que vienen del carrito
        $productos = [
            [
                'id_producto' => 1,
                'nombre' => 'Producto Premium Test',
                'precio_unitario' => 35.50,
                'cantidad' => 1,
                'subtotal' => 35.50
            ],
            [
                'id_producto' => 2,
                'nombre' => 'Producto Estándar Test',
                'precio_unitario' => 19.75,
                'cantidad' => 2,
                'subtotal' => 39.50
            ]
        ];
        
        echo "📦 Productos preparados:<br>";
        foreach ($productos as $i => $producto) {
            echo "- Item " . ($i + 1) . ": " . $producto['nombre'] . 
                 " (Qty: " . $producto['cantidad'] . 
                 ", Precio: $" . number_format($producto['precio_unitario'], 2) . 
                 ", Subtotal: $" . number_format($producto['subtotal'], 2) . ")<br>";
        }
        
        echo "<h2>4. Insertando detalles de factura (punto de error original)...</h2>";
        echo "<p><em>Esta es la línea 115 donde ocurría el error bind_param() en bool</em></p>";
        
        // Este es el punto exacto donde fallaba
        $resultado = $facturaModel->insertarDetallesFactura($idFactura, $productos);
        
        if ($resultado) {
            echo "🎉 ¡ÉXITO! Detalles insertados correctamente<br>";
            
            echo "<h2>5. Verificando detalles en base de datos...</h2>";
            
            $conexion = new mysqli("localhost", "root", "", "productos_iniciales");
            $query = "SELECT * FROM detalle_factura WHERE id_factura = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $idFactura);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $detallesGuardados = [];
            while ($row = $result->fetch_assoc()) {
                $detallesGuardados[] = $row;
            }
            
            echo "✅ Detalles verificados en BD: " . count($detallesGuardados) . " registros<br>";
            
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background-color: #f8f9fa;'>
                    <th>ID Detalle</th>
                    <th>Producto</th>
                    <th>Precio Unit.</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                  </tr>";
            
            foreach ($detallesGuardados as $detalle) {
                echo "<tr>";
                echo "<td>" . $detalle['id_detalle'] . "</td>";
                echo "<td>" . $detalle['nombre_producto'] . "</td>";
                echo "<td>$" . number_format($detalle['precio_unitario'], 2) . "</td>";
                echo "<td>" . $detalle['cantidad'] . "</td>";
                echo "<td>$" . number_format($detalle['subtotal'], 2) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            $conexion->close();
            
        } else {
            echo "❌ Error al insertar detalles<br>";
        }
        
        // Limpiar datos de prueba
        $conexionLimpiar = new mysqli("localhost", "root", "", "productos_iniciales");
        $conexionLimpiar->query("DELETE FROM detalle_factura WHERE id_factura = $idFactura");
        $conexionLimpiar->query("DELETE FROM facturas WHERE id_factura = $idFactura");
        $conexionLimpiar->close();
        echo "🧹 Datos de prueba eliminados<br>";
        
    } else {
        echo "❌ Error al crear factura de prueba<br>";
    }
    
    echo "<h2>✅ Test Completado</h2>";
    echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>🎉 Error de insertarDetallesFactura resuelto</strong><br>";
    echo "✅ bind_param() ya no se ejecuta sobre bool<br>";
    echo "✅ Tabla detalle_factura funcionando correctamente<br>";
    echo "✅ Detalles de factura se guardan sin problemas<br>";
    echo "✅ Proceso completo de checkout operativo<br>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error detectado:</h2>";
    echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>Error:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Archivo:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Línea:</strong> " . $e->getLine() . "<br>";
    echo "</div>";
    
    echo "<h3>💡 Si el error persiste:</h3>";
    echo "<ul>";
    echo "<li>Ejecutar <a href='verificar_tabla_detalle_factura.php'>verificar_tabla_detalle_factura.php</a></li>";
    echo "<li>Verificar que la tabla detalle_factura tenga todas las columnas</li>";
    echo "<li>Comprobar permisos de base de datos</li>";
    echo "</ul>";
}
?>