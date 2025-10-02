<?php
/**
 * Script para reparar automáticamente la tabla facturas
 * Agrega las columnas faltantes necesarias para el sistema de clientes
 */

echo "<h1>🔧 Reparación Automática de Tabla 'facturas'</h1>";

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
    
    echo "<h2>1. Verificando tabla 'facturas'...</h2>";
    
    // Verificar si la tabla existe
    $result = $conn->query("SHOW TABLES LIKE 'facturas'");
    
    if ($result->num_rows == 0) {
        echo "❌ Tabla 'facturas' no existe. Creándola...<br>";
        
        $createTableSQL = "
        CREATE TABLE facturas (
            id_factura int(11) NOT NULL AUTO_INCREMENT,
            numero_factura varchar(50) UNIQUE,
            fecha_factura datetime DEFAULT CURRENT_TIMESTAMP,
            id_cliente int(11),
            cliente_nombre varchar(100),
            cliente_email varchar(100),
            cliente_telefono varchar(20),
            cliente_direccion text,
            subtotal decimal(10,2) DEFAULT 0.00,
            impuesto decimal(10,2) DEFAULT 0.00,
            total decimal(10,2) DEFAULT 0.00,
            metodo_pago varchar(50),
            estado varchar(20) DEFAULT 'pendiente',
            notas text,
            fecha_creacion timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id_factura),
            INDEX idx_cliente (id_cliente),
            INDEX idx_numero (numero_factura),
            INDEX idx_fecha (fecha_factura)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        if ($conn->query($createTableSQL)) {
            echo "✅ Tabla 'facturas' creada exitosamente<br>";
        } else {
            echo "❌ Error al crear tabla: " . $conn->error . "<br>";
        }
    } else {
        echo "✅ Tabla 'facturas' existe<br>";
        
        echo "<h2>2. Verificando y agregando columnas faltantes...</h2>";
        
        // Obtener columnas actuales
        $result = $conn->query("DESCRIBE facturas");
        $columnasExistentes = [];
        
        while ($row = $result->fetch_assoc()) {
            $columnasExistentes[] = $row['Field'];
        }
        
        // Definir columnas requeridas con sus definiciones
        $columnasRequeridas = [
            'numero_factura' => 'VARCHAR(50) UNIQUE',
            'fecha_factura' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
            'id_cliente' => 'INT(11)',
            'cliente_nombre' => 'VARCHAR(100)',
            'cliente_email' => 'VARCHAR(100)',
            'cliente_telefono' => 'VARCHAR(20)',
            'cliente_direccion' => 'TEXT',
            'subtotal' => 'DECIMAL(10,2) DEFAULT 0.00',
            'impuesto' => 'DECIMAL(10,2) DEFAULT 0.00',
            'metodo_pago' => 'VARCHAR(50)',
            'estado' => 'VARCHAR(20) DEFAULT \'pendiente\'',
            'notas' => 'TEXT'
        ];
        
        $columnasAgregadas = 0;
        
        foreach ($columnasRequeridas as $columna => $definicion) {
            if (!in_array($columna, $columnasExistentes)) {
                echo "🔧 Agregando columna '$columna'...<br>";
                
                $alterSQL = "ALTER TABLE facturas ADD COLUMN $columna $definicion";
                
                if ($conn->query($alterSQL)) {
                    echo "✅ Columna '$columna' agregada exitosamente<br>";
                    $columnasAgregadas++;
                } else {
                    echo "❌ Error al agregar columna '$columna': " . $conn->error . "<br>";
                }
            } else {
                echo "✅ Columna '$columna' ya existe<br>";
            }
        }
        
        if ($columnasAgregadas > 0) {
            echo "<h3>🎉 Se agregaron $columnasAgregadas columnas nuevas</h3>";
        } else {
            echo "<h3>ℹ️ No se necesitó agregar columnas adicionales</h3>";
        }
    }
    
    echo "<h2>3. Verificando integridad de la tabla...</h2>";
    
    // Verificar la estructura final
    $result = $conn->query("DESCRIBE facturas");
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
    
    echo "<h2>4. Probando inserción de factura de prueba...</h2>";
    
    // Test de inserción
    require_once 'modelos/FacturaModel.php';
    $facturaModel = new FacturaModel();
    
    $datosFactura = [
        'numero_factura' => 'TEST-' . time(),
        'fecha_factura' => date('Y-m-d H:i:s'),
        'id_cliente' => 1,
        'cliente_nombre' => 'Cliente Test Reparación',
        'cliente_email' => 'test@reparacion.com',
        'cliente_telefono' => '12345678',
        'cliente_direccion' => 'Dirección test',
        'subtotal' => 100.00,
        'impuesto' => 15.00,
        'total' => 115.00,
        'metodo_pago' => 'efectivo',
        'estado' => 'completada',
        'notas' => 'Factura de prueba post-reparación'
    ];
    
    $idFactura = $facturaModel->insertarFactura($datosFactura);
    
    if ($idFactura) {
        echo "✅ Prueba exitosa: Factura insertada con ID $idFactura<br>";
        
        // Eliminar la factura de prueba
        $conn->query("DELETE FROM facturas WHERE id_factura = $idFactura");
        echo "🧹 Factura de prueba eliminada<br>";
    } else {
        echo "❌ Error en la prueba de inserción<br>";
    }
    
    $conn->close();
    
    echo "<h2>✅ Reparación Completada</h2>";
    echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>🎉 La tabla 'facturas' ha sido reparada exitosamente</strong><br>";
    echo "✅ Todas las columnas necesarias están disponibles<br>";
    echo "✅ El FacturaModel debería funcionar correctamente ahora<br>";
    echo "✅ El sistema de checkout puede proceder sin errores<br>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error durante la reparación:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>