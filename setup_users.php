<?php
// Script para crear usuarios de prueba con contraseñas correctas
require_once 'config/cn.php';

try {
    $cn = new CNpdo();
    
    // Crear las tablas si no existen
    $cn->ejecutar("
        CREATE TABLE IF NOT EXISTS roles (
          id_rol INT AUTO_INCREMENT PRIMARY KEY,
          nombre VARCHAR(50) NOT NULL UNIQUE,
          descripcion VARCHAR(255)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    
    $cn->ejecutar("
        CREATE TABLE IF NOT EXISTS usuarios (
          id_usuario INT AUTO_INCREMENT PRIMARY KEY,
          nombre VARCHAR(100) NOT NULL,
          email VARCHAR(100) UNIQUE NOT NULL,
          password VARCHAR(255) NOT NULL,
          id_rol INT NOT NULL,
          FOREIGN KEY (id_rol) REFERENCES roles(id_rol) 
            ON DELETE RESTRICT ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    
    // Insertar roles
    $cn->ejecutar("INSERT IGNORE INTO roles (nombre, descripcion) VALUES (?, ?)", 
        ['Administrador', 'Acceso completo al sistema']);
    $cn->ejecutar("INSERT IGNORE INTO roles (nombre, descripcion) VALUES (?, ?)", 
        ['Usuario', 'Acceso básico al sistema']);
    $cn->ejecutar("INSERT IGNORE INTO roles (nombre, descripcion) VALUES (?, ?)", 
        ['Moderador', 'Acceso intermedio']);
    
    // Contraseñas en texto plano (sin hashear)
    $adminPassword = 'admin123';
    $userPassword = 'usuario123';
    
    // Eliminar usuarios existentes con estos emails (si existen)
    $cn->ejecutar("DELETE FROM usuarios WHERE email IN (?, ?)", 
        ['admin@sistema.com', 'usuario@test.com']);
    
    // Insertar usuarios con contraseñas en texto plano
    $cn->ejecutar("INSERT INTO usuarios (nombre, email, password, id_rol) VALUES (?, ?, ?, ?)", 
        ['Administrador del Sistema', 'admin@sistema.com', $adminPassword, 1]);
    
    $cn->ejecutar("INSERT INTO usuarios (nombre, email, password, id_rol) VALUES (?, ?, ?, ?)", 
        ['Usuario de Prueba', 'usuario@test.com', $userPassword, 2]);
    
    echo "<h2>✅ ¡Base de datos configurada exitosamente!</h2>";
    echo "<h3>Usuarios creados:</h3>";
    echo "<ul>";
    echo "<li><strong>Administrador:</strong> admin@sistema.com / admin123</li>";
    echo "<li><strong>Usuario:</strong> usuario@test.com / usuario123</li>";
    echo "</ul>";
    echo "<h3>Contraseñas guardadas en texto plano:</h3>";
    echo "<p><strong>Admin:</strong> " . $adminPassword . "</p>";
    echo "<p><strong>Usuario:</strong> " . $userPassword . "</p>";
    echo "<br>";
    echo "<a href='?controller=usuario&action=login' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔑 Ir al Login</a>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Asegúrate de que la base de datos esté conectada correctamente.</p>";
}
?>