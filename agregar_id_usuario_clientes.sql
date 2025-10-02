-- Script SQL para agregar la columna id_usuario a la tabla clientes
-- Ejecutar este script en tu base de datos MySQL

-- Agregar la columna id_usuario a la tabla clientes
ALTER TABLE clientes 
ADD COLUMN id_usuario INT DEFAULT NULL;

-- Agregar la clave foránea que referencia a la tabla usuarios
ALTER TABLE clientes 
ADD CONSTRAINT fk_clientes_usuarios 
FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- Verificar la estructura actualizada de la tabla clientes
DESCRIBE clientes;

-- Mostrar información sobre las claves foráneas
SELECT 
  CONSTRAINT_NAME,
  COLUMN_NAME,
  REFERENCED_TABLE_NAME,
  REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_NAME = 'clientes' 
AND TABLE_SCHEMA = DATABASE()
AND REFERENCED_TABLE_NAME IS NOT NULL;