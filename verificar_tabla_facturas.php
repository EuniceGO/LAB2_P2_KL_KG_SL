<?php
/**
 * Verificar estructura de la tabla facturas
 */

echo "<h1>🔍 Verificación de Estructura de Tabla 'facturas'</h1>";

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
    
    echo "<h2>1. Verificando si la tabla 'facturas' existe...</h2>";
    $result = $conn->query("SHOW TABLES LIKE 'facturas'");
    
    if ($result->num_rows > 0) {
        echo "✅ Tabla 'facturas' existe<br>";
        
        echo "<h2>2. Estructura actual de la tabla 'facturas':</h2>";
        $result = $conn->query("DESCRIBE facturas");
        
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
                'numero_factura', 
                'fecha_factura', 
                'id_cliente',
                'cliente_nombre', 
                'cliente_email', 
                'cliente_telefono', 
                'cliente_direccion', 
                'subtotal', 
                'impuesto', 
                'total', 
                'metodo_pago', 
                'estado',
                'notas'
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
                echo "<h2>4. SQL para agregar columnas faltantes:</h2>";
                echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                echo "<pre>";
                foreach ($faltantes as $columna) {
                    switch ($columna) {
                        case 'numero_factura':
                            echo "ALTER TABLE facturas ADD COLUMN numero_factura VARCHAR(50) UNIQUE;\n";
                            break;
                        case 'fecha_factura':
                            echo "ALTER TABLE facturas ADD COLUMN fecha_factura DATETIME DEFAULT CURRENT_TIMESTAMP;\n";
                            break;
                        case 'id_cliente':
                            echo "ALTER TABLE facturas ADD COLUMN id_cliente INT(11);\n";
                            break;
                        case 'cliente_nombre':
                            echo "ALTER TABLE facturas ADD COLUMN cliente_nombre VARCHAR(100);\n";
                            break;
                        case 'cliente_email':
                            echo "ALTER TABLE facturas ADD COLUMN cliente_email VARCHAR(100);\n";
                            break;
                        case 'cliente_telefono':
                            echo "ALTER TABLE facturas ADD COLUMN cliente_telefono VARCHAR(20);\n";
                            break;
                        case 'cliente_direccion':
                            echo "ALTER TABLE facturas ADD COLUMN cliente_direccion TEXT;\n";
                            break;
                        case 'subtotal':
                            echo "ALTER TABLE facturas ADD COLUMN subtotal DECIMAL(10,2) DEFAULT 0.00;\n";
                            break;
                        case 'impuesto':
                            echo "ALTER TABLE facturas ADD COLUMN impuesto DECIMAL(10,2) DEFAULT 0.00;\n";
                            break;
                        case 'metodo_pago':
                            echo "ALTER TABLE facturas ADD COLUMN metodo_pago VARCHAR(50);\n";
                            break;
                        case 'estado':
                            echo "ALTER TABLE facturas ADD COLUMN estado VARCHAR(20) DEFAULT 'pendiente';\n";
                            break;
                        case 'notas':
                            echo "ALTER TABLE facturas ADD COLUMN notas TEXT;\n";
                            break;
                    }
                }
                echo "</pre>";
                echo "</div>";
            }
            
        } else {
            echo "❌ Error al obtener estructura de la tabla: " . $conn->error . "<br>";
        }
        
    } else {
        echo "❌ Tabla 'facturas' NO existe<br>";
        echo "<p>Se necesita crear la tabla primero.</p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>