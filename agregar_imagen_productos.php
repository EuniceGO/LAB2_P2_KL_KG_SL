<?php
require_once 'config/cn.php';

try {
    echo "<h3>Agregando campo imagen_url a la tabla productos...</h3>";
    
    // Verificar si el campo ya existe
    $checkQuery = "SHOW COLUMNS FROM productos LIKE 'imagen_url'";
    $checkResult = $conexion->query($checkQuery);
    
    if ($checkResult->num_rows == 0) {
        // El campo no existe, lo agregamos
        $alterQuery = "ALTER TABLE productos ADD COLUMN imagen_url VARCHAR(500) NULL AFTER precio";
        
        if ($conexion->query($alterQuery) === TRUE) {
            echo "✅ Campo 'imagen_url' agregado correctamente a la tabla productos.<br>";
            
            // Opcional: Agregar algunas imágenes de ejemplo
            $updateQuery = "UPDATE productos SET imagen_url = CASE 
                WHEN id = 1 THEN 'https://img.freepik.com/vector-gratis/ilustracion-sol-dibujos-animados-sonrisa-feliz_1308-179974.jpg?semt=ais_hybrid&w=740&q=80'
                WHEN id = 2 THEN 'https://img.freepik.com/vector-premium/dibujos-animados-luna-durmiendo-nubes_29190-7932.jpg'
                WHEN id = 3 THEN 'https://img.freepik.com/vector-gratis/ilustracion-estrella-dibujos-animados_1308-177539.jpg'
                WHEN id = 4 THEN 'https://img.freepik.com/vector-premium/dibujos-animados-arco-iris-lindo_29190-7940.jpg'
                ELSE imagen_url
            END WHERE id IN (1, 2, 3, 4)";
            
            if ($conexion->query($updateQuery) === TRUE) {
                echo "✅ Imágenes de ejemplo agregadas a productos existentes.<br>";
            }
            
        } else {
            echo "❌ Error al agregar el campo: " . $conexion->error . "<br>";
        }
    } else {
        echo "ℹ️ El campo 'imagen_url' ya existe en la tabla productos.<br>";
    }
    
    // Mostrar estructura actual de la tabla
    echo "<h4>Estructura actual de la tabla productos:</h4>";
    $showQuery = "DESCRIBE productos";
    $result = $conexion->query($showQuery);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background-color: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h4>Productos con imágenes:</h4>";
    $productosQuery = "SELECT id, nombre, precio, imagen_url FROM productos";
    $productosResult = $conexion->query($productosQuery);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background-color: #f0f0f0;'><th>ID</th><th>Nombre</th><th>Precio</th><th>Imagen URL</th></tr>";
    
    while ($producto = $productosResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $producto['id'] . "</td>";
        echo "<td>" . $producto['nombre'] . "</td>";
        echo "<td>$" . $producto['precio'] . "</td>";
        echo "<td>" . ($producto['imagen_url'] ? substr($producto['imagen_url'], 0, 50) . "..." : "Sin imagen") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

$conexion->close();
?>