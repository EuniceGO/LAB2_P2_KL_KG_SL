<?php
/**
 * Debug del checkout - Verificar datos del cliente
 */

session_start();

echo "<h2>🔍 Debug del Checkout - Datos del Cliente</h2>";

echo "<h3>1. Estado de la Sesión:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>2. Verificaciones:</h3>";
echo "• user_id existe: " . (isset($_SESSION['user_id']) ? "✅ Sí (" . $_SESSION['user_id'] . ")" : "❌ No") . "<br>";
echo "• user_role_id: " . ($_SESSION['user_role_id'] ?? 'No definido') . "<br>";
echo "• Es cliente (role_id == 2): " . (isset($_SESSION['user_role_id']) && $_SESSION['user_role_id'] == 2 ? "✅ Sí" : "❌ No") . "<br>";

if (isset($_SESSION['user_id']) && $_SESSION['user_role_id'] == 2) {
    echo "<br><h3>3. Intentando obtener datos del ClienteModel:</h3>";
    
    try {
        require_once 'modelos/ClienteModel.php';
        $clienteModel = new ClienteModel();
        $cliente = $clienteModel->obtenerPorUsuario($_SESSION['user_id']);
        
        echo "• Resultado de obtenerPorUsuario(): ";
        if ($cliente) {
            echo "✅ Cliente encontrado<br>";
            echo "<pre>";
            print_r($cliente);
            echo "</pre>";
            
            $datosCliente = [
                'nombre' => $cliente['nombre'],
                'email' => $cliente['email'],
                'telefono' => $cliente['telefono'] ?? '',
                'direccion' => $cliente['direccion'] ?? ''
            ];
            
            echo "<h4>Datos que se enviarían al checkout:</h4>";
            echo "<pre>";
            print_r($datosCliente);
            echo "</pre>";
            
        } else {
            echo "❌ Cliente no encontrado en BD<br>";
            echo "Usando datos de sesión como fallback:<br>";
            
            $datosCliente = [
                'nombre' => $_SESSION['user_name'] ?? '',
                'email' => $_SESSION['user_email'] ?? '',
                'telefono' => '',
                'direccion' => ''
            ];
            
            echo "<pre>";
            print_r($datosCliente);
            echo "</pre>";
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
        echo "Usando datos de sesión como fallback:<br>";
        
        $datosCliente = [
            'nombre' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'telefono' => '',
            'direccion' => ''
        ];
        
        echo "<pre>";
        print_r($datosCliente);
        echo "</pre>";
    }
} else {
    echo "<br><div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb;'>";
    echo "❌ No es un cliente logueado válido";
    echo "</div>";
}

echo "<h3>4. Enlaces de prueba:</h3>";
echo "<p><a href='?c=carrito&a=checkout'>🛒 Ir al Checkout Real</a></p>";
echo "<p><a href='test_carrito_checkout.php'>🧪 Simulador de Carrito</a></p>";

// Verificar si hay productos en el carrito
echo "<h3>5. Estado del Carrito:</h3>";
try {
    require_once 'clases/Carrito.php';
    $resumen = Carrito::obtenerResumen();
    
    if ($resumen['esta_vacio']) {
        echo "<div style='background: #fff3cd; padding: 10px;'>⚠️ Carrito vacío - Necesitas agregar productos primero</div>";
        echo "<p><a href='test_carrito_checkout.php?agregar_productos=1'>➕ Agregar productos de prueba</a></p>";
    } else {
        echo "<div style='background: #d4edda; padding: 10px;'>✅ Carrito tiene productos</div>";
    }
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 10px;'>❌ Error al verificar carrito: " . $e->getMessage() . "</div>";
}

echo "<h3>6. Estructura de la tabla clientes:</h3>";
try {
    // Crear conexión directa
    $conexion = new mysqli("localhost", "root", "", "productos_iniciales");
    
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }
    
    $sql = "DESCRIBE clientes";
    $result = $conexion->query($sql);
    
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No se pudo obtener estructura de la tabla clientes";
    }
    
    $conexion->close();
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>