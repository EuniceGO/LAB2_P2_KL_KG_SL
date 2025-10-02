<?php
/**
 * Script para verificar la estructura de las tablas existentes
 */

// Configurar conexión MySQLi directamente
$host = "localhost";
$usuario = "root";
$password = "";
$baseDatos = "productos_iniciales";

// Crear conexión
$conn = new mysqli($host, $usuario, $password, $baseDatos);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verificación de Estructura de BD</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-4'>
        <h2>🔍 Verificación de Estructura de Base de Datos</h2>";

// Verificar tabla cliente
echo "<div class='card mt-4'>
    <div class='card-header bg-primary text-white'>
        <h4>Tabla: cliente</h4>
    </div>
    <div class='card-body'>";

$result = $conn->query("DESCRIBE cliente");
if ($result) {
    echo "<table class='table table-striped'>
        <thead>
            <tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th></tr>
        </thead>
        <tbody>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td><strong>{$row['Field']}</strong></td>
            <td>{$row['Type']}</td>
            <td>{$row['Null']}</td>
            <td>{$row['Key']}</td>
            <td>{$row['Default']}</td>
        </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
}

echo "</div></div>";

// Verificar tabla factura
echo "<div class='card mt-4'>
    <div class='card-header bg-success text-white'>
        <h4>Tabla: factura</h4>
    </div>
    <div class='card-body'>";

$result = $conn->query("DESCRIBE factura");
if ($result) {
    echo "<table class='table table-striped'>
        <thead>
            <tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th></tr>
        </thead>
        <tbody>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td><strong>{$row['Field']}</strong></td>
            <td>{$row['Type']}</td>
            <td>{$row['Null']}</td>
            <td>{$row['Key']}</td>
            <td>{$row['Default']}</td>
        </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
}

echo "</div></div>";

// Verificar tabla detalle_factura
echo "<div class='card mt-4'>
    <div class='card-header bg-info text-white'>
        <h4>Tabla: detalle_factura</h4>
    </div>
    <div class='card-body'>";

$result = $conn->query("DESCRIBE detalle_factura");
if ($result) {
    echo "<table class='table table-striped'>
        <thead>
            <tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th></tr>
        </thead>
        <tbody>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td><strong>{$row['Field']}</strong></td>
            <td>{$row['Type']}</td>
            <td>{$row['Null']}</td>
            <td>{$row['Key']}</td>
            <td>{$row['Default']}</td>
        </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
}

echo "</div></div>";

// Verificar datos existentes
echo "<div class='card mt-4'>
    <div class='card-header bg-warning text-dark'>
        <h4>📊 Datos Existentes</h4>
    </div>
    <div class='card-body'>";

$tables = ['cliente', 'factura', 'detalle_factura'];
foreach ($tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as total FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p><strong>$table:</strong> {$row['total']} registros</p>";
    }
}

echo "</div></div>";

echo "
        <div class='mt-4 text-center'>
            <a href='test_carrito_completo.php' class='btn btn-primary'>Probar Sistema</a>
            <a href='vistas/Clientes/index.php' class='btn btn-success'>Ver Clientes</a>
        </div>
    </div>
</body>
</html>";

$conn->close();
?>