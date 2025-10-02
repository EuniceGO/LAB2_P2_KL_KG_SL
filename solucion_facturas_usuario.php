<?php
/**
 * Solución completa: Vincular usuario con cliente existente y mostrar facturas
 */

session_start();

echo "<h2>🔧 Solución: Vincular Usuario con Cliente</h2>";

if (!isset($_SESSION['user_id']) || $_SESSION['user_role_id'] != 2) {
    echo "<div style='background: #f8d7da; padding: 15px;'>❌ Debes estar logueado como cliente</div>";
    exit;
}

try {
    $conexion = new mysqli("localhost", "root", "", "productos_iniciales");
    
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }
    
    echo "<h3>🔍 Análisis del Problema:</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "• <strong>Usuario:</strong> " . $_SESSION['user_name'] . " (ID: " . $_SESSION['user_id'] . ")<br>";
    echo "• <strong>Email:</strong> " . $_SESSION['user_email'] . "<br>";
    echo "• <strong>Problema:</strong> Usuario no vinculado con cliente en BD<br>";
    echo "</div>";
    
    // Buscar clientes con el mismo nombre o email
    echo "<h3>🔎 Buscando clientes compatibles:</h3>";
    $stmt = $conexion->prepare("
        SELECT id_cliente, nombre, email, id_usuario 
        FROM clientes 
        WHERE nombre LIKE ? OR email = ? OR nombre = ?
        ORDER BY 
            CASE 
                WHEN email = ? THEN 1
                WHEN nombre = ? THEN 2  
                WHEN nombre LIKE ? THEN 3
                ELSE 4
            END
    ");
    
    $nombreBusqueda = "%" . $_SESSION['user_name'] . "%";
    $nombre = $_SESSION['user_name'];
    $email = $_SESSION['user_email'];
    
    $stmt->bind_param("ssssss", $nombreBusqueda, $email, $nombre, $email, $nombre, $nombreBusqueda);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $clientesCompatibles = [];
    while ($row = $result->fetch_assoc()) {
        $clientesCompatibles[] = $row;
    }
    
    if (empty($clientesCompatibles)) {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
        echo "❌ No se encontraron clientes compatibles<br>";
        echo "Se creará un nuevo cliente";
        echo "</div>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background: #f1f1f1;'><th>ID Cliente</th><th>Nombre</th><th>Email</th><th>ID Usuario</th><th>Compatibilidad</th></tr>";
        
        foreach ($clientesCompatibles as $cliente) {
            echo "<tr>";
            echo "<td><strong>" . $cliente['id_cliente'] . "</strong></td>";
            echo "<td>" . htmlspecialchars($cliente['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($cliente['email'] ?? 'N/A') . "</td>";
            echo "<td>" . ($cliente['id_usuario'] ?? 'Sin vincular') . "</td>";
            
            // Determinar compatibilidad
            $compatibilidad = '';
            if ($cliente['email'] == $_SESSION['user_email']) {
                $compatibilidad = '🎯 Email exacto';
            } elseif ($cliente['nombre'] == $_SESSION['user_name']) {
                $compatibilidad = '✅ Nombre exacto';
            } else {
                $compatibilidad = '⚠️ Nombre similar';
            }
            
            echo "<td>" . $compatibilidad . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Acción para vincular
    if (isset($_GET['vincular']) && !empty($clientesCompatibles)) {
        $cliente_id = (int)$_GET['vincular'];
        
        // Verificar que el cliente existe y no está vinculado
        $stmt = $conexion->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $cliente = $result->fetch_assoc();
            
            if ($cliente['id_usuario'] && $cliente['id_usuario'] != $_SESSION['user_id']) {
                echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
                echo "❌ Este cliente ya está vinculado a otro usuario (ID: " . $cliente['id_usuario'] . ")";
                echo "</div>";
            } else {
                // Vincular usuario con cliente
                $stmt = $conexion->prepare("UPDATE clientes SET id_usuario = ? WHERE id_cliente = ?");
                $stmt->bind_param("ii", $_SESSION['user_id'], $cliente_id);
                
                if ($stmt->execute()) {
                    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
                    echo "🎉 <strong>¡VINCULACIÓN EXITOSA!</strong><br>";
                    echo "• Usuario: " . $_SESSION['user_name'] . " (ID: " . $_SESSION['user_id'] . ")<br>";
                    echo "• Cliente: " . htmlspecialchars($cliente['nombre']) . " (ID: " . $cliente_id . ")<br>";
                    echo "• Ahora podrás ver todas tus facturas en el dashboard";
                    echo "</div>";
                    
                    // Mostrar facturas del cliente vinculado
                    echo "<h3>🧾 Facturas encontradas:</h3>";
                    $stmt = $conexion->prepare("
                        SELECT id_factura, numero_factura, fecha_factura, total, estado 
                        FROM facturas 
                        WHERE id_cliente = ? 
                        ORDER BY fecha_factura DESC
                    ");
                    $stmt->bind_param("i", $cliente_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $totalGastado = 0;
                        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
                        echo "<tr style='background: #f1f1f1;'><th>ID</th><th>Número</th><th>Fecha</th><th>Total</th><th>Estado</th></tr>";
                        
                        while ($row = $result->fetch_assoc()) {
                            $totalGastado += $row['total'];
                            echo "<tr>";
                            echo "<td>" . $row['id_factura'] . "</td>";
                            echo "<td>" . ($row['numero_factura'] ?? 'N/A') . "</td>";
                            echo "<td>" . date('d/m/Y H:i', strtotime($row['fecha_factura'])) . "</td>";
                            echo "<td><strong>$" . number_format($row['total'], 2) . "</strong></td>";
                            echo "<td>" . ($row['estado'] ?? 'Pagada') . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        
                        echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px;'>";
                        echo "📊 <strong>Resumen:</strong><br>";
                        echo "• Total de facturas: " . $result->num_rows . "<br>";
                        echo "• Total gastado: $" . number_format($totalGastado, 2);
                        echo "</div>";
                    } else {
                        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
                        echo "⚠️ Este cliente aún no tiene facturas registradas";
                        echo "</div>";
                    }
                    
                    echo "<div style='margin: 20px 0;'>";
                    echo "<a href='?controller=usuario&action=dashboardCliente' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-size: 16px;'>";
                    echo "🎉 IR AL DASHBOARD ACTUALIZADO";
                    echo "</a>";
                    echo "</div>";
                    
                } else {
                    throw new Exception("Error al vincular: " . $conexion->error);
                }
            }
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
            echo "❌ Cliente no encontrado";
            echo "</div>";
        }
    }
    
    // Acción para crear nuevo cliente
    if (isset($_GET['crear_nuevo'])) {
        $stmt = $conexion->prepare("
            INSERT INTO clientes (nombre, email, id_usuario, telefono, direccion) 
            VALUES (?, ?, ?, '', '')
        ");
        $stmt->bind_param("ssi", $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $nuevo_cliente_id = $conexion->insert_id;
            
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
            echo "🎉 <strong>¡CLIENTE CREADO EXITOSAMENTE!</strong><br>";
            echo "• Nuevo Cliente ID: " . $nuevo_cliente_id . "<br>";
            echo "• Nombre: " . $_SESSION['user_name'] . "<br>";
            echo "• Email: " . $_SESSION['user_email'] . "<br>";
            echo "• Vinculado al Usuario ID: " . $_SESSION['user_id'];
            echo "</div>";
            
            echo "<div style='margin: 20px 0;'>";
            echo "<a href='?controller=usuario&action=dashboardCliente' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-size: 16px;'>";
            echo "🎉 IR AL DASHBOARD";
            echo "</a>";
            echo "</div>";
            
        } else {
            throw new Exception("Error al crear cliente: " . $conexion->error);
        }
    }
    
    // Mostrar opciones de acción
    if (!isset($_GET['vincular']) && !isset($_GET['crear_nuevo'])) {
        echo "<h3>🚀 Opciones de Solución:</h3>";
        
        if (!empty($clientesCompatibles)) {
            echo "<h4>Opción 1: Vincular con Cliente Existente</h4>";
            foreach ($clientesCompatibles as $cliente) {
                $disabled = ($cliente['id_usuario'] && $cliente['id_usuario'] != $_SESSION['user_id']) ? 'opacity: 0.5; pointer-events: none;' : '';
                $texto = ($cliente['id_usuario'] && $cliente['id_usuario'] != $_SESSION['user_id']) ? ' (Ya vinculado)' : '';
                
                echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px; {$disabled}'>";
                echo "<strong>Cliente ID " . $cliente['id_cliente'] . ":</strong> " . htmlspecialchars($cliente['nombre']) . "<br>";
                echo "<small>Email: " . htmlspecialchars($cliente['email'] ?? 'N/A') . "</small><br>";
                
                if (!$disabled) {
                    echo "<a href='?vincular=" . $cliente['id_cliente'] . "' style='background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-top: 5px; display: inline-block;'>";
                    echo "🔗 Vincular con este Cliente";
                    echo "</a>";
                } else {
                    echo "<span style='color: #666;'>Ya vinculado a otro usuario</span>";
                }
                echo "</div>";
            }
            
            echo "<h4>Opción 2: Crear Nuevo Cliente</h4>";
        } else {
            echo "<h4>Crear Nuevo Cliente:</h4>";
        }
        
        echo "<div style='margin: 15px 0;'>";
        echo "<a href='?crear_nuevo=1' style='background: #28a745; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px;'>";
        echo "➕ Crear Nuevo Cliente";
        echo "</a>";
        echo "</div>";
        
        echo "<p><small><strong>Recomendación:</strong> Si ves un cliente con tu nombre/email exacto, vincúlalo. Si no, crea uno nuevo.</small></p>";
    }
    
    $conexion->close();
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
}

echo "<h3>🔗 Enlaces:</h3>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='debug_facturas_usuario.php' style='background: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔍 Volver al Debug</a>";
echo "<a href='?controller=usuario&action=dashboardCliente' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>👤 Dashboard Cliente</a>";
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