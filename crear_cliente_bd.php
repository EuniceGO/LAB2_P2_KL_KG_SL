<?php
/**
 * Crear cliente en la BD para que funcione completamente
 */

session_start();

echo "<h2>👤 Crear Cliente en Base de Datos</h2>";

if (isset($_SESSION['user_id']) && $_SESSION['user_role_id'] == 2) {
    echo "<h3>Datos actuales del usuario:</h3>";
    echo "<div style='background: #e9ecef; padding: 15px; border-radius: 5px;'>";
    echo "• <strong>ID Usuario:</strong> " . $_SESSION['user_id'] . "<br>";
    echo "• <strong>Nombre:</strong> " . $_SESSION['user_name'] . "<br>";
    echo "• <strong>Email:</strong> " . $_SESSION['user_email'] . "<br>";
    echo "• <strong>Rol:</strong> " . $_SESSION['user_role'] . " (ID: " . $_SESSION['user_role_id'] . ")";
    echo "</div>";
    
    if (isset($_GET['crear'])) {
        try {
            // Crear conexión
            $conexion = new mysqli("localhost", "root", "", "productos_iniciales");
            
            if ($conexion->connect_error) {
                throw new Exception("Error de conexión: " . $conexion->connect_error);
            }
            
            // Verificar si ya existe
            $stmt = $conexion->prepare("SELECT id FROM clientes WHERE id_usuario = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "<div style='background: #fff3cd; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
                echo "⚠️ El cliente ya existe en la base de datos";
                echo "</div>";
            } else {
                // Insertar nuevo cliente
                $stmt = $conexion->prepare("
                    INSERT INTO clientes (nombre, email, telefono, direccion, id_usuario, fecha_registro) 
                    VALUES (?, ?, '', '', ?, NOW())
                ");
                $stmt->bind_param("ssi", $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_id']);
                
                if ($stmt->execute()) {
                    echo "<div style='background: #d4edda; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
                    echo "✅ <strong>Cliente creado exitosamente en la base de datos</strong><br>";
                    echo "ID del cliente: " . $conexion->insert_id;
                    echo "</div>";
                    
                    echo "<div style='background: #d1ecf1; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
                    echo "🎉 Ahora el checkout debería obtener los datos directamente de la BD en lugar del fallback";
                    echo "</div>";
                } else {
                    throw new Exception("Error al insertar: " . $conexion->error);
                }
            }
            
            $conexion->close();
            
        } catch (Exception $e) {
            echo "<div style='background: #f8d7da; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
            echo "❌ Error: " . $e->getMessage();
            echo "</div>";
        }
    }
    
    echo "<h3>Acciones:</h3>";
    if (!isset($_GET['crear'])) {
        echo "<a href='?crear=1' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-size: 16px;'>";
        echo "➕ Crear Cliente en BD";
        echo "</a><br><br>";
        
        echo "<p><small>Esto creará un registro del cliente en la tabla 'clientes' con los datos de tu sesión actual.</small></p>";
    }
    
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='debug_flujo_completo.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔍 Volver al Debug</a>";
    echo "<a href='?c=carrito&a=checkout' style='background: #fd7e14; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🛒 Ir al Checkout</a>";
    echo "</div>";
    
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "❌ No estás logueado como cliente. <a href='cambiar_tipo_usuario.php?tipo=cliente'>Cambiar a cliente</a>";
    echo "</div>";
}

// Mostrar estructura actual de la tabla clientes
echo "<h3>📋 Estructura actual de la tabla clientes:</h3>";
try {
    $conexion = new mysqli("localhost", "root", "", "productos_iniciales");
    
    if (!$conexion->connect_error) {
        $result = $conexion->query("DESCRIBE clientes");
        if ($result) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
            echo "<tr style='background: #f1f1f1;'><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['Field'] . "</td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . ($row['Key'] ?: '-') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Mostrar clientes existentes
        $result = $conexion->query("SELECT * FROM clientes LIMIT 5");
        if ($result && $result->num_rows > 0) {
            echo "<h4>Clientes existentes:</h4>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
            echo "<tr style='background: #f1f1f1;'><th>ID</th><th>Nombre</th><th>Email</th><th>ID Usuario</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . ($row['nombre'] ?? 'N/A') . "</td>";
                echo "<td>" . ($row['email'] ?? 'N/A') . "</td>";
                echo "<td>" . ($row['id_usuario'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        $conexion->close();
    }
} catch (Exception $e) {
    echo "Error al consultar BD: " . $e->getMessage();
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3 { color: #333; }
table { font-size: 14px; }
th, td { padding: 8px; text-align: left; }
a { text-decoration: none; }
a:hover { opacity: 0.8; }
</style>