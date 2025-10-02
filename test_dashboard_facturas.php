<?php
/**
 * Test del Dashboard Cliente Actualizado
 */

session_start();

echo "<h2>🧪 Test Dashboard Cliente - Facturas</h2>";

if (!isset($_SESSION['user_id']) || $_SESSION['user_role_id'] != 2) {
    echo "<div style='background: #f8d7da; padding: 15px;'>❌ Debes estar logueado como cliente</div>";
    exit;
}

echo "<h3>👤 Usuario Actual:</h3>";
echo "<div style='background: #e9ecef; padding: 15px; border-radius: 5px;'>";
echo "• <strong>User ID:</strong> " . $_SESSION['user_id'] . "<br>";
echo "• <strong>Nombre:</strong> " . $_SESSION['user_name'] . "<br>";
echo "• <strong>Email:</strong> " . $_SESSION['user_email'] . "<br>";
echo "• <strong>Rol:</strong> " . $_SESSION['user_role'];
echo "</div>";

echo "<h3>🔧 Simulando lógica del Dashboard:</h3>";

// Simular exactamente lo que hace el dashboard
$misFacturas = [];
$totalGastado = 0;
$totalFacturas = 0;

try {
    require_once 'modelos/FacturaModel.php';
    require_once 'modelos/ClienteModel.php';
    
    $facturaModel = new FacturaModel();
    $clienteModel = new ClienteModel();
    
    echo "<div style='border: 2px dashed #007bff; padding: 15px; margin: 10px 0;'>";
    echo "<strong>Paso 1: Obtener cliente vinculado</strong><br>";
    
    $cliente = $clienteModel->obtenerPorUsuario($_SESSION['user_id']);
    
    if ($cliente) {
        echo "✅ Cliente encontrado en BD<br>";
        echo "<pre style='background: #f8f9fa; padding: 10px; margin: 5px 0;'>";
        print_r($cliente);
        echo "</pre>";
        
        $cliente_id = $cliente['id_cliente'] ?? $cliente['id'];
        echo "<strong>Paso 2: Obtener facturas del cliente ID: " . $cliente_id . "</strong><br>";
        
        $misFacturas = $facturaModel->obtenerPorCliente($cliente_id);
        
        if (!empty($misFacturas)) {
            echo "✅ Facturas encontradas: " . count($misFacturas) . "<br>";
            
            $totalFacturas = count($misFacturas);
            foreach ($misFacturas as $factura) {
                $totalGastado += $factura['total'];
            }
            
            echo "✅ Total gastado calculado: $" . number_format($totalGastado, 2) . "<br>";
        } else {
            echo "⚠️ No se encontraron facturas para este cliente<br>";
        }
        
    } else {
        echo "⚠️ Cliente NO encontrado - Usando fallback<br>";
        
        echo "<strong>Paso 2 (Fallback): Buscar facturas por email/nombre</strong><br>";
        
        $conexion = new mysqli("localhost", "root", "", "productos_iniciales");
        if (!$conexion->connect_error) {
            $stmt = $conexion->prepare("
                SELECT f.* FROM facturas f 
                WHERE f.cliente_email = ? OR f.cliente_nombre = ?
                ORDER BY f.fecha_factura DESC
            ");
            $stmt->bind_param("ss", $_SESSION['user_email'], $_SESSION['user_name']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $misFacturas[] = $row;
                $totalGastado += $row['total'];
            }
            $totalFacturas = count($misFacturas);
            $conexion->close();
            
            echo "✅ Facturas encontradas por fallback: " . $totalFacturas . "<br>";
            echo "✅ Total gastado: $" . number_format($totalGastado, 2) . "<br>";
        }
    }
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
}

echo "<h3>📊 Resultados Finales:</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "<strong>Variables que van a la vista:</strong><br>";
echo "• <strong>totalFacturas:</strong> " . $totalFacturas . "<br>";
echo "• <strong>totalGastado:</strong> $" . number_format($totalGastado, 2) . "<br>";
echo "• <strong>misFacturas count:</strong> " . count($misFacturas) . "<br>";
echo "</div>";

if (!empty($misFacturas)) {
    echo "<h3>🧾 Facturas Encontradas:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background: #f1f1f1;'>";
    echo "<th>ID</th><th>Número</th><th>Fecha</th><th>Total</th><th>Cliente</th>";
    echo "</tr>";
    
    foreach ($misFacturas as $factura) {
        echo "<tr>";
        echo "<td>" . $factura['id_factura'] . "</td>";
        echo "<td>" . ($factura['numero_factura'] ?? 'N/A') . "</td>";
        echo "<td>" . date('d/m/Y H:i', strtotime($factura['fecha_factura'] ?? $factura['fecha'])) . "</td>";
        echo "<td><strong>$" . number_format($factura['total'], 2) . "</strong></td>";
        echo "<td>" . ($factura['cliente_nombre'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
    echo "🎉 <strong>¡ÉXITO!</strong> El dashboard debería mostrar todas estas facturas correctamente";
    echo "</div>";
} else {
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
    echo "⚠️ <strong>No se encontraron facturas</strong> - Verifica la vinculación usuario-cliente";
    echo "</div>";
}

echo "<h3>🚀 Probar Dashboard Real:</h3>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='?controller=usuario&action=dashboardCliente' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-size: 16px;'>";
echo "🎯 IR AL DASHBOARD REAL";
echo "</a>";
echo "</div>";

echo "<h3>🔗 Enlaces Útiles:</h3>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='debug_facturas_usuario.php' style='background: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔍 Debug Original</a>";
echo "<a href='solucion_facturas_usuario.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔧 Solución Vincular</a>";
echo "</div>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3 { color: #333; }
pre { font-size: 12px; }
table { font-size: 14px; }
th, td { padding: 8px; text-align: left; }
a { text-decoration: none; }
a:hover { opacity: 0.8; }
</style>