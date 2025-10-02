# 🛒 Sistema E-commerce Completo - Guía de Uso

## 📋 Resumen del Sistema

Has implementado exitosamente un sistema completo de e-commerce con las siguientes características:

### ✅ Funcionalidades Implementadas

1. **Códigos QR para WiFi** 
   - QR codes configurados para funcionar en tu red WiFi (192.168.1.23)
   - Acceso móvil directo a información de productos

2. **Sistema de Carrito de Compras**
   - Gestión completa de sesiones
   - Agregar/quitar productos
   - Cálculo automático de totales

3. **Generación de Facturas**
   - Facturas profesionales en HTML
   - Guardado automático en base de datos
   - Numeración correlativa automática

4. **Persistencia de Datos**
   - Todas las compras se guardan en la base de datos
   - Historial completo de facturas
   - Detalles de cada compra registrados

## 🚀 Pasos para Usar el Sistema

### 1. Configurar Base de Datos
```
http://192.168.1.23/examen2/LAB2_P2_KL_KG_SL/instalar_base_datos.php
```
- Ejecuta este script para crear las tablas necesarias
- Incluye datos de ejemplo para pruebas

### 2. Regenerar Códigos QR (IMPORTANTE)
```
http://192.168.1.23/examen2/LAB2_P2_KL_KG_SL/fix_qr_codes.php
```
- Ejecuta este script para actualizar todos los QR codes
- Los QR codes apuntarán a tu IP de WiFi (192.168.1.23)

### 3. Probar el Sistema Completo
```
http://192.168.1.23/examen2/LAB2_P2_KL_KG_SL/test_carrito_completo.php
```
- Script de prueba automatizada que verifica:
  - Funcionamiento del carrito
  - Generación de facturas
  - Guardado en base de datos

### 4. Acceder desde el Móvil
1. Conecta tu móvil a la misma red WiFi
2. Escanea cualquier código QR de los productos
3. Verás la información del producto optimizada para móvil
4. Usa el botón "Agregar al Carrito" 

### 5. Flujo de Compra Completo
1. **Escanear QR** → Ver producto
2. **Agregar al carrito** → Producto guardado en sesión
3. **Ver carrito** → `http://192.168.1.23/examen2/LAB2_P2_KL_KG_SL/?c=carrito&a=ver`
4. **Finalizar compra** → Completar datos del cliente
5. **Generar factura** → Factura guardada en BD
6. **Ver historial** → `http://192.168.1.23/examen2/LAB2_P2_KL_KG_SL/?c=carrito&a=historial`

## 📱 URLs Principales del Sistema

### Gestión de Productos
- **Ver productos**: `/?c=producto&a=index`
- **Crear producto**: `/?c=producto&a=create`
- **Ver QR de producto**: `/?c=producto&a=view_qr&id=X`

### Sistema de Carrito
- **Ver carrito**: `/?c=carrito&a=ver`
- **Historial de facturas**: `/?c=carrito&a=historial`
- **Ver factura específica**: `/?c=carrito&a=verFactura&numero=XXX`

### Móvil (desde QR)
- **Producto móvil**: `/mobile/producto.php?id=X`
- **Carrito móvil**: `/mobile/carrito.php`
- **Checkout móvil**: `/mobile/checkout.php`

## 🗄️ Estructura de Base de Datos

### Tabla `facturas`
```sql
- id_factura (PK, AUTO_INCREMENT)
- numero_factura (UNIQUE)
- fecha_factura
- cliente_nombre, cliente_email, cliente_telefono, cliente_direccion
- subtotal, impuesto, total
- metodo_pago (efectivo/tarjeta/transferencia)
- estado (pendiente/pagada/cancelada)
- notas
```

### Tabla `factura_detalles`
```sql
- id_detalle (PK, AUTO_INCREMENT)
- id_factura (FK)
- id_producto
- nombre_producto
- precio_unitario
- cantidad
- subtotal
```

## 🔧 Archivos Principales del Sistema

### Clases Core
- `clases/Carrito.php` - Gestión completa del carrito
- `clases/Factura.php` - Generación y guardado de facturas
- `clases/QRCodeGenerator.php` - Generación de QR codes

### Modelos de Datos
- `modelos/FacturaModel.php` - Operaciones de BD para facturas
- `modelos/ProductoModel.php` - Gestión de productos

### Controladores
- `controladores/CarritoController.php` - Lógica del carrito y checkout

### Vistas Móviles
- `mobile/producto.php` - Vista de producto optimizada
- `mobile/carrito.php` - Carrito móvil
- `mobile/checkout.php` - Proceso de pago móvil

### Vistas de Facturas
- `vistas/Carrito/historial.php` - Historial de facturas
- `vistas/Carrito/ver_factura.php` - Detalle de factura individual

## 🎯 Casos de Uso Principales

### Caso 1: Cliente escanea QR desde móvil
1. Cliente escanea QR en tienda física
2. Ve información del producto en su móvil
3. Agrega producto al carrito
4. Continúa comprando o procede al checkout
5. Completa datos y genera factura
6. Recibe factura por email/impresión

### Caso 2: Administración desde PC
1. Administrador accede al panel web
2. Ve historial de todas las facturas
3. Puede buscar facturas por número o fecha
4. Puede ver detalles completos de cada venta
5. Puede reimprimir facturas

### Caso 3: Gestión de inventario
1. Crear nuevos productos
2. Generar QR codes automáticamente
3. Productos inmediatamente disponibles para venta móvil

## 🔍 Scripts de Diagnóstico

- `instalar_base_datos.php` - Instala/verifica BD
- `test_carrito_completo.php` - Prueba sistema completo
- `fix_qr_codes.php` - Regenera todos los QR codes
- `diagnostico_completo.php` - Diagnóstico general

## ⚡ Próximos Pasos Recomendados

1. **Ejecutar instalación de BD**: Asegúrate que las tablas estén creadas
2. **Regenerar QR codes**: Para que apunten a tu IP de WiFi
3. **Probar desde móvil**: Escanear QR y hacer una compra de prueba
4. **Configurar impresora**: Para imprimir facturas (opcional)
5. **Backup de BD**: Configurar respaldos automáticos

## 🛡️ Seguridad y Mantenimiento

- Las sesiones se limpian automáticamente al completar compra
- Todas las transacciones se registran con timestamp
- Los datos del cliente se validan antes de guardar
- Las facturas tienen numeración correlativa para auditoría

## 📞 Soporte

Si encuentras algún problema:
1. Ejecuta `diagnostico_completo.php` para ver errores
2. Verifica que tu IP sea realmente 192.168.1.23
3. Asegúrate que la base de datos esté funcionando
4. Revisa los logs de errores de PHP

---

## 🎉 ¡El sistema está listo para usar!

Tu e-commerce completo con QR codes está funcionando. Los clientes pueden escanear códigos desde sus móviles, agregar productos al carrito, completar compras y generar facturas que se guardan automáticamente en la base de datos.

**Última actualización**: Sistema completado con persistencia de datos y funcionalidad móvil completa.