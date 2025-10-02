<?php
/**
 * Modelo para manejar facturas en la base de datos
 */

class FacturaModel {
    private $conn;

    public function __construct() {
        $this->conn = $this->getConnection();
    }
    
    /**
     * Obtener conexión MySQLi
     * @return mysqli Conexión a la base de datos
     */
    private function getConnection() {
        // Configuración de base de datos
        $host = "localhost";
        $usuario = "root";
        $password = "";
        $baseDatos = "productos_iniciales";
        
        // Crear conexión MySQLi
        $conn = new mysqli($host, $usuario, $password, $baseDatos);
        
        // Verificar conexión
        if ($conn->connect_error) {
            throw new Exception("Error de conexión: " . $conn->connect_error);
        }
        
        // Configurar charset
        $conn->set_charset("utf8mb4");
        
        return $conn;
    }

    /**
     * Inserta una nueva factura en la base de datos
     * @param array $datosFactura - Datos de la factura
     * @return int|false - ID de la factura insertada o false en caso de error
     */
    public function insertarFactura($datosFactura) {
        try {
            $sql = "INSERT INTO facturas (
                numero_factura, 
                fecha_factura, 
                id_cliente,
                cliente_nombre, 
                cliente_email, 
                cliente_telefono, 
                cliente_direccion, 
                subtotal, 
                impuesto, 
                total, 
                metodo_pago, 
                estado,
                notas
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            
            // Verificar si la preparación fue exitosa
            if ($stmt === false) {
                throw new Exception("Error al preparar consulta SQL: " . $this->conn->error);
            }
            
            $stmt->bind_param(
                "ssissssdddsss",
                $datosFactura['numero_factura'],
                $datosFactura['fecha_factura'],
                $datosFactura['id_cliente'],
                $datosFactura['cliente_nombre'],
                $datosFactura['cliente_email'],
                $datosFactura['cliente_telefono'],
                $datosFactura['cliente_direccion'],
                $datosFactura['subtotal'],
                $datosFactura['impuesto'],
                $datosFactura['total'],
                $datosFactura['metodo_pago'],
                $datosFactura['estado'],
                $datosFactura['notas']
            );

            if ($stmt->execute()) {
                return $this->conn->insert_id;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error al insertar factura: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Inserta los detalles de una factura
     * @param int $idFactura - ID de la factura
     * @param array $productos - Array de productos
     * @return bool - True si se insertaron correctamente
     */
    public function insertarDetallesFactura($idFactura, $productos) {
        try {
            $sql = "INSERT INTO detalle_factura (
                id_factura, 
                id_producto, 
                nombre_producto, 
                precio_unitario, 
                cantidad, 
                subtotal
            ) VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            
            // Verificar si la preparación fue exitosa
            if ($stmt === false) {
                throw new Exception("Error al preparar consulta SQL para detalles: " . $this->conn->error);
            }
            
            foreach ($productos as $producto) {
                $stmt->bind_param(
                    "iisdid",
                    $idFactura,
                    $producto['id_producto'],
                    $producto['nombre'],
                    $producto['precio_unitario'],
                    $producto['cantidad'],
                    $producto['subtotal']
                );
                
                if (!$stmt->execute()) {
                    error_log("Error al insertar detalle de factura: " . $stmt->error);
                    return false;
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error al insertar detalles de factura: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene una factura por su ID
     * @param int $idFactura - ID de la factura
     * @return array|false - Datos de la factura o false si no existe
     */
    public function obtenerFacturaPorId($idFactura) {
        try {
            $sql = "SELECT * FROM facturas WHERE id_factura = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $idFactura);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error al obtener factura: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los detalles de una factura
     * @param int $idFactura - ID de la factura
     * @return array - Array de detalles de la factura
     */
    public function obtenerDetallesFactura($idFactura) {
        try {
            $sql = "SELECT * FROM detalle_factura WHERE id_factura = ? ORDER BY id_detalle";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $idFactura);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $detalles = [];
            while ($row = $result->fetch_assoc()) {
                $detalles[] = $row;
            }
            
            return $detalles;
        } catch (Exception $e) {
            error_log("Error al obtener detalles de factura: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene todas las facturas con paginación
     * @param int $limite - Límite de registros
     * @param int $offset - Offset para paginación
     * @return array - Array de facturas
     */
    public function obtenerFacturas($limite = 50, $offset = 0) {
        try {
            $sql = "SELECT * FROM facturas ORDER BY fecha_factura DESC LIMIT ? OFFSET ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $limite, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $facturas = [];
            while ($row = $result->fetch_assoc()) {
                $facturas[] = $row;
            }
            
            return $facturas;
        } catch (Exception $e) {
            error_log("Error al obtener facturas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el total de ventas por período
     * @param string $fechaInicio - Fecha de inicio (Y-m-d)
     * @param string $fechaFin - Fecha de fin (Y-m-d)
     * @return float - Total de ventas
     */
    public function obtenerVentasPorPeriodo($fechaInicio, $fechaFin) {
        try {
            $sql = "SELECT SUM(total) as total_ventas 
                    FROM facturas 
                    WHERE DATE(fecha_factura) BETWEEN ? AND ? 
                    AND estado = 'pagada'";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $fechaInicio, $fechaFin);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return $row['total_ventas'] ?? 0;
        } catch (Exception $e) {
            error_log("Error al obtener ventas por período: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Actualiza el estado de una factura
     * @param int $idFactura - ID de la factura
     * @param string $estado - Nuevo estado
     * @return bool - True si se actualizó correctamente
     */
    public function actualizarEstadoFactura($idFactura, $estado) {
        try {
            $sql = "UPDATE facturas SET estado = ?, updated_at = CURRENT_TIMESTAMP WHERE id_factura = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $estado, $idFactura);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error al actualizar estado de factura: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene estadísticas de ventas
     * @return array - Estadísticas de ventas
     */
    public function obtenerEstadisticasVentas() {
        try {
            $estadisticas = [];
            
            // Ventas del día
            $sql = "SELECT COUNT(*) as facturas_hoy, SUM(total) as total_hoy 
                    FROM facturas 
                    WHERE DATE(fecha_factura) = CURDATE() AND estado = 'pagada'";
            $result = $this->conn->query($sql);
            $estadisticas['hoy'] = $result->fetch_assoc();
            
            // Ventas del mes
            $sql = "SELECT COUNT(*) as facturas_mes, SUM(total) as total_mes 
                    FROM facturas 
                    WHERE YEAR(fecha_factura) = YEAR(CURDATE()) 
                    AND MONTH(fecha_factura) = MONTH(CURDATE()) 
                    AND estado = 'pagada'";
            $result = $this->conn->query($sql);
            $estadisticas['mes'] = $result->fetch_assoc();
            
            // Total de facturas
            $sql = "SELECT COUNT(*) as total_facturas FROM facturas";
            $result = $this->conn->query($sql);
            $estadisticas['total'] = $result->fetch_assoc();
            
            return $estadisticas;
        } catch (Exception $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene facturas con información completa del cliente
     * @param int $limite - Límite de resultados
     * @param int $offset - Offset para paginación
     * @return array - Array de facturas con datos del cliente
     */
    public function obtenerFacturasConCliente($limite = 20, $offset = 0) {
        try {
            $sql = "SELECT f.*, c.nombre as cliente_nombre_completo, c.email as cliente_email_completo
                    FROM facturas f
                    LEFT JOIN clientes c ON f.id_cliente = c.id_cliente
                    ORDER BY f.fecha_factura DESC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $limite, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $facturas = [];
            while ($row = $result->fetch_assoc()) {
                $facturas[] = $row;
            }
            
            return $facturas;
        } catch (Exception $e) {
            error_log("Error al obtener facturas con cliente: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene facturas de un cliente específico
     * @param int $idCliente - ID del cliente
     * @param int $limite - Límite de resultados
     * @return array - Array de facturas del cliente
     */
    public function obtenerFacturasPorCliente($idCliente, $limite = 50) {
        try {
            $sql = "SELECT f.*, COUNT(fd.id_detalle) as total_items
                    FROM facturas f
                    LEFT JOIN detalle_factura fd ON f.id_factura = fd.id_factura
                    WHERE f.id_cliente = ?
                    GROUP BY f.id_factura
                    ORDER BY f.fecha_factura DESC 
                    LIMIT ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $idCliente, $limite);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $facturas = [];
            while ($row = $result->fetch_assoc()) {
                $facturas[] = $row;
            }
            
            return $facturas;
        } catch (Exception $e) {
            error_log("Error al obtener facturas por cliente: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Buscar facturas por datos del cliente
     * @param string $termino - Término de búsqueda (nombre, email, teléfono)
     * @return array - Array de facturas encontradas
     */
    public function buscarFacturasPorCliente($termino) {
        try {
            $termino = "%" . $termino . "%";
            $sql = "SELECT f.*, c.nombre as cliente_nombre_completo, c.email as cliente_email_completo
                    FROM facturas f
                    LEFT JOIN clientes c ON f.id_cliente = c.id_cliente
                    WHERE f.cliente_nombre LIKE ? 
                    OR f.cliente_email LIKE ? 
                    OR f.cliente_telefono LIKE ?
                    OR c.nombre LIKE ?
                    OR c.email LIKE ?
                    ORDER BY f.fecha_factura DESC 
                    LIMIT 30";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssss", $termino, $termino, $termino, $termino, $termino);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $facturas = [];
            while ($row = $result->fetch_assoc()) {
                $facturas[] = $row;
            }
            
            return $facturas;
        } catch (Exception $e) {
            error_log("Error al buscar facturas por cliente: " . $e->getMessage());
            return [];
        }
    }
}
?>