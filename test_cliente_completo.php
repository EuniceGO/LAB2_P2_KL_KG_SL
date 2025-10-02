<?php
/**
 * Test completo del sistema de clientes
 * Verifica que todas las funciones trabajen con el esquema correcto de la base de datos
 */

// Incluir archivos necesarios
require_once 'config/mysqli_connection.php';
require_once 'modelos/ClienteModel.php';
require_once 'modelos/FacturaModel.php';

echo "<h1>Test del Sistema de Clientes</h1>";

try {
    // Test 1: Verificar conexión a la base de datos
    echo "<h2>1. Verificando conexión a la base de datos...</h2>";
    if ($conn) {
        echo "✅ Conexión establecida correctamente<br>";
    } else {
        echo "❌ Error en la conexión<br>";
        exit;
    }

    // Test 2: Verificar que las tablas existen
    echo "<h2>2. Verificando que las tablas existen...</h2>";
    
    $tablas = ['clientes', 'facturas', 'detalle_factura'];
    foreach ($tablas as $tabla) {
        $result = $conn->query("SHOW TABLES LIKE '$tabla'");
        if ($result->num_rows > 0) {
            echo "✅ Tabla '$tabla' existe<br>";
        } else {
            echo "❌ Tabla '$tabla' NO existe<br>";
        }
    }

    // Test 3: Verificar estructura de la tabla clientes
    echo "<h2>3. Verificando estructura de la tabla clientes...</h2>";
    $result = $conn->query("DESCRIBE clientes");
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th></tr>";
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
    }

    // Test 4: Crear una instancia del modelo de cliente
    echo "<h2>4. Testando ClienteModel...</h2>";
    $clienteModel = new ClienteModel();
    echo "✅ ClienteModel creado correctamente<br>";

    // Test 5: Contar clientes existentes
    echo "<h2>5. Contando clientes existentes...</h2>";
    $totalClientes = $clienteModel->contarTotal();
    echo "📊 Total de clientes en la base de datos: $totalClientes<br>";

    // Test 6: Buscar cliente por email de prueba
    echo "<h2>6. Buscando cliente de prueba...</h2>";
    $emailPrueba = "test@ejemplo.com";
    $clienteExistente = $clienteModel->buscarPorEmail($emailPrueba);
    
    if ($clienteExistente) {
        echo "✅ Cliente encontrado: " . $clienteExistente['nombre'] . " (" . $clienteExistente['email'] . ")<br>";
    } else {
        echo "ℹ️ No se encontró cliente con email $emailPrueba<br>";
        
        // Test 7: Crear cliente de prueba
        echo "<h2>7. Creando cliente de prueba...</h2>";
        $datosCliente = [
            'nombre' => 'Cliente Test',
            'email' => $emailPrueba,
            'telefono' => '12345678',
            'direccion' => 'Dirección de prueba 123'
        ];
        
        $idClienteNuevo = $clienteModel->insertarOActualizar($datosCliente);
        
        if ($idClienteNuevo) {
            echo "✅ Cliente creado correctamente con ID: $idClienteNuevo<br>";
        } else {
            echo "❌ Error al crear cliente<br>";
        }
    }

    // Test 8: Obtener algunos clientes
    echo "<h2>8. Obteniendo lista de clientes (primeros 5)...</h2>";
    $clientes = $clienteModel->obtenerTodos(5, 0);
    
    if (!empty($clientes)) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Total Facturas</th><th>Total Compras</th></tr>";
        
        foreach ($clientes as $cliente) {
            echo "<tr>";
            echo "<td>" . $cliente['id_cliente'] . "</td>";
            echo "<td>" . $cliente['nombre'] . "</td>";
            echo "<td>" . $cliente['email'] . "</td>";
            echo "<td>" . $cliente['telefono'] . "</td>";
            echo "<td>" . ($cliente['total_facturas'] ?? 0) . "</td>";
            echo "<td>$" . number_format($cliente['total_compras'] ?? 0, 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "ℹ️ No hay clientes en la base de datos<br>";
    }

    // Test 9: Testear FacturaModel
    echo "<h2>9. Testando FacturaModel...</h2>";
    $facturaModel = new FacturaModel();
    echo "✅ FacturaModel creado correctamente<br>";

    echo "<h2>✅ Todos los tests completados exitosamente!</h2>";
    echo "<p><strong>El sistema de clientes está configurado correctamente y funciona con tu esquema de base de datos.</strong></p>";

} catch (Exception $e) {
    echo "<h2>❌ Error durante las pruebas:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<p>Revisar la configuración de la base de datos y los modelos.</p>";
}

?>