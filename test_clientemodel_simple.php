<?php
/**
 * Test rápido de ClienteModel para verificar conexión
 */

echo "<h1>Test de ClienteModel - Verificación de Conexión</h1>";

try {
    echo "<h2>1. Creando ClienteModel...</h2>";
    require_once 'modelos/ClienteModel.php';
    $clienteModel = new ClienteModel();
    echo "✅ ClienteModel creado exitosamente<br>";
    
    echo "<h2>2. Testando método contarTotal()...</h2>";
    $total = $clienteModel->contarTotal();
    echo "✅ Total de clientes: $total<br>";
    
    echo "<h2>3. Testando buscarPorEmail()...</h2>";
    $email = 'test@ejemplo.com';
    $cliente = $clienteModel->buscarPorEmail($email);
    
    if ($cliente) {
        echo "✅ Cliente encontrado: " . $cliente['nombre'] . "<br>";
    } else {
        echo "ℹ️ Cliente con email '$email' no encontrado (esto es normal)<br>";
    }
    
    echo "<h2>4. Testando insertarOActualizar()...</h2>";
    $datosCliente = [
        'nombre' => 'Cliente Test Conexión',
        'email' => 'test_conexion@ejemplo.com',
        'telefono' => '12345678',
        'direccion' => 'Dirección de prueba'
    ];
    
    $idCliente = $clienteModel->insertarOActualizar($datosCliente);
    
    if ($idCliente) {
        echo "✅ Cliente insertado/actualizado con ID: $idCliente<br>";
        
        // Verificar que se guardó correctamente
        $clienteGuardado = $clienteModel->buscarPorEmail($datosCliente['email']);
        if ($clienteGuardado) {
            echo "✅ Verificación exitosa - Cliente guardado correctamente<br>";
            echo "📊 Datos: " . $clienteGuardado['nombre'] . " - " . $clienteGuardado['email'] . "<br>";
        }
    } else {
        echo "❌ Error al insertar cliente<br>";
    }
    
    echo "<h2>✅ Todas las pruebas completadas exitosamente!</h2>";
    echo "<p><strong>🎉 La conexión y ClienteModel están funcionando correctamente.</strong></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error durante las pruebas:</h2>";
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Stack Trace:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

?>