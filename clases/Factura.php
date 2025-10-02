<?php
/**
 * Clase Factura - Maneja la generación de facturas y tickets de compra
 */
require_once 'modelos/FacturaModel.php';
require_once 'modelos/ClienteModel.php';

class Factura {
    private $numeroFactura;
    private $fecha;
    private $clienteInfo;
    private $productos;
    private $subtotal;
    private $impuesto;
    private $total;
    private $metodoPago;
    private $facturaModel;
    private $clienteModel;
    private $idFactura;
    private $idCliente;
    
    public function __construct() {
        $this->numeroFactura = $this->generarNumeroFactura();
        $this->fecha = date('Y-m-d H:i:s');
        $this->productos = [];
        $this->subtotal = 0;
        $this->impuesto = 0;
        $this->total = 0;
        $this->metodoPago = 'efectivo';
        $this->facturaModel = new FacturaModel();
        $this->clienteModel = new ClienteModel();
        $this->idFactura = null;
        $this->idCliente = null;
    }
    
    /**
     * Genera un número de factura único
     * @return string - Número de factura
     */
    private function generarNumeroFactura() {
        $prefijo = 'FAC';
        $fecha = date('Ymd');
        $aleatorio = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $prefijo . '-' . $fecha . '-' . $aleatorio;
    }
    
    /**
     * Establece el método de pago
     * @param string $metodo - Método de pago
     */
    public function setMetodoPago($metodo) {
        $this->metodoPago = $metodo;
    }
    
    /**
     * Establece la información del cliente
     * @param array $info - Información del cliente
     */
    public function setClienteInfo($info) {
        $this->clienteInfo = $info;
    }
    
    /**
     * Agrega productos desde el carrito
     * @param array $productosCarrito - Productos del carrito
     */
    public function agregarProductosDesdeCarrito($productosCarrito) {
        $this->productos = $productosCarrito;
        $this->calcularTotales();
    }
    
    /**
     * Calcula los totales de la factura
     */
    private function calcularTotales() {
        $this->subtotal = 0;
        
        foreach ($this->productos as $producto) {
            $this->subtotal += $producto['precio_unitario'] * $producto['cantidad'];
        }
        
        $this->impuesto = $this->subtotal * 0.16; // 16% IVA
        $this->total = $this->subtotal + $this->impuesto;
    }
    
    /**
     * Genera la factura en formato HTML para imprimir
     * @return string - HTML de la factura
     */
    public function generarFacturaHTML() {
        $html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Factura ' . $this->numeroFactura . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #f5f5f5;
                }
                .factura {
                    background: white;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0,0,0,0.1);
                }
                .header {
                    text-align: center;
                    border-bottom: 3px solid #007bff;
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }
                .logo {
                    font-size: 2.5rem;
                    color: #007bff;
                    margin-bottom: 10px;
                }
                .empresa-info {
                    color: #666;
                    margin-bottom: 10px;
                }
                .factura-info {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 30px;
                    flex-wrap: wrap;
                }
                .info-box {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 8px;
                    margin-bottom: 15px;
                    flex: 1;
                    margin-right: 15px;
                }
                .info-box:last-child {
                    margin-right: 0;
                }
                .info-box h4 {
                    margin: 0 0 10px 0;
                    color: #333;
                    font-size: 1.1rem;
                }
                .productos-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 30px;
                }
                .productos-table th,
                .productos-table td {
                    padding: 12px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                .productos-table th {
                    background-color: #007bff;
                    color: white;
                    font-weight: bold;
                }
                .productos-table tr:hover {
                    background-color: #f5f5f5;
                }
                .totales {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                    margin-top: 20px;
                }
                .total-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 10px;
                    padding: 5px 0;
                }
                .total-final {
                    font-size: 1.3rem;
                    font-weight: bold;
                    color: #007bff;
                    border-top: 2px solid #007bff;
                    padding-top: 10px;
                    margin-top: 15px;
                }
                .footer {
                    text-align: center;
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 1px solid #ddd;
                    color: #666;
                }
                .print-btn {
                    background: #007bff;
                    color: white;
                    border: none;
                    padding: 12px 25px;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 1rem;
                    margin: 20px 0;
                }
                .print-btn:hover {
                    background: #0056b3;
                }
                @media print {
                    body { background: white; }
                    .print-btn { display: none; }
                    .factura { box-shadow: none; }
                }
                @media (max-width: 600px) {
                    .factura-info {
                        flex-direction: column;
                    }
                    .info-box {
                        margin-right: 0;
                    }
                }
            </style>
        </head>
        <body>
            <div class="factura">
                <!-- Header -->
                <div class="header">
                    <div class="logo">🛒 Mi Tienda Online</div>
                    <div class="empresa-info">
                        <p><strong>Dirección:</strong> Calle Principal #123, Ciudad</p>
                        <p><strong>Teléfono:</strong> (123) 456-7890 | <strong>Email:</strong> info@mitienda.com</p>
                        <p><strong>RFC:</strong> MTO123456789</p>
                    </div>
                </div>
                
                <!-- Información de la factura -->
                <div class="factura-info">
                    <div class="info-box">
                        <h4>📄 Datos de la Factura</h4>
                        <p><strong>Número:</strong> ' . $this->numeroFactura . '</p>
                        <p><strong>Fecha:</strong> ' . date('d/m/Y H:i', strtotime($this->fecha)) . '</p>
                        <p><strong>Método de pago:</strong> Efectivo</p>
                    </div>
                    <div class="info-box">
                        <h4>👤 Datos del Cliente</h4>';
        
        if ($this->clienteInfo) {
            $html .= '
                        <p><strong>Nombre:</strong> ' . htmlspecialchars($this->clienteInfo['nombre'] ?? 'Cliente General') . '</p>
                        <p><strong>Email:</strong> ' . htmlspecialchars($this->clienteInfo['email'] ?? 'N/A') . '</p>
                        <p><strong>Teléfono:</strong> ' . htmlspecialchars($this->clienteInfo['telefono'] ?? 'N/A') . '</p>';
        } else {
            $html .= '
                        <p><strong>Nombre:</strong> Cliente General</p>
                        <p><strong>Tipo:</strong> Venta al público</p>';
        }
        
        $html .= '
                    </div>
                </div>
                
                <!-- Botón de impresión -->
                <div style="text-align: center;">
                    <button class="print-btn" onclick="window.print()">🖨️ Imprimir Factura</button>
                </div>
                
                <!-- Tabla de productos -->
                <table class="productos-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($this->productos as $producto) {
            $html .= '
                        <tr>
                            <td>' . htmlspecialchars($producto['nombre']) . '</td>
                            <td>' . $producto['cantidad'] . '</td>
                            <td>$' . number_format($producto['precio_unitario'], 2) . '</td>
                            <td>$' . number_format($producto['subtotal'], 2) . '</td>
                        </tr>';
        }
        
        $html .= '
                    </tbody>
                </table>
                
                <!-- Totales -->
                <div class="totales">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>$' . number_format($this->subtotal, 2) . '</span>
                    </div>
                    <div class="total-row">
                        <span>IVA (16%):</span>
                        <span>$' . number_format($this->impuesto, 2) . '</span>
                    </div>
                    <div class="total-row total-final">
                        <span>TOTAL:</span>
                        <span>$' . number_format($this->total, 2) . '</span>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="footer">
                    <p>¡Gracias por su compra!</p>
                    <p>Esta factura fue generada electrónicamente el ' . date('d/m/Y \a \l\a\s H:i') . '</p>
                    <p>Para cualquier duda o aclaración, contáctenos a través de nuestros medios oficiales</p>
                </div>
            </div>
            
            <script>
                // Auto-focus para impresión
                window.addEventListener("load", function() {
                    // Opcional: auto-imprimir al cargar
                    // window.print();
                });
            </script>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Guarda la factura en la base de datos
     * @return bool - True si se guardó exitosamente
     */
    public function guardarEnBaseDatos() {
        try {
            // Si ya hay un cliente asignado (logueado), usarlo directamente
            if ($this->idCliente !== null) {
                // Cliente ya establecido, obtener sus datos desde la base de datos
                require_once 'modelos/ClienteModel.php';
                $clienteModel = new ClienteModel();
                $datosClienteExistente = $clienteModel->obtenerPorId($this->idCliente);
                
                if ($datosClienteExistente) {
                    // Usar datos del cliente existente pero permitir actualizaciones desde formulario
                    $datosCliente = [
                        'nombre' => $this->clienteInfo['nombre'] ?? $datosClienteExistente['nombre'],
                        'email' => $this->clienteInfo['email'] ?? $datosClienteExistente['email'],
                        'telefono' => $this->clienteInfo['telefono'] ?? $datosClienteExistente['telefono'],
                        'direccion' => $this->clienteInfo['direccion'] ?? $datosClienteExistente['direccion']
                    ];
                } else {
                    throw new Exception("Cliente logueado no encontrado en la base de datos");
                }
            } else {
                // No hay cliente logueado, crear o buscar cliente basado en email
                $datosCliente = $this->clienteModel->limpiarDatos($this->clienteInfo);
                $erroresValidacion = $this->clienteModel->validarDatos($datosCliente);
                
                if (!empty($erroresValidacion)) {
                    error_log("Error de validación de cliente: " . json_encode($erroresValidacion));
                    // Usar datos por defecto si hay errores de validación
                    $datosCliente = [
                        'nombre' => $this->clienteInfo['nombre'] ?? 'Cliente General',
                        'email' => $this->clienteInfo['email'] ?? 'cliente@general.com',
                        'telefono' => $this->clienteInfo['telefono'] ?? '',
                        'direccion' => $this->clienteInfo['direccion'] ?? ''
                    ];
                }
                
                // Insertar o actualizar cliente y obtener ID
                $this->idCliente = $this->clienteModel->insertarOActualizar($datosCliente);
                
                if (!$this->idCliente) {
                    throw new Exception("No se pudo guardar la información del cliente");
                }
            }
            
            // Preparar datos de la factura
            $datosFactura = [
                'numero_factura' => $this->numeroFactura,
                'fecha_factura' => $this->fecha,
                'id_cliente' => $this->idCliente,
                'cliente_nombre' => $datosCliente['nombre'],
                'cliente_email' => $datosCliente['email'],
                'cliente_telefono' => $datosCliente['telefono'],
                'cliente_direccion' => $datosCliente['direccion'],
                'subtotal' => $this->subtotal,
                'impuesto' => $this->impuesto,
                'total' => $this->total,
                'metodo_pago' => $this->metodoPago,
                'estado' => 'pagada',
                'notas' => 'Factura generada desde sistema web - Cliente registrado automáticamente'
            ];
            
            // Insertar factura
            $this->idFactura = $this->facturaModel->insertarFactura($datosFactura);
            
            if ($this->idFactura) {
                // Insertar detalles de la factura
                $productosParaBD = [];
                foreach ($this->productos as $producto) {
                    $productosParaBD[] = [
                        'id_producto' => $producto['id_producto'],
                        'nombre' => $producto['nombre'],
                        'precio_unitario' => $producto['precio_unitario'],
                        'cantidad' => $producto['cantidad'],
                        'subtotal' => $producto['subtotal']
                    ];
                }
                
                if ($this->facturaModel->insertarDetallesFactura($this->idFactura, $productosParaBD)) {
                    return true;
                } else {
                    error_log("Error al guardar detalles de factura: " . $this->numeroFactura);
                    return false;
                }
            } else {
                error_log("Error al guardar factura en BD: " . $this->numeroFactura);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Error en guardarEnBaseDatos: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene el ID de la factura en la base de datos
     * @return int|null - ID de la factura
     */
    public function getIdFactura() {
        return $this->idFactura;
    }
    
    /**
     * Obtiene el número de factura
     * @return string - Número de factura
     */
    public function getNumeroFactura() {
        return $this->numeroFactura;
    }
    
    /**
     * Obtiene el total de la factura
     * @return float - Total
     */
    public function getTotal() {
        return $this->total;
    }
    
    /**
     * Obtiene todos los datos de la factura
     * @return array - Datos completos de la factura
     */
    public function getDatos() {
        return [
            'numero' => $this->numeroFactura,
            'fecha' => $this->fecha,
            'cliente' => $this->clienteInfo,
            'productos' => $this->productos,
            'subtotal' => $this->subtotal,
            'impuesto' => $this->impuesto,
            'total' => $this->total
        ];
    }
    
    /**
     * Obtiene el ID del cliente
     * @return int|null - ID del cliente
     */
    public function getIdCliente() {
        return $this->idCliente;
    }
    
    /**
     * Establece el ID del cliente (para clientes ya logueados)
     * @param int $idCliente - ID del cliente
     */
    public function setIdCliente($idCliente) {
        $this->idCliente = $idCliente;
    }
    
    /**
     * Obtiene la información completa del cliente
     * @return array|null - Datos del cliente
     */
    public function getClienteInfo() {
        return $this->clienteInfo;
    }
    
    /**
     * Verifica si el cliente fue guardado exitosamente
     * @return bool - True si el cliente existe en BD
     */
    public function clienteGuardado() {
        return $this->idCliente !== null;
    }
}
?>