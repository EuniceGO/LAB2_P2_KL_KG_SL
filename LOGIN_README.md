# Sistema de Login y Gestión de Usuarios

Este proyecto ahora incluye un sistema completo de autenticación y gestión de usuarios con roles.

## 🚀 Nuevas Características

### Autenticación
- Sistema de login seguro con contraseñas hasheadas
- Sesiones de usuario
- Roles y permisos

### Gestión de Usuarios
- CRUD completo de usuarios
- Cambio de contraseñas
- Perfil de usuario

### Gestión de Roles
- CRUD completo de roles
- Asignación de roles a usuarios
- Control de permisos básico

## 📁 Archivos Creados

### Clases
- `clases/Usuario.php` - Clase Usuario con métodos de autenticación
- `clases/Role.php` - Clase Role para gestión de roles

### Modelos
- `modelos/UsuarioModel.php` - Operaciones de base de datos para usuarios
- `modelos/RoleModel.php` - Operaciones de base de datos para roles

### Controladores
- `controladores/UsuarioController.php` - Controlador con autenticación
- `controladores/RoleController.php` - Controlador para gestión de roles

### Vistas
#### Usuarios
- `vistas/Usuarios/login.php` - Formulario de login
- `vistas/Usuarios/dashboard.php` - Dashboard principal
- `vistas/Usuarios/index.php` - Lista de usuarios
- `vistas/Usuarios/create.php` - Crear usuario
- `vistas/Usuarios/edit.php` - Editar usuario
- `vistas/Usuarios/profile.php` - Perfil del usuario
- `vistas/Usuarios/change_password.php` - Cambiar contraseña

#### Roles
- `vistas/Roles/index.php` - Lista de roles
- `vistas/Roles/create.php` - Crear rol

### Configuración
- `config/create_auth_tables.sql` - Script SQL para crear las tablas

## 🗄️ Base de Datos

### Estructura de Tablas

```sql
-- Tabla de roles
CREATE TABLE roles (
  id_rol INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL UNIQUE,
  descripcion VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de usuarios
CREATE TABLE usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  id_rol INT NOT NULL,
  FOREIGN KEY (id_rol) REFERENCES roles(id_rol) 
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Instalación

1. **Ejecutar el script SQL:**
   ```bash
   # Desde la línea de comandos de MySQL
   mysql -u root -p nombre_base_datos < config/create_auth_tables.sql
   ```

2. **O desde phpMyAdmin:**
   - Copia el contenido de `config/create_auth_tables.sql`
   - Pégalo en la pestaña SQL de phpMyAdmin
   - Ejecuta

## 👤 Usuarios por Defecto

El script crea automáticamente:

### Administrador
- **Email:** admin@sistema.com
- **Contraseña:** admin123
- **Rol:** Administrador

### Usuario de Prueba
- **Email:** usuario@test.com
- **Contraseña:** usuario123
- **Rol:** Usuario

## 🔗 URLs del Sistema

### Autenticación
- Login: `?controller=usuario&action=login`
- Dashboard: `?controller=usuario&action=dashboard`
- Logout: `?controller=usuario&action=logout`

### Gestión de Usuarios
- Lista: `?controller=usuario&action=index`
- Crear: `?controller=usuario&action=create`
- Editar: `?controller=usuario&action=edit&id=1`
- Perfil: `?controller=usuario&action=profile`
- Cambiar contraseña: `?controller=usuario&action=changePassword`

### Gestión de Roles
- Lista: `?controller=role&action=index`
- Crear: `?controller=role&action=create`
- Editar: `?controller=role&action=edit&id=1`

## 🔒 Seguridad

### Características de Seguridad
- Contraseñas hasheadas con `password_hash()`
- Verificación de sesiones
- Protección contra eliminación accidental
- Validación de emails únicos
- Control de acceso básico

### Mejoras Recomendadas
- Implementar tokens CSRF
- Añadir límites de intentos de login
- Implementar recuperación de contraseñas
- Añadir log de actividades
- Mejores controles de permisos por rol

## 🎨 Interfaz

- Diseño responsive con Bootstrap 5
- Iconos Font Awesome
- Mensajes de éxito/error
- Navegación intuitiva
- Dashboard con estadísticas

## 🚦 Cómo Usar

1. **Primer acceso:**
   - Ve a `?controller=usuario&action=login`
   - Usa las credenciales del administrador

2. **Gestionar usuarios:**
   - Desde el dashboard, ve a "Administración" → "Usuarios"
   - Crea, edita o elimina usuarios

3. **Gestionar roles:**
   - Desde el dashboard, ve a "Administración" → "Roles"
   - Crea roles personalizados

4. **Cambiar contraseña:**
   - Desde el menú de usuario, selecciona "Cambiar Contraseña"

## 🔧 Personalización

### Añadir Nuevos Roles
```php
// En RoleModel
$role = new Role(null, "Nombre del Rol", "Descripción");
$roleModel->insert($role);
```

### Verificar Permisos
```php
// En cualquier controlador
if ($_SESSION['user_role_id'] == 1) {
    // Solo administradores
}
```

## 📞 Soporte

Para problemas o mejoras, revisa:
1. Los logs de errores de PHP
2. La consola del navegador
3. Los mensajes de error de la aplicación

## 🎯 Próximas Mejoras

- [ ] Sistema de permisos granular
- [ ] Recuperación de contraseñas
- [ ] Doble factor de autenticación
- [ ] API REST para usuarios
- [ ] Roles jerárquicos
- [ ] Auditoría de acciones