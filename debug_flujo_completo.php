<?php
/**
 * Debug completo del flujo QR → Login → Checkout
 */
session_start();

echo "<h2>🔍 Debug Completo del Flujo QR → Login → Checkout</h2>";

echo "<h3>📱 Estado Actual de la Sesión:</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
if (empty($_SESSION)) {
    echo "❌ <strong>No hay sesión activa</strong>";
} else {
    echo "<strong>Datos de sesión encontrados:</strong><br>";
    foreach ($_SESSION as $key => $value) {
        echo "• <strong>{$key}:</strong> " . (is_array($value) ? json_encode($value) : $value) . "<br>";
    }
    
    // Verificaciones específicas
    echo "<br><strong>Verificaciones:</strong><br>";
    echo "• Usuario logueado: " . (isset($_SESSION['user_id']) ? "✅ Sí (ID: {$_SESSION['user_id']})" : "❌ No") . "<br>";
    echo "• Es cliente: " . (isset($_SESSION['user_role_id']) && $_SESSION['user_role_id'] == 2 ? "✅ Sí" : "❌ No") . "<br>";
    echo "• Rol correcto: " . (isset($_SESSION['user_role']) ? "✅ {$_SESSION['user_role']}" : "❌ No definido") . "<br>";
}
echo "</div>";

echo "<h3>🛒 Estado del Carrito:</h3>";
try {
    require_once 'clases/Carrito.php';
    $resumen = Carrito::obtenerResumen();
    
    if ($resumen['esta_vacio']) {
        echo "<div style='background: #fff3cd; padding: 10px; margin: 10px 0;'>";
        echo "⚠️ <strong>Carrito vacío</strong> - No se puede acceder al checkout";
        echo "</div>";
    } else {
        echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0;'>";
        echo "✅ <strong>Carrito tiene productos</strong><br>";
        echo "• Productos: " . count($resumen['productos']) . "<br>";
        echo "• Total: $" . number_format($resumen['total'], 2);
        echo "</div>";
        
        echo "<strong>Productos en carrito:</strong><br>";
        foreach ($resumen['productos'] as $producto) {
            echo "- " . htmlspecialchars($producto['nombre']) . " (Cantidad: {$producto['cantidad']})<br>";
        }
    }
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0;'>";
    echo "❌ Error al verificar carrito: " . $e->getMessage();
    echo "</div>";
}

echo "<h3>🔧 Simulación del Checkout:</h3>";

// Simular exactamente lo que hace el checkout
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$datosCliente = null;
$esClienteLogueado = false;

echo "<div style='border: 2px dashed #007bff; padding: 15px; margin: 10px 0;'>";
echo "<strong>Ejecutando lógica del checkout...</strong><br><br>";

if (isset($_SESSION['user_id']) && $_SESSION['user_role_id'] == 2) {
    echo "✅ Paso 1: Usuario es cliente válido<br>";
    $esClienteLogueado = true;
    
    try {
        require_once 'modelos/ClienteModel.php';
        $clienteModel = new ClienteModel();
        $cliente = $clienteModel->obtenerPorUsuario($_SESSION['user_id']);
        
        echo "✅ Paso 2: Consulta a ClienteModel ejecutada<br>";
        
        if ($cliente) {
            echo "✅ Paso 3: Cliente encontrado en BD<br>";
            echo "<pre style='background: #e9ecef; padding: 10px;'>";
            print_r($cliente);
            echo "</pre>";
            
            $datosCliente = [
                'nombre' => $cliente['nombre'],
                'email' => $cliente['email'],
                'telefono' => $cliente['telefono'] ?? '',
                'direccion' => $cliente['direccion'] ?? ''
            ];
            
            echo "✅ Paso 4: Datos del cliente preparados<br>";
        } else {
            echo "⚠️ Paso 3: Cliente NO encontrado en BD - Usando fallback<br>";
            
            $datosCliente = [
                'nombre' => $_SESSION['user_name'] ?? '',
                'email' => $_SESSION['user_email'] ?? '',
                'telefono' => '',
                'direccion' => ''
            ];
        }
        
    } catch (Exception $e) {
        echo "❌ Paso 2/3: Error en ClienteModel: " . $e->getMessage() . "<br>";
        echo "⚠️ Usando datos de sesión como fallback<br>";
        
        $datosCliente = [
            'nombre' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'telefono' => '',
            'direccion' => ''
        ];
    }
} else {
    echo "❌ Paso 1: Usuario NO es cliente válido<br>";
    if (!isset($_SESSION['user_id'])) {
        echo "  - Razón: No hay user_id en sesión<br>";
    }
    if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 2) {
        echo "  - Razón: Role ID no es 2 (es: " . ($_SESSION['user_role_id'] ?? 'undefined') . ")<br>";
    }
}

echo "</div>";

echo "<h3>📊 Resultado Final:</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "<strong>esClienteLogueado:</strong> " . ($esClienteLogueado ? 'TRUE' : 'FALSE') . "<br>";
echo "<strong>datosCliente:</strong><br>";
if ($datosCliente) {
    foreach ($datosCliente as $key => $value) {
        echo "• <strong>{$key}:</strong> '" . htmlspecialchars($value) . "'<br>";
    }
} else {
    echo "❌ NULL - No hay datos";
}
echo "</div>";

echo "<h3>🎯 Simulación Visual del Formulario:</h3>";
echo "<div style='border: 3px solid #28a745; padding: 20px; background: white; margin: 15px 0;'>";

if ($esClienteLogueado): ?>
    <div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 5px;'>
        ✅ <strong>Cliente autenticado</strong>
    </div>
<?php endif; ?>

<?php if ($esClienteLogueado && $datosCliente): ?>
    <div style='background: #d1ecf1; padding: 10px; margin: 10px 0; border-radius: 5px;'>
        ℹ️ Sus datos han sido cargados automáticamente. Puede modificarlos si es necesario.
    </div>
<?php endif; ?>

<div style='margin: 10px 0;'>
    <label><strong>Nombre Completo:</strong></label><br>
    <input type="text" style='width: 100%; max-width: 400px; padding: 8px; border: 2px solid #007bff;' 
           value="<?php echo isset($datosCliente) ? htmlspecialchars($datosCliente['nombre']) : ''; ?>"
           placeholder="Ingrese su nombre completo">
    <small style='color: #666;'>Valor actual: "<?php echo isset($datosCliente) ? htmlspecialchars($datosCliente['nombre']) : 'VACÍO'; ?>"</small>
</div>

<div style='margin: 10px 0;'>
    <label><strong>Email:</strong></label><br>
    <input type="email" style='width: 100%; max-width: 400px; padding: 8px; border: 2px solid #007bff;' 
           value="<?php echo isset($datosCliente) ? htmlspecialchars($datosCliente['email']) : ''; ?>"
           placeholder="ejemplo@correo.com">
    <small style='color: #666;'>Valor actual: "<?php echo isset($datosCliente) ? htmlspecialchars($datosCliente['email']) : 'VACÍO'; ?>"</small>
</div>

<?php
echo "</div>";

echo "<h3>🚀 Acciones de Prueba:</h3>";
echo "<div style='margin: 20px 0;'>";

// Enlaces para diferentes escenarios
echo "<p><strong>Preparar escenario completo:</strong></p>";
echo "<a href='preparar_checkout.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🛒 Preparar Carrito + Cliente</a><br><br>";

echo "<p><strong>Ir directamente al checkout:</strong></p>";
echo "<a href='?c=carrito&a=checkout&debug=1' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🧪 Checkout con Debug</a><br>";
echo "<a href='?c=carrito&a=checkout&force_data=1' style='background: #fd7e14; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>⚡ Checkout con Datos Forzados</a><br><br>";

echo "<p><strong>Gestión de sesión:</strong></p>";
echo "<a href='cambiar_tipo_usuario.php?tipo=cliente' style='background: #6f42c1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>👤 Forzar Cliente</a><br>";

echo "</div>";

echo "<h3>📋 Diagnóstico:</h3>";
$problemas = [];

if (empty($_SESSION)) {
    $problemas[] = "No hay sesión activa";
}
if (!isset($_SESSION['user_id'])) {
    $problemas[] = "No hay user_id en la sesión";
}
if (!isset($_SESSION['user_role_id']) || $_SESSION['user_role_id'] != 2) {
    $problemas[] = "El usuario no es un cliente (role_id != 2)";
}
if (!$esClienteLogueado) {
    $problemas[] = "La variable esClienteLogueado es FALSE";
}
if (!$datosCliente || empty($datosCliente['nombre'])) {
    $problemas[] = "No hay datos del cliente o están vacíos";
}

if (empty($problemas)) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
    echo "🎉 <strong>TODO ESTÁ CORRECTO</strong> - El checkout debería mostrar los datos pre-llenados";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "⚠️ <strong>PROBLEMAS DETECTADOS:</strong><br>";
    foreach ($problemas as $problema) {
        echo "• " . $problema . "<br>";
    }
    echo "</div>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3 { color: #333; }
pre { background: #f1f1f1; padding: 10px; border-radius: 5px; overflow-x: auto; }
a { text-decoration: none; }
a:hover { opacity: 0.8; }
input, textarea { font-family: Arial, sans-serif; }
</style>