<?php
require_once 'modelos/ProductoModel.php';
require_once 'clases/Carrito.php';
require_once 'clases/Factura.php';
require_once 'clases/Producto.php';

class CarritoController {
    private $productoModel;

    public function __construct() {
        $this->productoModel = new ProductoModel();
    }

    /**
     * Muestra el contenido del carrito
     */
    public function index() {
        $resumenCarrito = Carrito::obtenerResumen();
        include 'vistas/Carrito/index.php';
    }

    /**
     * Agrega un producto al carrito
     */
    public function agregar($idProducto = null) {
        // Obtener ID del producto desde parámetros o POST
        $idProducto = $idProducto ?? ($_POST['id_producto'] ?? $_GET['id']);
        $cantidad = $_POST['cantidad'] ?? 1;
        $fromMobile = $_POST['from_mobile'] ?? $_GET['mobile'] ?? false;

        if ($idProducto) {
            $producto = $this->productoModel->getById($idProducto);
            
            if ($producto) {
                $exito = Carrito::agregarProducto($producto, $cantidad);
                
                if ($fromMobile) {
                    // Respuesta para móvil (vista optimizada)
                    $mensaje = $exito ? 'Producto agregado al carrito' : 'Error al agregar producto';
                    $resumenCarrito = Carrito::obtenerResumen();
                    include 'vistas/Carrito/mobile_success.php';
                } else {
                    // Respuesta para web normal
                    if ($exito) {
                        header('Location: ?c=carrito&a=index&agregado=1');
                    } else {
                        header('Location: ?c=producto&a=index&error=1');
                    }
                }
            } else {
                if ($fromMobile) {
                    $mensaje = 'Producto no encontrado';
                    include 'vistas/Carrito/mobile_error.php';
                } else {
                    header('Location: ?c=producto&a=index&error=producto_no_encontrado');
                }
            }
        } else {
            if ($fromMobile) {
                $mensaje = 'ID de producto no válido';
                include 'vistas/Carrito/mobile_error.php';
            } else {
                header('Location: ?c=producto&a=index&error=id_invalido');
            }
        }
        
        // Si no hay redirección, asegurar que no haya output adicional
        if (!$fromMobile) {
            exit;
        }
    }

    /**
     * Actualiza la cantidad de un producto en el carrito
     */
    public function actualizar($idProducto = null) {
        $idProducto = $idProducto ?? $_POST['id_producto'];
        $cantidad = $_POST['cantidad'] ?? 1;

        if ($idProducto && $cantidad >= 0) {
            Carrito::actualizarCantidad($idProducto, $cantidad);
            header('Location: ?c=carrito&a=index&actualizado=1');
        } else {
            header('Location: ?c=carrito&a=index&error=datos_invalidos');
        }
        exit;
    }

    /**
     * Elimina un producto del carrito
     */
    public function eliminar($idProducto = null) {
        $idProducto = $idProducto ?? $_GET['id'];

        if ($idProducto) {
            Carrito::eliminarProducto($idProducto);
            header('Location: ?c=carrito&a=index&eliminado=1');
        } else {
            header('Location: ?c=carrito&a=index&error=id_invalido');
        }
        exit;
    }

    /**
     * Vacía completamente el carrito
     */
    public function vaciar() {
        Carrito::vaciarCarrito();
        header('Location: ?c=carrito&a=index&vaciado=1');
        exit;
    }

    /**
     * Muestra la página de checkout
     */
    public function checkout() {
        $resumenCarrito = Carrito::obtenerResumen();
        
        if ($resumenCarrito['esta_vacio']) {
            header('Location: ?c=carrito&a=index&error=carrito_vacio');
            exit;
        }
        
        include 'vistas/Carrito/checkout.php';
    }

    /**
     * Procesa la compra y genera la factura
     */
    public function procesar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?c=carrito&a=checkout');
            exit;
        }

        $resumenCarrito = Carrito::obtenerResumen();
        
        if ($resumenCarrito['esta_vacio']) {
            header('Location: ?c=carrito&a=index&error=carrito_vacio');
            exit;
        }

        // Obtener datos del cliente del formulario
        $clienteInfo = [
            'nombre' => $_POST['nombre'] ?? 'Cliente General',
            'email' => $_POST['email'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'direccion' => $_POST['direccion'] ?? ''
        ];

        // Obtener método de pago
        $metodoPago = $_POST['metodo_pago'] ?? 'efectivo';

        try {
            // Crear factura
            $factura = new Factura();
            $factura->setClienteInfo($clienteInfo);
            $factura->setMetodoPago($metodoPago);
            $factura->agregarProductosDesdeCarrito(Carrito::obtenerDatosParaFactura());
            
            // Guardar factura en la base de datos
            $guardadoExitoso = $factura->guardarEnBaseDatos();
            
            if (!$guardadoExitoso) {
                throw new Exception("No se pudo guardar la factura en la base de datos");
            }
            
            // Generar HTML de la factura
            $facturaHTML = $factura->generarFacturaHTML();
            
            // Limpiar el carrito después de la compra exitosa
            Carrito::vaciarCarrito();
            
            // Mostrar la factura
            echo $facturaHTML;
            
        } catch (Exception $e) {
            // Error en el procesamiento
            $error = "Error al procesar la compra: " . $e->getMessage();
            include 'vistas/Carrito/checkout.php';
        }
    }

    /**
     * Vista móvil del carrito (para acceso desde QR)
     */
    public function mobile() {
        $resumenCarrito = Carrito::obtenerResumen();
        include 'vistas/Carrito/mobile_view.php';
    }

    /**
     * API para obtener el contador del carrito (AJAX)
     */
    public function contador() {
        header('Content-Type: application/json');
        $cantidad = Carrito::contarProductos();
        echo json_encode(['cantidad' => $cantidad]);
        exit;
    }

    /**
     * API para obtener resumen del carrito (AJAX)
     */
    public function resumen() {
        header('Content-Type: application/json');
        $resumen = Carrito::obtenerResumen();
        echo json_encode($resumen);
        exit;
    }

    /**
     * Muestra el historial de facturas
     */
    public function historial() {
        include 'vistas/Carrito/historial.php';
    }

    /**
     * Muestra una factura específica
     */
    public function verFactura($idFactura = null) {
        $idFactura = $idFactura ?? $_GET['id'] ?? null;
        
        if (!$idFactura) {
            header('Location: ?c=carrito&a=historial&error=id_invalido');
            exit;
        }

        require_once 'modelos/FacturaModel.php';
        $facturaModel = new FacturaModel();
        
        $datosFactura = $facturaModel->obtenerFacturaPorId($idFactura);
        $detallesFactura = $facturaModel->obtenerDetallesFactura($idFactura);
        
        if (!$datosFactura) {
            header('Location: ?c=carrito&a=historial&error=factura_no_encontrada');
            exit;
        }
        
        include 'vistas/Carrito/ver_factura.php';
    }

    /**
     * Imprime una factura específica
     */
    public function imprimirFactura($idFactura = null) {
        $idFactura = $idFactura ?? $_GET['id'] ?? null;
        
        if (!$idFactura) {
            echo "ID de factura no válido";
            exit;
        }

        require_once 'modelos/FacturaModel.php';
        $facturaModel = new FacturaModel();
        
        $datosFactura = $facturaModel->obtenerFacturaPorId($idFactura);
        $detallesFactura = $facturaModel->obtenerDetallesFactura($idFactura);
        
        if (!$datosFactura) {
            echo "Factura no encontrada";
            exit;
        }

        // Recrear objeto factura para generar HTML
        $factura = new Factura();
        $factura->setClienteInfo([
            'nombre' => $datosFactura['cliente_nombre'],
            'email' => $datosFactura['cliente_email'],
            'telefono' => $datosFactura['cliente_telefono'],
            'direccion' => $datosFactura['cliente_direccion']
        ]);
        
        // Convertir detalles a formato esperado
        $productosParaFactura = [];
        foreach ($detallesFactura as $detalle) {
            $productosParaFactura[] = [
                'id_producto' => $detalle['id_producto'],
                'nombre' => $detalle['nombre_producto'],
                'precio_unitario' => $detalle['precio_unitario'],
                'cantidad' => $detalle['cantidad'],
                'subtotal' => $detalle['subtotal']
            ];
        }
        
        $factura->agregarProductosDesdeCarrito($productosParaFactura);
        
        // Generar y mostrar HTML
        echo $factura->generarFacturaHTML();
    }

    /**
     * Página de éxito después de agregar producto desde móvil
     */
    public function exito() {
        $resumenCarrito = Carrito::obtenerResumen();
        include 'vistas/Carrito/mobile_success.php';
    }
}
?>