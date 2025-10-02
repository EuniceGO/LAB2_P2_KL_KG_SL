<?php
/**
 * Clase Carrito - Maneja el carrito de compras usando sesiones
 */
class Carrito {
    private static $session_key = 'carrito_productos';
    
    /**
     * Inicia la sesión si no está iniciada
     */
    private static function iniciarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Obtiene todos los productos del carrito
     * @return array - Array de productos en el carrito
     */
    public static function obtenerCarrito() {
        self::iniciarSesion();
        return $_SESSION[self::$session_key] ?? [];
    }
    
    /**
     * Agrega un producto al carrito
     * @param object $producto - Objeto producto
     * @param int $cantidad - Cantidad a agregar (default: 1)
     * @return bool - True si se agregó exitosamente
     */
    public static function agregarProducto($producto, $cantidad = 1) {
        self::iniciarSesion();
        
        $carritoActual = self::obtenerCarrito();
        $idProducto = $producto->getIdProducto();
        
        // Si el producto ya existe en el carrito, sumar la cantidad
        if (isset($carritoActual[$idProducto])) {
            $carritoActual[$idProducto]['cantidad'] += $cantidad;
        } else {
            // Agregar nuevo producto al carrito
            $carritoActual[$idProducto] = [
                'id' => $producto->getIdProducto(),
                'nombre' => $producto->getNombre(),
                'precio' => $producto->getPrecio(),
                'categoria' => $producto->getIdCategoria(),
                'cantidad' => $cantidad,
                'fecha_agregado' => date('Y-m-d H:i:s')
            ];
        }
        
        $_SESSION[self::$session_key] = $carritoActual;
        return true;
    }
    
    /**
     * Actualiza la cantidad de un producto en el carrito
     * @param int $idProducto - ID del producto
     * @param int $cantidad - Nueva cantidad
     * @return bool - True si se actualizó exitosamente
     */
    public static function actualizarCantidad($idProducto, $cantidad) {
        self::iniciarSesion();
        
        $carritoActual = self::obtenerCarrito();
        
        if (isset($carritoActual[$idProducto])) {
            if ($cantidad > 0) {
                $carritoActual[$idProducto]['cantidad'] = $cantidad;
            } else {
                // Si la cantidad es 0 o menor, eliminar el producto
                unset($carritoActual[$idProducto]);
            }
            
            $_SESSION[self::$session_key] = $carritoActual;
            return true;
        }
        
        return false;
    }
    
    /**
     * Elimina un producto del carrito
     * @param int $idProducto - ID del producto a eliminar
     * @return bool - True si se eliminó exitosamente
     */
    public static function eliminarProducto($idProducto) {
        self::iniciarSesion();
        
        $carritoActual = self::obtenerCarrito();
        
        if (isset($carritoActual[$idProducto])) {
            unset($carritoActual[$idProducto]);
            $_SESSION[self::$session_key] = $carritoActual;
            return true;
        }
        
        return false;
    }
    
    /**
     * Vacía completamente el carrito
     */
    public static function vaciarCarrito() {
        self::iniciarSesion();
        $_SESSION[self::$session_key] = [];
    }
    
    /**
     * Obtiene el número total de productos en el carrito
     * @return int - Cantidad total de productos
     */
    public static function contarProductos() {
        $carrito = self::obtenerCarrito();
        $total = 0;
        
        foreach ($carrito as $item) {
            $total += $item['cantidad'];
        }
        
        return $total;
    }
    
    /**
     * Calcula el subtotal del carrito (sin impuestos)
     * @return float - Subtotal del carrito
     */
    public static function calcularSubtotal() {
        $carrito = self::obtenerCarrito();
        $subtotal = 0;
        
        foreach ($carrito as $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
        }
        
        return $subtotal;
    }
    
    /**
     * Calcula el impuesto (IVA) - configurable
     * @param float $porcentaje - Porcentaje de impuesto (default: 16%)
     * @return float - Monto del impuesto
     */
    public static function calcularImpuesto($porcentaje = 16) {
        $subtotal = self::calcularSubtotal();
        return $subtotal * ($porcentaje / 100);
    }
    
    /**
     * Calcula el total del carrito (subtotal + impuestos)
     * @param float $porcentajeImpuesto - Porcentaje de impuesto (default: 16%)
     * @return float - Total del carrito
     */
    public static function calcularTotal($porcentajeImpuesto = 16) {
        $subtotal = self::calcularSubtotal();
        $impuesto = self::calcularImpuesto($porcentajeImpuesto);
        return $subtotal + $impuesto;
    }
    
    /**
     * Verifica si el carrito está vacío
     * @return bool - True si está vacío
     */
    public static function estaVacio() {
        $carrito = self::obtenerCarrito();
        return empty($carrito);
    }
    
    /**
     * Obtiene un resumen del carrito para mostrar
     * @return array - Resumen con totales y productos
     */
    public static function obtenerResumen() {
        $carrito = self::obtenerCarrito();
        
        return [
            'productos' => $carrito,
            'cantidad_total' => self::contarProductos(),
            'subtotal' => self::calcularSubtotal(),
            'impuesto' => self::calcularImpuesto(),
            'total' => self::calcularTotal(),
            'esta_vacio' => self::estaVacio()
        ];
    }
    
    /**
     * Obtiene los datos del carrito formateados para factura
     * @return array - Datos formateados para factura
     */
    public static function obtenerDatosParaFactura() {
        $carrito = self::obtenerCarrito();
        $datosFactura = [];
        
        foreach ($carrito as $item) {
            $datosFactura[] = [
                'id_producto' => $item['id'],
                'nombre' => $item['nombre'],
                'precio_unitario' => $item['precio'],
                'cantidad' => $item['cantidad'],
                'subtotal' => $item['precio'] * $item['cantidad']
            ];
        }
        
        return $datosFactura;
    }
}
?>