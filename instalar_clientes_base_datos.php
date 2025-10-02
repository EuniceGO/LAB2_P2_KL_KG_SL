<?php
/**
 * Script para verificar las tablas de clientes y facturas
 * Ejecuta este archivo desde el navegador para verificar las tablas existentes
 */

// Configurar conexión MySQLi directamente
$host = "localhost";
$usuario = "root";
$password = "";
$baseDatos = "productos_iniciales";

// Crear conexión
$conn = new mysqli($host, $usuario, $password, $baseDatos);

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Instalación de Base de Datos - Sistema de Facturas y Clientes</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background-color: #f8f9fa; }
        .install-card { margin: 20px auto; max-width: 800px; }
        .log-item { margin: 5px 0; padding: 10px; border-radius: 5px; }
        .log-success { background-color: #d4edda; border-left: 4px solid #28a745; }
        .log-error { background-color: #f8d7da; border-left: 4px solid #dc3545; }
        .log-info { background-color: #d1ecf1; border-left: 4px solid #17a2b8; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='install-card'>
            <div class='card'>
                <div class='card-header bg-primary text-white'>
                    <h2><i class='fas fa-database'></i> Instalación de Base de Datos - Sistema Completo</h2>
                </div>
                <div class='card-body'>";

try {
    echo "<div class='log-info log-item'>🔄 Iniciando instalación de base de datos...</div>";
    
    // Verificar conexión
    if (!$conn) {
        throw new Exception("No se pudo conectar a la base de datos: " . mysqli_connect_error());
    }
    
    echo "<div class='log-success log-item'>✅ Conexión a base de datos establecida.</div>";
    
    // SQL para crear tabla de clientes PRIMERO
    $sqlClientes = "
    CREATE TABLE IF NOT EXISTS clientes (
        id_cliente INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        telefono VARCHAR(50) NULL,
        direccion TEXT NULL,
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_nombre (nombre)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sqlClientes)) {
        echo "<div class='log-success log-item'>✅ Tabla 'clientes' creada correctamente.</div>";
    } else {
        throw new Exception("Error al crear tabla 'clientes': " . $conn->error);
    }
    
    // SQL para crear tabla de facturas (CON REFERENCIA A CLIENTES)
    $sqlFacturas = "
    CREATE TABLE IF NOT EXISTS facturas (
        id_factura INT AUTO_INCREMENT PRIMARY KEY,
        numero_factura VARCHAR(50) UNIQUE NOT NULL,
        fecha_factura DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        id_cliente INT NULL,
        cliente_nombre VARCHAR(255) NOT NULL DEFAULT 'Cliente General',
        cliente_email VARCHAR(255) NULL,
        cliente_telefono VARCHAR(50) NULL,
        cliente_direccion TEXT NULL,
        subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        impuesto DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        metodo_pago ENUM('efectivo', 'tarjeta', 'transferencia') DEFAULT 'efectivo',
        estado ENUM('pendiente', 'pagada', 'cancelada') DEFAULT 'pendiente',
        notas TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE SET NULL,
        INDEX idx_cliente (id_cliente),
        INDEX idx_fecha (fecha_factura),
        INDEX idx_estado (estado)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sqlFacturas)) {
        echo "<div class='log-success log-item'>✅ Tabla 'facturas' creada correctamente.</div>";
    } else {
        throw new Exception("Error al crear tabla 'facturas': " . $conn->error);
    }
    
    // SQL para crear tabla de detalles de factura
    $sqlDetalles = "
    CREATE TABLE IF NOT EXISTS factura_detalles (
        id_detalle INT AUTO_INCREMENT PRIMARY KEY,
        id_factura INT NOT NULL,
        id_producto INT NOT NULL,
        nombre_producto VARCHAR(255) NOT NULL,
        precio_unitario DECIMAL(10, 2) NOT NULL,
        cantidad INT NOT NULL DEFAULT 1,
        subtotal DECIMAL(10, 2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_factura) REFERENCES facturas(id_factura) ON DELETE CASCADE,
        INDEX idx_factura (id_factura),
        INDEX idx_producto (id_producto)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sqlDetalles)) {
        echo "<div class='log-success log-item'>✅ Tabla 'factura_detalles' creada correctamente.</div>";
    } else {
        throw new Exception("Error al crear tabla 'factura_detalles': " . $conn->error);
    }
    
    // Crear índices adicionales para mejor rendimiento
    $indices = [
        "CREATE INDEX IF NOT EXISTS idx_facturas_fecha ON facturas(fecha_factura)",
        "CREATE INDEX IF NOT EXISTS idx_facturas_estado ON facturas(estado)",
        "CREATE INDEX IF NOT EXISTS idx_facturas_numero ON facturas(numero_factura)",
        "CREATE INDEX IF NOT EXISTS idx_detalles_producto ON factura_detalles(id_producto, id_factura)",
        "CREATE INDEX IF NOT EXISTS idx_clientes_fecha ON clientes(fecha_registro)"
    ];
    
    foreach ($indices as $index) {
        if ($conn->query($index)) {
            echo "<div class='log-success log-item'>✅ Índice creado correctamente.</div>";
        } else {
            echo "<div class='log-error log-item'>⚠️ Advertencia al crear índice: " . $conn->error . "</div>";
        }
    }
    
    // Verificar que las tablas se crearon correctamente
    $tablas = ['clientes', 'facturas', 'factura_detalles'];
    foreach ($tablas as $tabla) {
        $result = $conn->query("SHOW TABLES LIKE '$tabla'");
        if ($result && $result->num_rows > 0) {
            // Contar registros existentes
            $count = $conn->query("SELECT COUNT(*) as total FROM $tabla")->fetch_assoc();
            echo "<div class='log-info log-item'>📊 Tabla '$tabla' verificada: {$count['total']} registros.</div>";
        }
    }
    
    // Insertar datos de ejemplo si las tablas están vacías
    $countClientes = $conn->query("SELECT COUNT(*) as total FROM clientes")->fetch_assoc();
    $countFacturas = $conn->query("SELECT COUNT(*) as total FROM facturas")->fetch_assoc();
    
    if ($countClientes['total'] == 0) {
        echo "<div class='log-info log-item'>📝 Insertando cliente de ejemplo...</div>";
        
        $ejemploCliente = "
        INSERT INTO clientes (nombre, email, telefono, direccion) VALUES
        ('Cliente de Ejemplo', 'ejemplo@correo.com', '555-0123', 'Dirección de Ejemplo 123, Ciudad')";
        
        if ($conn->query($ejemploCliente)) {
            $idClienteEjemplo = $conn->insert_id;
            echo "<div class='log-success log-item'>✅ Cliente de ejemplo insertado con ID: $idClienteEjemplo</div>";
        }
    } else {
        // Si ya hay clientes, tomar el primero para la factura de ejemplo
        $result = $conn->query("SELECT id_cliente FROM clientes LIMIT 1");
        $cliente = $result->fetch_assoc();
        $idClienteEjemplo = $cliente['id_cliente'];
    }
    
    if ($countFacturas['total'] == 0 && isset($idClienteEjemplo)) {
        echo "<div class='log-info log-item'>📝 Insertando factura de ejemplo...</div>";
        
        $ejemploFactura = "
        INSERT INTO facturas (numero_factura, id_cliente, cliente_nombre, cliente_email, subtotal, impuesto, total, metodo_pago, estado, notas) VALUES
        ('FAC-" . date('Ymd') . "-0001', $idClienteEjemplo, 'Cliente de Ejemplo', 'ejemplo@correo.com', 100.00, 16.00, 116.00, 'efectivo', 'pagada', 'Factura de ejemplo del sistema')";
        
        if ($conn->query($ejemploFactura)) {
            $idFacturaEjemplo = $conn->insert_id;
            
            $ejemploDetalle = "
            INSERT INTO factura_detalles (id_factura, id_producto, nombre_producto, precio_unitario, cantidad, subtotal) VALUES
            ($idFacturaEjemplo, 1, 'Producto de Ejemplo', 50.00, 2, 100.00)";
            
            if ($conn->query($ejemploDetalle)) {
                echo "<div class='log-success log-item'>✅ Datos de ejemplo insertados correctamente.</div>";
            }
        }
    }
    
    echo "<div class='log-success log-item'>🎉 ¡Instalación completada exitosamente!</div>";
    
    // Mostrar resumen
    echo "<div class='mt-4 p-3 bg-light border rounded'>
        <h5>📋 Resumen de Instalación:</h5>
        <ul>
            <li>✅ Tabla 'clientes' - Para almacenar información de clientes</li>
            <li>✅ Tabla 'facturas' - Para almacenar datos de facturas</li>
            <li>✅ Tabla 'factura_detalles' - Para almacenar productos de cada factura</li>
            <li>✅ Relación clientes ↔ facturas establecida</li>
            <li>✅ Índices de rendimiento creados</li>
            <li>✅ Datos de ejemplo insertados</li>
        </ul>
    </div>";
    
    echo "<div class='mt-4 p-3 bg-success text-white rounded'>
        <h5>🚀 ¡Sistema listo para usar!</h5>
        <p>Ahora puedes:</p>
        <ul>
            <li>Realizar compras desde códigos QR</li>
            <li>Guardar automáticamente datos de clientes</li>
            <li>Generar facturas con información completa</li>
            <li>Ver historial de compras por cliente</li>
            <li>Gestionar base de datos de clientes</li>
        </ul>
    </div>";
    
} catch (Exception $e) {
    echo "<div class='log-error log-item'>❌ Error durante la instalación: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='mt-3 p-3 bg-danger text-white rounded'>
        <h5>🚨 Error de Instalación</h5>
        <p>Por favor:</p>
        <ul>
            <li>Verifica la conexión a la base de datos</li>
            <li>Asegúrate de tener permisos para crear tablas</li>
            <li>Revisa el archivo config/cn.php</li>
        </ul>
    </div>";
}

echo "
                </div>
                <div class='card-footer text-center'>
                    <div class='btn-group' role='group'>
                        <a href='?c=carrito&a=historial' class='btn btn-primary'>Ver Facturas</a>
                        <a href='?c=producto&a=index' class='btn btn-success'>Ver Productos</a>
                        <a href='test_carrito_completo.php' class='btn btn-info'>Probar Sistema</a>
                        <a href='javascript:location.reload()' class='btn btn-secondary'>Reinstalar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>";
?>