<?php
/**
 * Test rápido del checkout - Verificar variables
 */

// Simular que estamos logueados como cliente
session_start();
$_SESSION['user_id'] = 2;
$_SESSION['user_name'] = 'Cliente de Prueba';
$_SESSION['user_email'] = 'cliente@test.com';
$_SESSION['user_role_id'] = 2;
$_SESSION['user_role'] = 'Cliente';

echo "<h2>🧪 Test Rápido del Checkout</h2>";

// Simular el mismo código del controlador
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$datosCliente = null;
$esClienteLogueado = false;

echo "<h3>1. Verificaciones iniciales:</h3>";
echo "• Session user_id: " . ($_SESSION['user_id'] ?? 'NO DEFINIDO') . "<br>";
echo "• Session user_role_id: " . ($_SESSION['user_role_id'] ?? 'NO DEFINIDO') . "<br>";
echo "• Condición (isset user_id): " . (isset($_SESSION['user_id']) ? 'TRUE' : 'FALSE') . "<br>";
echo "• Condición (role_id == 2): " . (isset($_SESSION['user_role_id']) && $_SESSION['user_role_id'] == 2 ? 'TRUE' : 'FALSE') . "<br>";

if (isset($_SESSION['user_id']) && $_SESSION['user_role_id'] == 2) {
    echo "<br><div style='background: #d4edda; padding: 10px;'>✅ Condiciones cumplidas - Debería cargar datos</div>";
    
    $esClienteLogueado = true;
    
    try {
        require_once 'modelos/ClienteModel.php';
        $clienteModel = new ClienteModel();
        
        echo "<h3>2. Intentando obtener cliente de BD:</h3>";
        echo "• Buscando cliente con ID: " . $_SESSION['user_id'] . "<br>";
        
        $cliente = $clienteModel->obtenerPorUsuario($_SESSION['user_id']);
        
        if ($cliente) {
            echo "• ✅ Cliente encontrado en BD<br>";
            echo "<pre>";
            print_r($cliente);
            echo "</pre>";
            
            $datosCliente = [
                'nombre' => $cliente['nombre'],
                'email' => $cliente['email'],
                'telefono' => $cliente['telefono'] ?? '',
                'direccion' => $cliente['direccion'] ?? ''
            ];
        } else {
            echo "• ❌ Cliente NO encontrado en BD - Usando fallback<br>";
            
            $datosCliente = [
                'nombre' => $_SESSION['user_name'] ?? '',
                'email' => $_SESSION['user_email'] ?? '',
                'telefono' => '',
                'direccion' => ''
            ];
        }
        
    } catch (Exception $e) {
        echo "• ❌ Error al consultar BD: " . $e->getMessage() . "<br>";
        echo "• Usando datos de sesión como fallback<br>";
        
        $datosCliente = [
            'nombre' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'telefono' => '',
            'direccion' => ''
        ];
    }
    
} else {
    echo "<br><div style='background: #f8d7da; padding: 10px;'>❌ Condiciones NO cumplidas</div>";
}

echo "<h3>3. Variables finales que van a la vista:</h3>";
echo "<strong>esClienteLogueado:</strong> " . ($esClienteLogueado ? 'TRUE' : 'FALSE') . "<br>";
echo "<strong>datosCliente:</strong><br>";
echo "<pre>";
print_r($datosCliente);
echo "</pre>";

echo "<h3>4. Simulación de la vista:</h3>";
echo "<div style='border: 2px solid #007bff; padding: 15px; margin: 10px 0; background: #f8f9fa;'>";

if ($esClienteLogueado): ?>
    <div style='background: #d4edda; padding: 10px; margin: 10px 0;'>
        ✅ Cliente autenticado
    </div>
<?php endif; ?>

<?php if ($esClienteLogueado && $datosCliente): ?>
    <div style='background: #d1ecf1; padding: 10px; margin: 10px 0;'>
        ℹ️ Sus datos han sido cargados automáticamente. Puede modificarlos si es necesario.
    </div>
<?php endif; ?>

<div style='margin: 10px 0;'>
    <label><strong>Nombre Completo:</strong></label><br>
    <input type="text" style='width: 300px; padding: 5px;' 
           value="<?php echo isset($datosCliente) ? htmlspecialchars($datosCliente['nombre']) : ''; ?>"
           placeholder="Ingrese su nombre completo">
</div>

<div style='margin: 10px 0;'>
    <label><strong>Email:</strong></label><br>
    <input type="email" style='width: 300px; padding: 5px;' 
           value="<?php echo isset($datosCliente) ? htmlspecialchars($datosCliente['email']) : ''; ?>"
           placeholder="ejemplo@correo.com">
</div>

<div style='margin: 10px 0;'>
    <label><strong>Teléfono:</strong></label><br>
    <input type="tel" style='width: 300px; padding: 5px;' 
           value="<?php echo isset($datosCliente) ? htmlspecialchars($datosCliente['telefono']) : ''; ?>"
           placeholder="Teléfono">
</div>

<div style='margin: 10px 0;'>
    <label><strong>Dirección:</strong></label><br>
    <textarea style='width: 300px; height: 60px; padding: 5px;'
              placeholder="Dirección completa"><?php echo isset($datosCliente) ? htmlspecialchars($datosCliente['direccion']) : ''; ?></textarea>
</div>

<?php
echo "</div>";

echo "<h3>5. Enlaces para probar:</h3>";
echo "<p><a href='cambiar_tipo_usuario.php?tipo=cliente'>👤 Asegurar que soy cliente</a></p>";
echo "<p><a href='test_carrito_checkout.php?agregar_productos=1'>🛒 Agregar productos y probar checkout real</a></p>";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
pre { background: #f1f1f1; padding: 10px; border-radius: 5px; }
</style>