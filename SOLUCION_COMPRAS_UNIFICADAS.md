# 🛒 Sistema de Compras Unificado - Documentación

## ✅ Problema Resuelto

**Problema Original**: El sistema creaba un nuevo cliente en cada compra, incluso cuando el usuario ya estaba logueado como cliente.

**Solución Implementada**: Las compras ahora se asignan correctamente al cliente logueado existente.

## 🔧 Cambios Realizados

### 1. CarritoController.php - Corrección de Variables de Sesión
```php
// ANTES (variables incorrectas):
if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_role'] === 'Cliente') {
    $idClienteLogueado = $_SESSION['cliente_id'];

// DESPUÉS (variables correctas):
if (isset($_SESSION['user_id']) && $_SESSION['user_role_id'] == 2) {
    $clienteData = $clienteModel->obtenerPorUsuario($_SESSION['user_id']);
    $idClienteLogueado = $clienteData['id_cliente'];
```

### 2. Factura.php - Lógica de Cliente Logueado
```php
// ANTES: Siempre creaba/buscaba cliente
$this->idCliente = $this->clienteModel->insertarOActualizar($datosCliente);

// DESPUÉS: Verifica si ya hay cliente logueado
if ($this->idCliente !== null) {
    // Cliente ya establecido, usar datos existentes
    $datosClienteExistente = $clienteModel->obtenerPorId($this->idCliente);
} else {
    // No hay cliente logueado, crear/buscar cliente
    $this->idCliente = $this->clienteModel->insertarOActualizar($datosCliente);
}
```

## 🔄 Flujo del Sistema Corregido

### Para Cliente Logueado:
1. **Login**: Usuario inicia sesión como cliente (rol ID = 2)
2. **Sesión**: Se establecen variables `user_id`, `user_role_id`, etc.
3. **Carrito**: Cliente agrega productos al carrito
4. **Checkout**: Sistema detecta cliente logueado automáticamente
5. **Compra**: Factura se asigna al cliente existente (NO se crea nuevo cliente)
6. **Historial**: Compra aparece en el historial del cliente

### Para Cliente No Logueado:
1. **Carrito**: Usuario agrega productos sin iniciar sesión
2. **Checkout**: Usuario llena formulario con sus datos
3. **Compra**: Sistema busca/crea cliente basado en email
4. **Factura**: Se asigna al cliente encontrado/creado

## 🧪 Test de Verificación

Ejecutar `test_compra_cliente_logueado.php` para verificar:
- ✅ Cliente logueado detectado correctamente
- ✅ Factura asignada al cliente existente
- ✅ No se crean clientes duplicados
- ✅ Compra registrada en historial del cliente

## 📊 Variables de Sesión Unificadas

```php
$_SESSION['user_id']         // ID del usuario en tabla usuarios
$_SESSION['user_name']       // Nombre del usuario
$_SESSION['user_email']      // Email del usuario
$_SESSION['user_role_id']    // ID del rol (1=Admin, 2=Cliente)
$_SESSION['user_role']       // Nombre del rol
$_SESSION['cliente_id']      // ID en tabla clientes (solo para clientes)
```

## 🎯 Casos de Uso

### Caso 1: Cliente Registrado Compra
1. Cliente inicia sesión → `user_role_id = 2`
2. Sistema obtiene `id_cliente` de tabla clientes usando `user_id`
3. En checkout, factura se asigna directamente al `id_cliente`
4. Cliente puede ver sus compras en "Mi Historial"

### Caso 2: Usuario Anónimo Compra
1. Usuario no logueado llena formulario en checkout
2. Sistema busca cliente existente por email
3. Si existe: asigna factura al cliente encontrado
4. Si no existe: crea nuevo cliente y asigna factura

## 🚀 Beneficios

- ✅ **No más clientes duplicados**
- ✅ **Historial de compras unificado**
- ✅ **Mejor experiencia para clientes registrados**
- ✅ **Datos coherentes en la base de datos**
- ✅ **Seguimiento correcto de compras por cliente**

## 🔍 Verificación

Para verificar que funciona correctamente:
1. Registrar un cliente nuevo
2. Iniciar sesión como cliente
3. Agregar productos al carrito
4. Realizar compra
5. Verificar en dashboard del cliente que aparece la compra
6. Verificar en base de datos que no se creó cliente duplicado