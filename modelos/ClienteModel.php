<?php
/**
 * Modelo para la gestión de clientes
 * Maneja todas las operaciones de base de datos relacionadas con clientes
 */

class ClienteModel {
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
     * Buscar cliente por email
     * @param string $email Email del cliente
     * @return array|null Datos del cliente o null si no existe
     */
    public function buscarPorEmail($email) {
        $sql = "SELECT * FROM clientes WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Buscar cliente por ID
     * @param int $id ID del cliente
     * @return array|null Datos del cliente o null si no existe
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM clientes WHERE id_cliente = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Insertar nuevo cliente o actualizar existente
     * @param array $datosCliente Datos del cliente (nombre, email, telefono, direccion)
     * @return int ID del cliente (nuevo o existente)
     */
    public function insertarOActualizar($datosCliente) {
        // Verificar si el cliente ya existe por email
        $clienteExistente = $this->buscarPorEmail($datosCliente['email']);
        
        if ($clienteExistente) {
            // Actualizar datos del cliente existente
            return $this->actualizar($clienteExistente['id_cliente'], $datosCliente);
        } else {
            // Insertar nuevo cliente
            return $this->insertar($datosCliente);
        }
    }
    
    /**
     * Insertar nuevo cliente
     * @param array $datosCliente Datos del cliente
     * @return int ID del cliente insertado
     */
    private function insertar($datosCliente) {
        $sql = "INSERT INTO clientes (nombre, email, telefono, direccion) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", 
            $datosCliente['nombre'],
            $datosCliente['email'],
            $datosCliente['telefono'],
            $datosCliente['direccion']
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        } else {
            throw new Exception("Error al insertar cliente: " . $this->conn->error);
        }
    }
    
    /**
     * Actualizar datos de cliente existente
     * @param int $idCliente ID del cliente
     * @param array $datosCliente Nuevos datos del cliente
     * @return int ID del cliente actualizado
     */
    private function actualizar($idCliente, $datosCliente) {
        $sql = "UPDATE clientes SET 
                nombre = ?, 
                telefono = ?, 
                direccion = ?
                WHERE id_cliente = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", 
            $datosCliente['nombre'],
            $datosCliente['telefono'],
            $datosCliente['direccion'],
            $idCliente
        );
        
        if ($stmt->execute()) {
            return $idCliente;
        } else {
            throw new Exception("Error al actualizar cliente: " . $this->conn->error);
        }
    }
    
    /**
     * Obtener todos los clientes con paginación
     * @param int $limite Número de clientes por página
     * @param int $offset Offset para la paginación
     * @return array Lista de clientes
     */
    public function obtenerTodos($limite = 50, $offset = 0) {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM facturas WHERE facturas.id_cliente = c.id_cliente) as total_facturas,
                (SELECT SUM(total) FROM facturas WHERE facturas.id_cliente = c.id_cliente) as total_compras
                FROM clientes c
                ORDER BY c.id_cliente DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limite, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $clientes = [];
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
        
        return $clientes;
    }
    
    /**
     * Contar total de clientes
     * @return int Número total de clientes
     */
    public function contarTotal() {
        $sql = "SELECT COUNT(*) as total FROM clientes";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    /**
     * Buscar clientes por nombre o email
     * @param string $termino Término de búsqueda
     * @return array Lista de clientes que coinciden
     */
    public function buscar($termino) {
        $termino = "%" . $termino . "%";
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM facturas WHERE facturas.id_cliente = c.id_cliente) as total_facturas,
                (SELECT SUM(total) FROM facturas WHERE facturas.id_cliente = c.id_cliente) as total_compras
                FROM clientes c
                WHERE c.nombre LIKE ? OR c.email LIKE ?
                ORDER BY c.id_cliente DESC 
                LIMIT 20";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $termino, $termino);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $clientes = [];
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
        
        return $clientes;
    }
    
    /**
     * Obtener historial de compras de un cliente
     * @param int $idCliente ID del cliente
     * @return array Lista de facturas del cliente
     */
    public function obtenerHistorialCompras($idCliente) {
        $sql = "SELECT f.*, 
                COUNT(df.id_detalle) as total_items
                FROM facturas f
                LEFT JOIN detalle_factura df ON f.id_factura = df.id_factura
                WHERE f.id_cliente = ?
                GROUP BY f.id_factura
                ORDER BY f.fecha DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idCliente);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $facturas = [];
        while ($row = $result->fetch_assoc()) {
            $facturas[] = $row;
        }
        
        return $facturas;
    }
    
    /**
     * Validar datos de cliente
     * @param array $datos Datos a validar
     * @return array Array con errores (vacío si todo está bien)
     */
    public function validarDatos($datos) {
        $errores = [];
        
        // Validar nombre
        if (empty($datos['nombre']) || strlen(trim($datos['nombre'])) < 2) {
            $errores['nombre'] = 'El nombre debe tener al menos 2 caracteres';
        }
        
        // Validar email
        if (empty($datos['email']) || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'Debe proporcionar un email válido';
        }
        
        // Validar teléfono (opcional pero si se proporciona debe ser válido)
        if (!empty($datos['telefono']) && !preg_match('/^[0-9+\-\s()]+$/', $datos['telefono'])) {
            $errores['telefono'] = 'El teléfono contiene caracteres no válidos';
        }
        
        return $errores;
    }
    
    /**
     * Limpiar y preparar datos de cliente
     * @param array $datos Datos sin limpiar
     * @return array Datos limpiados
     */
    public function limpiarDatos($datos) {
        return [
            'nombre' => trim($datos['nombre'] ?? ''),
            'email' => trim(strtolower($datos['email'] ?? '')),
            'telefono' => trim($datos['telefono'] ?? ''),
            'direccion' => trim($datos['direccion'] ?? '')
        ];
    }
    
    /**
     * Métodos adicionales para sistema de autenticación de clientes
     */
    
    /**
     * Crear nuevo cliente con ID de usuario
     * @param array $datosCliente Datos del cliente incluyendo id_usuario
     * @return int ID del cliente creado
     */
    public function crear($datosCliente) {
        // Primero verificar si la tabla tiene la columna id_usuario
        $checkColumn = "SHOW COLUMNS FROM clientes LIKE 'id_usuario'";
        $result = $this->conn->query($checkColumn);
        
        if ($result && $result->num_rows > 0) {
            // La columna existe, usar consulta con id_usuario
            $sql = "INSERT INTO clientes (nombre, email, telefono, direccion, id_usuario) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->conn->error);
            }
            
            // Preparar variables para bind_param
            $telefono = $datosCliente['telefono'] ?? '';
            $direccion = $datosCliente['direccion'] ?? '';
            
            $stmt->bind_param("ssssi", 
                $datosCliente['nombre'],
                $datosCliente['email'],
                $telefono,
                $direccion,
                $datosCliente['id_usuario']
            );
        } else {
            // La columna no existe, usar consulta sin id_usuario
            $sql = "INSERT INTO clientes (nombre, email, telefono, direccion) 
                    VALUES (?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->conn->error);
            }
            
            // Preparar variables para bind_param
            $telefono = $datosCliente['telefono'] ?? '';
            $direccion = $datosCliente['direccion'] ?? '';
            
            $stmt->bind_param("ssss", 
                $datosCliente['nombre'],
                $datosCliente['email'],
                $telefono,
                $direccion
            );
        }
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        } else {
            throw new Exception("Error al crear cliente: " . $this->conn->error);
        }
    }
    
    /**
     * Obtener cliente por ID de usuario
     * @param int $idUsuario ID del usuario
     * @return array|null Datos del cliente o null si no existe
     */
    public function obtenerPorUsuario($idUsuario) {
        // Verificar si la columna id_usuario existe
        $checkColumn = "SHOW COLUMNS FROM clientes LIKE 'id_usuario'";
        $result = $this->conn->query($checkColumn);
        
        if ($result && $result->num_rows > 0) {
            // La columna existe, buscar por id_usuario
            $sql = "SELECT * FROM clientes WHERE id_usuario = ? LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
        }
        
        // Si no existe la columna o no se encuentra el cliente, retornar null
        return null;
    }
    
    /**
     * Obtener cliente por ID (alias para buscarPorId)
     * @param int $id ID del cliente
     * @return array|null Datos del cliente o null si no existe
     */
    public function obtenerPorId($id) {
        return $this->buscarPorId($id);
    }
}
?>