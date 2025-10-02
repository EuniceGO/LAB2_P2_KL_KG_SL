<?php
require_once 'modelos/UsuarioModel.php';
require_once 'modelos/ClienteModel.php';
require_once 'modelos/RoleModel.php';
require_once 'modelos/FacturaModel.php';

class ClienteAuthController {
    private $usuarioModel;
    private $clienteModel;
    
    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->clienteModel = new ClienteModel();
    }
    
    public function mostrarRegistro() {
        include 'vistas/Auth/registro_cliente.php';
    }
    
    public function mostrarLoginCliente() {
        include 'vistas/Auth/login_cliente.php';
    }
    
    public function registrarCliente() {
        $errores = [];
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar datos
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $nombre = trim($_POST['nombre'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            
            // Validaciones
            if (empty($email)) {
                $errores[] = "El email es requerido";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = "El email no es válido";
            }
            
            if (empty($password)) {
                $errores[] = "La contraseña es requerida";
            } elseif (strlen($password) < 6) {
                $errores[] = "La contraseña debe tener al menos 6 caracteres";
            }
            
            if ($password !== $confirm_password) {
                $errores[] = "Las contraseñas no coinciden";
            }
            
            if (empty($nombre)) {
                $errores[] = "El nombre es requerido";
            }
            
            // Verificar si el email ya existe
            if (empty($errores)) {
                $usuarioExistente = $this->usuarioModel->obtenerPorEmail($email);
                if ($usuarioExistente) {
                    $errores[] = "Ya existe una cuenta con este email";
                }
            }
            
            if (empty($errores)) {
                try {
                    // Obtener ID del rol Cliente
                    $roleModel = new RoleModel();
                    $roles = $roleModel->getAll();
                    $roleClienteId = null;
                    
                    foreach ($roles as $role) {
                        if ($role['nombre'] === 'Cliente') {
                            $roleClienteId = $role['id_role'];
                            break;
                        }
                    }
                    
                    if (!$roleClienteId) {
                        throw new Exception("No se encontró el rol Cliente");
                    }
                    
                    // Crear usuario
                    $usuario = [
                        'email' => $email,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'id_role' => $roleClienteId
                    ];
                    
                    $idUsuario = $this->usuarioModel->crear($usuario);
                    
                    if ($idUsuario) {
                        // Crear cliente vinculado al usuario
                        $cliente = [
                            'nombre' => $nombre,
                            'email' => $email,
                            'telefono' => $telefono,
                            'direccion' => $direccion,
                            'id_usuario' => $idUsuario
                        ];
                        
                        $idCliente = $this->clienteModel->crear($cliente);
                        
                        if ($idCliente) {
                            // Iniciar sesión automáticamente
                            session_start();
                            $_SESSION['usuario_id'] = $idUsuario;
                            $_SESSION['usuario_email'] = $email;
                            $_SESSION['usuario_role'] = 'Cliente';
                            $_SESSION['cliente_id'] = $idCliente;
                            
                            header('Location: index.php?c=clienteauth&a=panelCliente&success=registro');
                            exit;
                        } else {
                            throw new Exception("Error al crear el perfil de cliente");
                        }
                    } else {
                        throw new Exception("Error al crear la cuenta de usuario");
                    }
                    
                } catch (Exception $e) {
                    $errores[] = "Error en el registro: " . $e->getMessage();
                }
            }
        }
        
        if (!empty($errores)) {
            include 'vistas/Auth/registro_cliente.php';
        }
    }
    
    public function loginCliente() {
        $errores = [];
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($email)) {
                $errores[] = "El email es requerido";
            }
            
            if (empty($password)) {
                $errores[] = "La contraseña es requerida";
            }
            
            if (empty($errores)) {
                $usuario = $this->usuarioModel->obtenerPorEmail($email);
                
                if ($usuario && password_verify($password, $usuario['password'])) {
                    // Verificar que sea un cliente
                    $roleModel = new RoleModel();
                    $role = $roleModel->getById($usuario['id_role']);
                    
                    if ($role && $role['nombre'] === 'Cliente') {
                        // Obtener datos del cliente
                        $cliente = $this->clienteModel->obtenerPorUsuario($usuario['id_usuario']);
                        
                        if ($cliente) {
                            session_start();
                            $_SESSION['usuario_id'] = $usuario['id_usuario'];
                            $_SESSION['usuario_email'] = $usuario['email'];
                            $_SESSION['usuario_role'] = 'Cliente';
                            $_SESSION['cliente_id'] = $cliente['id_cliente'];
                            
                            header('Location: index.php?c=clienteauth&a=panelCliente&success=login');
                            exit;
                        } else {
                            $errores[] = "No se encontró el perfil de cliente asociado";
                        }
                    } else {
                        $errores[] = "Esta cuenta no es de cliente";
                    }
                } else {
                    $errores[] = "Email o contraseña incorrectos";
                }
            }
        }
        
        include 'vistas/Auth/login_cliente.php';
    }
    
    public function panelCliente() {
        session_start();
        
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_role'] !== 'Cliente') {
            header('Location: index.php?c=clienteauth&a=mostrarLoginCliente');
            exit;
        }
        
        // Obtener datos del cliente
        $clienteId = $_SESSION['cliente_id'];
        $cliente = $this->clienteModel->obtenerPorId($clienteId);
        
        // Obtener facturas del cliente
        $facturaModel = new FacturaModel();
        $facturas = $facturaModel->obtenerPorCliente($clienteId);
        
        include 'vistas/Auth/panel_cliente.php';
    }
    
    public function logout() {
        session_start();
        session_destroy();
        header('Location: index.php?success=logout');
        exit;
    }
    
    public function verFactura() {
        session_start();
        
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_role'] !== 'Cliente') {
            header('Location: index.php?c=clienteauth&a=mostrarLoginCliente');
            exit;
        }
        
        $facturaId = $_GET['id'] ?? 0;
        $clienteId = $_SESSION['cliente_id'];
        
        $facturaModel = new FacturaModel();
        $factura = $facturaModel->obtenerPorId($facturaId);
        
        // Verificar que la factura pertenece al cliente
        if (!$factura || $factura['id_cliente'] != $clienteId) {
            header('Location: index.php?c=clienteauth&a=panelCliente&error=factura_no_encontrada');
            exit;
        }
        
        // Obtener detalles de la factura
        $detalles = $facturaModel->obtenerDetallesPorFactura($facturaId);
        
        include 'vistas/Auth/ver_factura_cliente.php';
    }
}
?>