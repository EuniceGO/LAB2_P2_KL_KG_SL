# 📱 Funcionalidad de Códigos QR para Productos

## 🎯 **Funcionalidad Implementada**

Se ha agregado un sistema completo de generación automática de códigos QR para productos que incluye:

### ✨ **Características Principales:**

1. **Generación Automática de QR:**
   - Al crear un producto, se genera automáticamente un código QR
   - El QR contiene datos del producto en formato JSON
   - Se guarda como imagen en `assets/qr/`

2. **Datos incluidos en el QR:**
   ```json
   {
     "id": 1,
     "nombre": "Producto XYZ",
     "precio": 99.99,
     "categoria": 2
   }
   ```

3. **Gestión de QR:**
   - Visualización de códigos QR en la tabla de productos
   - Vista dedicada para ver código QR ampliado
   - Regeneración manual de códigos QR
   - Eliminación automática al eliminar producto

## 🔧 **Cambios Realizados:**

### **Base de Datos:**
- ✅ Agregado campo `codigo_qr VARCHAR(255)` a tabla `productos`

### **Clases Nuevas/Modificadas:**
- ✅ **Producto.php** - Agregado campo y métodos para código QR
- ✅ **QRCodeGenerator.php** - Clase para generar códigos QR
- ✅ **ProductoModel.php** - Métodos para manejar QR en BD
- ✅ **ProductoController.php** - Controladores para QR

### **Vistas Actualizadas:**
- ✅ **Productos/index.php** - Columna QR en tabla
- ✅ **Productos/view_qr.php** - Vista para ver QR ampliado
- ✅ Mensajes de éxito/error para operaciones QR

## 📋 **Instalación y Configuración:**

### **1. Actualizar Base de Datos:**
Ejecuta este SQL en tu base de datos:
```sql
ALTER TABLE productos
ADD COLUMN codigo_qr VARCHAR(255) DEFAULT NULL;
```

### **2. Crear Directorio para QR:**
El directorio `assets/qr/` se crea automáticamente.

### **3. URLs del Sistema:**

**Ver código QR de un producto:**
```
?c=producto&a=viewQR&id=1
```

**Regenerar código QR:**
```
?c=producto&a=regenerateQR&id=1
```

## 🖼️ **Cómo Funciona:**

### **Al Crear un Producto:**
1. Se guarda el producto en la BD
2. Se genera automáticamente el código QR
3. Se guarda la imagen QR en `assets/qr/`
4. Se actualiza la BD con la ruta del archivo QR

### **Generación de QR:**
- **Herramienta:** Google Chart API
- **Formato:** Imagen PNG
- **Tamaño:** 200x200 pixels (configurable)
- **Datos:** JSON con información del producto

### **Almacenamiento:**
- **Ruta:** `assets/qr/qr_producto_{ID}_{timestamp}.png`
- **Ejemplo:** `assets/qr/qr_producto_1_1696291234.png`

## 🎨 **Interfaz de Usuario:**

### **Tabla de Productos:**
- Nueva columna "QR" muestra miniatura del código
- Botón "Ver QR" para vista ampliada
- Icono cuando no hay QR disponible

### **Vista de QR:**
- Código QR ampliado
- Información del producto
- Botón para regenerar QR
- Datos JSON mostrados

## 🔧 **Funciones Disponibles:**

### **QRCodeGenerator::generateProductQR($producto)**
Genera URL del código QR para un producto.

### **QRCodeGenerator::generateAndSaveProductQR($producto)**
Genera y guarda el código QR como imagen local.

### **ProductoModel::regenerateQR($idProducto)**
Regenera el código QR para un producto existente.

### **ProductoController::viewQR($id)**
Muestra la vista ampliada del código QR.

## 🚀 **Uso del Sistema:**

### **Para crear productos con QR:**
1. Ve a "Nuevo Producto"
2. Llena el formulario
3. El QR se genera automáticamente al guardar

### **Para ver códigos QR:**
1. En la lista de productos, haz clic en el botón QR verde
2. Se abrirá la vista ampliada del código

### **Para regenerar QR:**
1. En la vista del QR, haz clic en "Regenerar QR"
2. Se creará un nuevo código con datos actualizados

## 📱 **Lectura de Códigos QR:**

Los códigos QR generados contienen datos JSON que pueden ser leídos por cualquier aplicación lectora de QR. Los datos incluyen:

- **ID del producto**
- **Nombre del producto**
- **Precio actual**
- **ID de categoría**

## 🛠️ **Personalización:**

### **Cambiar tamaño del QR:**
```php
// En QRCodeGenerator.php
$qrUrl = self::generateProductQR($producto, 300); // 300x300 pixels
```

### **Modificar datos del QR:**
```php
// En QRCodeGenerator::generateProductData()
$data = [
    'id' => $producto->getIdProducto(),
    'nombre' => $producto->getNombre(),
    'precio' => $producto->getPrecio(),
    'url' => 'http://mitienda.com/producto/' . $producto->getIdProducto()
];
```

### **Cambiar directorio de almacenamiento:**
```php
// En QRCodeGenerator::saveQRImage()
return self::saveQRImage($qrUrl, $fileName, 'mi_directorio/qr/');
```

## 🔒 **Seguridad y Limpieza:**

- Los archivos QR se eliminan automáticamente al eliminar productos
- Solo se generan QR para productos válidos
- Validación de existencia de archivos antes de mostrar

## 📊 **Beneficios:**

1. **Inventario Digital:** Códigos QR únicos para cada producto
2. **Trazabilidad:** Fácil identificación de productos
3. **Integración:** Datos JSON facilitan integración con apps móviles
4. **Automatización:** Generación automática sin intervención manual
5. **Flexibilidad:** Regeneración bajo demanda

## 🔍 **Próximas Mejoras:**

- [ ] Códigos QR con logotipo de la empresa
- [ ] Integración con lectores de códigos nativos
- [ ] API para lectura de QR desde aplicaciones móviles
- [ ] Estadísticas de escaneo de códigos QR
- [ ] Códigos QR con más información (descripción, imágenes)

## 🆘 **Solución de Problemas:**

### **QR no se muestra:**
- Verifica que el directorio `assets/qr/` tenga permisos de escritura
- Asegúrate de que la conexión a internet funcione (Google Chart API)

### **Error al generar QR:**
- Revisa los logs de PHP
- Verifica que `file_get_contents()` esté habilitado

### **Archivo QR no existe:**
- Usa el botón "Regenerar QR" en la vista del producto
- Verifica permisos del directorio `assets/qr/`