<?php
/**
 * Test rápido de conexión MySQLi
 */

echo "<h1>Test de Conexión MySQLi</h1>";

try {
    require_once 'config/mysqli_connection.php';
    
    if ($conn) {
        echo "✅ Conexión MySQLi establecida correctamente<br>";
        echo "📊 Información de la conexión:<br>";
        echo "- Host: " . $conn->host_info . "<br>";
        echo "- Versión del servidor: " . $conn->server_info . "<br>";
        echo "- Versión del cliente: " . $conn->client_info . "<br>";
        
        // Test de ClienteModel
        echo "<h2>Test de ClienteModel</h2>";
        require_once 'modelos/ClienteModel.php';
        $clienteModel = new ClienteModel();
        echo "✅ ClienteModel creado correctamente<br>";
        
        // Test básico
        $total = $clienteModel->contarTotal();
        echo "✅ Método contarTotal() funciona: $total clientes<br>";
        
    } else {
        echo "❌ Error en la conexión MySQLi<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>