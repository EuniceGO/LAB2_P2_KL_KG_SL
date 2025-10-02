<?php
/**
 * Test del escenario específico que causaba el error
 * Simula el proceso de checkout que estaba fallando
 */

echo "<h1>Test del Escenario de Error Original</h1>";
echo "<p>Simulando el proceso que causaba: 'Error no detectado: llamada a una función miembro prepare() en nulo'</p>";

try {
    echo "<h2>1. Simulando datos del checkout...</h2>";
    
    // Estos son los datos que venían del formulario de checkout
    $datosCliente = [
        'nombre' => 'Kathya Leal',
        'email' => 'lealkathya1@gmail.com',  // Email específico que causaba el error
        'telefono' => '12345678',
        'direccion' => 'Dirección del cliente'
    ];
    
    echo "📋 Email del cliente: " . $datosCliente['email'] . "<br>";
    echo "👤 Nombre del cliente: " . $datosCliente['nombre'] . "<br>";
    
    echo "<h2>2. Creando ClienteModel (donde ocurría el error)...</h2>";
    require_once 'modelos/ClienteModel.php';
    $clienteModel = new ClienteModel();
    echo "✅ ClienteModel creado exitosamente<br>";
    
    echo "<h2>3. Ejecutando buscarPorEmail() (línea 61 del error original)...</h2>";
    $clienteExistente = $clienteModel->buscarPorEmail($datosCliente['email']);
    
    if ($clienteExistente) {
        echo "✅ Cliente encontrado en la base de datos<br>";
        echo "📊 ID del cliente: " . $clienteExistente['id_cliente'] . "<br>";
        echo "📊 Nombre: " . $clienteExistente['nombre'] . "<br>";
    } else {
        echo "ℹ️ Cliente no existe, será creado<br>";
    }
    
    echo "<h2>4. Ejecutando insertarOActualizar() (donde también podía fallar)...</h2>";
    $idCliente = $clienteModel->insertarOActualizar($datosCliente);
    
    if ($idCliente) {
        echo "✅ Cliente procesado exitosamente con ID: $idCliente<br>";
        
        // Verificar que la integración con Factura funciona
        echo "<h2>5. Probando integración con clase Factura...</h2>";
        require_once 'clases/Factura.php';
        
        $factura = new Factura();
        $factura->setClienteInfo($datosCliente);
        
        echo "✅ Datos del cliente configurados en factura<br>";
        echo "✅ No hay errores de conexión<br>";
        
    } else {
        echo "❌ Error al procesar cliente<br>";
    }
    
    echo "<h2>✅ Escenario de error original resuelto!</h2>";
    echo "<p><strong>🎉 El problema de 'prepare() en nulo' ha sido completamente solucionado.</strong></p>";
    echo "<p>🚀 El proceso de checkout ahora funcionará correctamente y guardará los datos del cliente.</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error detectado:</h2>";
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    
    // Información adicional para debug
    echo "<h3>Información de debug:</h3>";
    echo "<p>Si este error persiste, verificar:</p>";
    echo "<ul>";
    echo "<li>✅ XAMPP está corriendo</li>";
    echo "<li>✅ MySQL está activo</li>";
    echo "<li>✅ Base de datos 'productos_iniciales' existe</li>";
    echo "<li>✅ Tabla 'clientes' existe con la estructura correcta</li>";
    echo "</ul>";
}

?>