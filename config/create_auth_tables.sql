-- Script SQL para crear las tablas de usuarios y roles
-- Ejecutar este script en tu base de datos MySQL

-- Crear tabla de roles
CREATE TABLE IF NOT EXISTS roles (
  id_rol INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL UNIQUE,
  descripcion VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crear tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  id_rol INT NOT NULL,
  FOREIGN KEY (id_rol) REFERENCES roles(id_rol) 
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar roles por defecto
INSERT IGNORE INTO roles (nombre, descripcion) VALUES 
('Administrador', 'Acceso completo al sistema, puede gestionar usuarios, roles y todos los módulos'),
('Usuario', 'Acceso básico al sistema, puede gestionar productos, categorías e imágenes'),
('Moderador', 'Acceso intermedio, puede gestionar contenido pero no usuarios');

-- Insertar usuario administrador por defecto
-- Contraseña: admin123
INSERT IGNORE INTO usuarios (nombre, email, password, id_rol) VALUES 
('Administrador del Sistema', 'admin@sistema.com', 'admin123', 1);

-- Insertar usuario básico de ejemplo
-- Contraseña: usuario123
INSERT IGNORE INTO usuarios (nombre, email, password, id_rol) VALUES 
('Usuario de Prueba', 'usuario@test.com', 'usuario123', 2);

-- Agregar campo codigo_qr a la tabla productos
ALTER TABLE productos
ADD COLUMN codigo_qr VARCHAR(255) DEFAULT NULL;

-- Verificar las tablas creadas
SELECT 'Roles creados:' as Info;
SELECT * FROM roles;

SELECT 'Usuarios creados:' as Info;
SELECT u.id_usuario, u.nombre, u.email, r.nombre as rol 
FROM usuarios u 
JOIN roles r ON u.id_rol = r.id_rol;