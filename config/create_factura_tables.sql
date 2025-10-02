-- Script SQL para crear tablas de facturas y detalles de compra
-- Ejecutar este script en phpMyAdmin o desde línea de comandos

-- Tabla de facturas
CREATE TABLE IF NOT EXISTS facturas (
    id_factura INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura VARCHAR(50) UNIQUE NOT NULL,
    fecha_factura DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de detalles de factura
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
);

-- Tabla de sesiones de carrito (opcional - para persistir carritos)
CREATE TABLE IF NOT EXISTS carrito_sesiones (
    id_sesion VARCHAR(255) PRIMARY KEY,
    id_producto INT NOT NULL,
    nombre_producto VARCHAR(255) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sesion (id_sesion),
    INDEX idx_producto (id_producto)
);

-- Insertar datos de ejemplo (opcional)
INSERT INTO facturas (numero_factura, cliente_nombre, subtotal, impuesto, total, metodo_pago, estado) VALUES
('FAC-20251002-0001', 'Cliente Ejemplo', 100.00, 16.00, 116.00, 'efectivo', 'pagada'),
('FAC-20251002-0002', 'María García', 250.50, 40.08, 290.58, 'tarjeta', 'pagada');

-- Crear vista para reportes de ventas
CREATE OR REPLACE VIEW vista_ventas AS
SELECT 
    f.id_factura,
    f.numero_factura,
    f.fecha_factura,
    f.cliente_nombre,
    f.total,
    f.metodo_pago,
    f.estado,
    COUNT(fd.id_detalle) as total_productos,
    SUM(fd.cantidad) as cantidad_total
FROM facturas f
LEFT JOIN factura_detalles fd ON f.id_factura = fd.id_factura
GROUP BY f.id_factura
ORDER BY f.fecha_factura DESC;

-- Crear vista para productos más vendidos
CREATE OR REPLACE VIEW productos_mas_vendidos AS
SELECT 
    fd.id_producto,
    fd.nombre_producto,
    SUM(fd.cantidad) as total_vendido,
    SUM(fd.subtotal) as ingresos_totales,
    COUNT(DISTINCT fd.id_factura) as numero_facturas,
    AVG(fd.precio_unitario) as precio_promedio
FROM factura_detalles fd
INNER JOIN facturas f ON fd.id_factura = f.id_factura
WHERE f.estado = 'pagada'
GROUP BY fd.id_producto, fd.nombre_producto
ORDER BY total_vendido DESC;

-- Crear función para obtener total de ventas por período
DELIMITER //
CREATE FUNCTION IF NOT EXISTS total_ventas_periodo(fecha_inicio DATE, fecha_fin DATE)
RETURNS DECIMAL(10,2)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE total DECIMAL(10,2) DEFAULT 0.00;
    
    SELECT COALESCE(SUM(total), 0.00) INTO total
    FROM facturas 
    WHERE DATE(fecha_factura) BETWEEN fecha_inicio AND fecha_fin
    AND estado = 'pagada';
    
    RETURN total;
END //
DELIMITER ;

-- Ejemplos de consultas útiles:

-- 1. Ventas del día actual:
-- SELECT * FROM vista_ventas WHERE DATE(fecha_factura) = CURDATE();

-- 2. Total de ventas del mes:
-- SELECT total_ventas_periodo(DATE_FORMAT(NOW(), '%Y-%m-01'), LAST_DAY(NOW()));

-- 3. Productos más vendidos:
-- SELECT * FROM productos_mas_vendidos LIMIT 10;

-- 4. Facturas pendientes:
-- SELECT * FROM vista_ventas WHERE estado = 'pendiente';

-- 5. Ventas por método de pago:
-- SELECT metodo_pago, COUNT(*) as cantidad, SUM(total) as total_ingresos 
-- FROM facturas WHERE estado = 'pagada' GROUP BY metodo_pago;

COMMIT;