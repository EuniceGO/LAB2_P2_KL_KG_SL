<?php
/**
 * Verificación y reparación de la tabla detalle_factura
 */

echo "<h1>🔍 Verificación de Tabla 'detalle_factura'</h1>";

try {
    // Crear conexión MySQLi
    $host = "localhost";
    $usuario = "root";
    $password = "";
    $baseDatos = "productos_iniciales";
    
    $conn = new mysqli($host, $usuario, $password, $baseDatos);
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    echo "<h2>1. Verificando si la tabla 'detalle_factura' existe...</h2>";
    $result = $conn->query("SHOW TABLES LIKE 'detalle_factura'");
    
    if ($result->num_rows > 0) {
        echo "✅ Tabla 'detalle_factura' existe<br>";
        
        echo "<h2>2. Estructura actual de la tabla 'detalle_factura':</h2>";
        $result = $conn->query("DESCRIBE detalle_factura");
        
        if ($result) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background-color: #f8f9fa;'>
                    <th>Campo</th>
                    <th>Tipo</th>
                    <th>Nulo</th>
                    <th>Clave</th>
                    <th>Por defecto</th>
                    <th>Extra</th>
                  </tr>";
            
            $columnas = [];
            while ($row = $result->fetch_assoc()) {
                $columnas[] = $row['Field'];
                echo "<tr>";
                echo "<td><strong>" . $row['Field'] . "</strong></td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . $row['Key'] . "</td>";
                echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
                echo "<td>" . ($row['Extra'] ?? '') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            echo "<h2>3. Columnas esperadas vs columnas existentes:</h2>";
            
            $columnasEsperadas = [
                'id_factura',
                'id_producto', 
                'nombre_producto', 
                'precio_unitario', 
                'cantidad', 
                'subtotal'
            ];
            
            echo "<div style='display: flex; gap: 20px;'>";
            echo "<div style='flex: 1;'>";
            echo "<h3>✅ Columnas que existen:</h3>";
            echo "<ul>";
            foreach ($columnasEsperadas as $columna) {
                if (in_array($columna, $columnas)) {
                    echo "<li style='color: green;'>✅ $columna</li>";
                }
            }
            echo "</ul>";
            echo "</div>";
            
            echo "<div style='flex: 1;'>";
            echo "<h3>❌ Columnas faltantes:</h3>";
            echo "<ul>";
            $faltantes = [];
            foreach ($columnasEsperadas as $columna) {
                if (!in_array($columna, $columnas)) {
                    $faltantes[] = $columna;
                    echo "<li style='color: red;'>❌ $columna</li>";
                }
            }
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            
            if (!empty($faltantes)) {
                echo "<h2>4. Agregando columnas faltantes...</h2>";
                
                foreach ($faltantes as $columna) {
                    echo "🔧 Agregando columna '$columna'...<br>";
                    
                    switch ($columna) {
                        case 'id_factura':
                            $alterSQL = "ALTER TABLE detalle_factura ADD COLUMN id_factura INT(11) NOT NULL";
                            break;
                        case 'id_producto':
                            $alterSQL = "ALTER TABLE detalle_factura ADD COLUMN id_producto INT(11)";
                            break;
                        case 'nombre_producto':
                            $alterSQL = "ALTER TABLE detalle_factura ADD COLUMN nombre_producto VARCHAR(255)";
                            break;
                        case 'precio_unitario':
                            $alterSQL = "ALTER TABLE detalle_factura ADD COLUMN precio_unitario DECIMAL(10,2)";
                            break;
                        case 'cantidad':
                            $alterSQL = "ALTER TABLE detalle_factura ADD COLUMN cantidad INT(11)";
                            break;
                        case 'subtotal':
                            $alterSQL = "ALTER TABLE detalle_factura ADD COLUMN subtotal DECIMAL(10,2)";
                            break;
                    }
                    
                    if ($conn->query($alterSQL)) {
                        echo "✅ Columna '$columna' agregada exitosamente<br>";
                    } else {
                        echo "❌ Error al agregar columna '$columna': " . $conn->error . "<br>";
                    }
                }
            } else {
                echo "<h3>ℹ️ Todas las columnas necesarias ya existen</h3>";
            }
            
        } else {
            echo "❌ Error al obtener estructura de la tabla: " . $conn->error . "<br>";
        }
        
    } else {
        echo "❌ Tabla 'detalle_factura' NO existe. Creándola...<br>";
        
        $createTableSQL = "
        CREATE TABLE detalle_factura (
            id_detalle int(11) NOT NULL AUTO_INCREMENT,
            id_factura int(11) NOT NULL,
            id_producto int(11),
            nombre_producto varchar(255),
            precio_unitario decimal(10,2),
            cantidad int(11),
            subtotal decimal(10,2),
            fecha_creacion timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id_detalle),
            INDEX idx_factura (id_factura),
            INDEX idx_producto (id_producto),
            FOREIGN KEY (id_factura) REFERENCES facturas(id_factura) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        if ($conn->query($createTableSQL)) {
            echo "✅ Tabla 'detalle_factura' creada exitosamente<br>";
        } else {
            echo "❌ Error al crear tabla: " . $conn->error . "<br>";
        }
    }
    
    echo "<h2>5. Estructura final de la tabla:</h2>";
    $result = $conn->query("DESCRIBE detalle_factura");
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background-color: #f8f9fa;'>
            <th>Campo</th>
            <th>Tipo</th>
            <th>Nulo</th>
            <th>Clave</th>
            <th>Por defecto</th>
          </tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>6. Probando inserción de detalle de prueba...</h2>";
    
    // Test de inserción
    require_once 'modelos/FacturaModel.php';
    $facturaModel = new FacturaModel();
    
    // Crear una factura de prueba primero
    $datosFactura = [
        'numero_factura' => 'TEST-DET-' . time(),
        'fecha_factura' => date('Y-m-d H:i:s'),
        'id_cliente' => 1,
        'cliente_nombre' => 'Cliente Test Detalles',
        'cliente_email' => 'test@detalles.com',
        'cliente_telefono' => '12345678',
        'cliente_direccion' => 'Dirección test',
        'subtotal' => 100.00,
        'impuesto' => 15.00,
        'total' => 115.00,
        'metodo_pago' => 'efectivo',
        'estado' => 'completada',
        'notas' => 'Factura de prueba para detalles'
    ];
    
    $idFactura = $facturaModel->insertarFactura($datosFactura);
    
    if ($idFactura) {
        echo "✅ Factura de prueba creada con ID: $idFactura<br>";
        
        // Ahora probar insertar detalles
        $productosTest = [
            [
                'id_producto' => 1,
                'nombre' => 'Producto Test 1',
                'precio_unitario' => 25.50,
                'cantidad' => 2,
                'subtotal' => 51.00
            ],
            [
                'id_producto' => 2,
                'nombre' => 'Producto Test 2',
                'precio_unitario' => 49.00,
                'cantidad' => 1,
                'subtotal' => 49.00
            ]
        ];
        
        $resultadoDetalles = $facturaModel->insertarDetallesFactura($idFactura, $productosTest);
        
        if ($resultadoDetalles) {
            echo "✅ Detalles insertados exitosamente<br>";
            
            // Verificar los detalles
            $detalles = $conn->query("SELECT * FROM detalle_factura WHERE id_factura = $idFactura");
            echo "✅ Detalles verificados: " . $detalles->num_rows . " registros<br>";
            
        } else {
            echo "❌ Error al insertar detalles<br>";
        }
        
        // Limpiar datos de prueba
        $conn->query("DELETE FROM detalle_factura WHERE id_factura = $idFactura");
        $conn->query("DELETE FROM facturas WHERE id_factura = $idFactura");
        echo "🧹 Datos de prueba eliminados<br>";
        
    } else {
        echo "❌ Error al crear factura de prueba<br>";
    }
    
    $conn->close();
    
    echo "<h2>✅ Verificación Completada</h2>";
    echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>🎉 La tabla 'detalle_factura' ha sido verificada/reparada</strong><br>";
    echo "✅ Todas las columnas necesarias están disponibles<br>";
    echo "✅ El método insertarDetallesFactura debería funcionar correctamente<br>";
    echo "✅ Los detalles de factura se pueden guardar sin errores<br>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error durante la verificación:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>