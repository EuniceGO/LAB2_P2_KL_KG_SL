<?php
/**
 * Debug de la estructura de facturas y relación con usuarios
 */

echo "<h2>🔍 Debug de Facturas y Usuarios</h2>";

session_start();

echo "<h3>📱 Usuario Actual:</h3>";
if (isset($_SESSION['user_id'])) {
    echo "<div style='background: #e9ecef; padding: 15px; border-radius: 5px;'>";
    echo "• <strong>User ID:</strong> " . $_SESSION['user_id'] . "<br>";
    echo "• <strong>Nombre:</strong> " . $_SESSION['user_name'] . "<br>";
    echo "• <strong>Email:</strong> " . $_SESSION['user_email'] . "<br>";
    echo "• <strong>Rol:</strong> " . $_SESSION['user_role'] . " (ID: " . $_SESSION['user_role_id'] . ")";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px;'>❌ No hay usuario logueado</div>";
}

try {
    $conexion = new mysqli("localhost", "root", "", "productos_iniciales");
    
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }
    
    // Verificar estructura de la tabla facturas
    echo "<h3>📋 Estructura de la tabla 'facturas':</h3>";
    $result = $conexion->query("DESCRIBE facturas");
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background: #f1f1f1;'><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><strong>" . $row['Field'] . "</strong></td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . ($row['Key'] ?: '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Verificar estructura de la tabla clientes
    echo "<h3>👤 Estructura de la tabla 'clientes':</h3>";
    $result = $conexion->query("DESCRIBE clientes");
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background: #f1f1f1;'><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><strong>" . $row['Field'] . "</strong></td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . ($row['Key'] ?: '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Mostrar facturas existentes (si las hay)
    echo "<h3>🧾 Facturas existentes:</h3>";
    $result = $conexion->query("SELECT COUNT(*) as total FROM facturas");
    $row = $result->fetch_assoc();
    echo "<strong>Total de facturas:</strong> " . $row['total'] . "<br><br>";
    
    if ($row['total'] > 0) {
        $result = $conexion->query("SELECT * FROM facturas ORDER BY fecha_factura DESC LIMIT 10");
        if ($result && $result->num_rows > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
            echo "<tr style='background: #f1f1f1;'>";
            echo "<th>ID</th><th>Número</th><th>Fecha</th><th>ID Cliente</th><th>Cliente Nombre</th><th>Total</th>";
            echo "</tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id_factura'] . "</td>";
                echo "<td>" . ($row['numero_factura'] ?? 'N/A') . "</td>";
                echo "<td>" . ($row['fecha_factura'] ?? 'N/A') . "</td>";
                echo "<td>" . ($row['id_cliente'] ?? 'N/A') . "</td>";
                echo "<td>" . ($row['cliente_nombre'] ?? 'N/A') . "</td>";
                echo "<td>$" . number_format($row['total'] ?? 0, 2) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
        echo "⚠️ No hay facturas en la base de datos";
        echo "</div>";
    }
    
    // Mostrar clientes existentes
    echo "<h3>👥 Clientes existentes:</h3>";
    $result = $conexion->query("SELECT COUNT(*) as total FROM clientes");
    $row = $result->fetch_assoc();
    echo "<strong>Total de clientes:</strong> " . $row['total'] . "<br><br>";
    
    if ($row['total'] > 0) {
        $result = $conexion->query("SELECT * FROM clientes LIMIT 10");
        if ($result && $result->num_rows > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
            echo "<tr style='background: #f1f1f1;'>";
            echo "<th>ID Cliente</th><th>Nombre</th><th>Email</th><th>ID Usuario</th>";
            echo "</tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . ($row['id'] ?? $row['id_cliente'] ?? 'N/A') . "</td>";
                echo "<td>" . ($row['nombre'] ?? 'N/A') . "</td>";
                echo "<td>" . ($row['email'] ?? 'N/A') . "</td>";
                echo "<td>" . ($row['id_usuario'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    // Si el usuario está logueado, buscar su cliente_id
    if (isset($_SESSION['user_id'])) {
        echo "<h3>🔗 Relación Usuario → Cliente → Facturas:</h3>";
        
        // Buscar cliente por user_id
        $stmt = $conexion->prepare("SELECT * FROM clientes WHERE id_usuario = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $cliente = $result->fetch_assoc();
            $cliente_id = $cliente['id'] ?? $cliente['id_cliente'];
            
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
            echo "✅ <strong>Cliente encontrado:</strong><br>";
            echo "• Cliente ID: " . $cliente_id . "<br>";
            echo "• Nombre: " . ($cliente['nombre'] ?? 'N/A') . "<br>";
            echo "• Email: " . ($cliente['email'] ?? 'N/A');
            echo "</div>";
            
            // Buscar facturas de este cliente
            $stmt = $conexion->prepare("SELECT * FROM facturas WHERE id_cliente = ? ORDER BY fecha_factura DESC");
            $stmt->bind_param("i", $cliente_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            echo "<h4>🧾 Facturas de este cliente:</h4>";
            if ($result->num_rows > 0) {
                echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
                echo "<tr style='background: #f1f1f1;'>";
                echo "<th>ID Factura</th><th>Número</th><th>Fecha</th><th>Total</th>";
                echo "</tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id_factura'] . "</td>";
                    echo "<td>" . ($row['numero_factura'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row['fecha_factura'] ?? 'N/A') . "</td>";
                    echo "<td>$" . number_format($row['total'] ?? 0, 2) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
                echo "⚠️ Este cliente no tiene facturas registradas";
                echo "</div>";
            }
            
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
            echo "❌ <strong>Cliente no encontrado</strong><br>";
            echo "El usuario con ID " . $_SESSION['user_id'] . " no tiene registro en la tabla 'clientes'";
            echo "</div>";
        }
    }
    
    $conexion->close();
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
}

echo "<h3>🚀 Acciones:</h3>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='?controller=usuario&action=dashboardCliente' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>👤 Ir al Dashboard Cliente</a>";
echo "<a href='crear_cliente_bd.php?crear=1' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>➕ Crear Cliente en BD</a>";
echo "</div>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3, h4 { color: #333; }
table { font-size: 14px; }
th, td { padding: 8px; text-align: left; }
a { text-decoration: none; }
a:hover { opacity: 0.8; }
</style>