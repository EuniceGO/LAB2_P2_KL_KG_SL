<?php
require_once 'config/cn.php';

try {
    echo "<h2>🔧 Configurando Sistema de Clientes-Usuarios</h2>";
    
    // 1. Agregar columna id_usuario a la tabla clientes
    echo "<h3>1. ✅ Modificando tabla clientes...</h3>";
    
    $checkColumn = "SHOW COLUMNS FROM clientes LIKE 'id_usuario'";
    $result = $conexion->query($checkColumn);
    
    if ($result->num_rows == 0) {
        $alterQuery = "ALTER TABLE clientes ADD COLUMN id_usuario INT NULL AFTER id_cliente";
        
        if ($conexion->query($alterQuery) === TRUE) {
            echo "✅ Columna 'id_usuario' agregada a la tabla clientes<br>";
        } else {
            echo "❌ Error al agregar columna: " . $conexion->error . "<br>";
        }
    } else {
        echo "ℹ️ La columna 'id_usuario' ya existe en la tabla clientes<br>";
    }
    
    // 2. Crear rol 'Cliente' si no existe
    echo "<h3>2. ✅ Verificando rol 'Cliente'...</h3>";
    
    $checkRole = "SELECT * FROM roles WHERE nombre = 'Cliente'";
    $roleResult = $conexion->query($checkRole);
    
    if ($roleResult->num_rows == 0) {
        $insertRole = "INSERT INTO roles (nombre, descripcion) VALUES ('Cliente', 'Cliente del sistema - puede ver sus facturas')";
        
        if ($conexion->query($insertRole) === TRUE) {
            echo "✅ Rol 'Cliente' creado exitosamente<br>";
        } else {
            echo "❌ Error al crear rol: " . $conexion->error . "<br>";
        }
    } else {
        echo "ℹ️ El rol 'Cliente' ya existe<br>";
    }
    
    // 3. Mostrar estructura actualizada
    echo "<h3>3. 📊 Estructura actualizada:</h3>";
    
    echo "<h4>Tabla clientes:</h4>";
    $showClientes = "DESCRIBE clientes";
    $result = $conexion->query($showClientes);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background-color: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h4>Roles disponibles:</h4>";
    $showRoles = "SELECT * FROM roles";
    $result = $conexion->query($showRoles);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background-color: #f0f0f0;'><th>ID</th><th>Nombre</th><th>Descripción</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_role'] . "</td>";
        echo "<td>" . $row['nombre'] . "</td>";
        echo "<td>" . $row['descripcion'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>4. 🎯 Próximos pasos:</h3>";
    echo "<ol>";
    echo "<li>✅ <strong>Estructura de base de datos lista</strong></li>";
    echo "<li>🔄 <strong>Crear formulario de registro de clientes</strong></li>";
    echo "<li>🔄 <strong>Modificar proceso de login</strong></li>";
    echo "<li>🔄 <strong>Crear panel de cliente</strong></li>";
    echo "<li>🔄 <strong>Modificar proceso de checkout</strong></li>";
    echo "</ol>";
    
    echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>✅ Base de datos configurada correctamente</h3>";
    echo "<p><strong>Cambios realizados:</strong></p>";
    echo "<ul>";
    echo "<li>📊 Campo 'id_usuario' agregado a tabla clientes</li>";
    echo "<li>👤 Rol 'Cliente' creado para usuarios clientes</li>";
    echo "<li>🔗 Relación clientes-usuarios preparada</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

$conexion->close();
?>