<?php
require_once 'modelos/UsuarioModel.php';
require_once 'modelos/RoleModel.php';
require_once 'clases/Usuario.php';

class UsuarioController {
    private $usuarioModel;
    private $roleModel;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->roleModel = new RoleModel();
    }

    // Iniciar sesión si no existe
    private function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Mostrar todos los usuarios
    public function index() {
        $this->startSession();
        $this->checkAuthentication();
        
        $buscar = isset($_GET['buscar']) ? $_GET['buscar'] : null;
        if ($buscar) {
            $usuarios = $this->usuarioModel->search($buscar);
        } else {
            $usuarios = $this->usuarioModel->getAll();
        }
        
        // Obtener roles para mostrar nombres en lugar de IDs
        $roles = $this->roleModel->getAll();
        $rolesArray = [];
        foreach ($roles as $rol) {
            $rolesArray[$rol->getIdRol()] = $rol->getNombre();
        }
        
        include 'vistas/Usuarios/index.php';
    }

    // Mostrar formulario para crear nuevo usuario
    public function create() {
        $this->startSession();
        $this->checkAuthentication();
        
        $roles = $this->roleModel->getAll();
        include 'vistas/Usuarios/create.php';
    }

    // Guardar nuevo usuario
    public function store() {
        $this->startSession();
        $this->checkAuthentication();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verificar si el email ya existe
            if ($this->usuarioModel->emailExists($_POST['email'])) {
                $error = "Ya existe un usuario con ese email";
                $roles = $this->roleModel->getAll();
                include 'vistas/Usuarios/create.php';
                return;
            }
            
            $usuario = new Usuario(
                null,
                $_POST['nombre'],
                $_POST['email'],
                $_POST['password'],
                $_POST['id_rol']
            );
            
            if ($this->usuarioModel->insert($usuario)) {
                header('Location: ?controller=usuario&action=index&success=created');
                exit;
            } else {
                $error = "Error al crear el usuario";
                $roles = $this->roleModel->getAll();
                include 'vistas/Usuarios/create.php';
            }
        }
    }

    // Mostrar formulario para editar usuario
    public function edit() {
        $this->startSession();
        $this->checkAuthentication();
        
        $id = $_GET['id'] ?? null;
        if ($id) {
            $usuario = $this->usuarioModel->getById($id);
            if ($usuario) {
                $roles = $this->roleModel->getAll();
                include 'vistas/Usuarios/edit.php';
            } else {
                header('Location: ?controller=usuario&action=index&error=notfound');
                exit;
            }
        } else {
            header('Location: ?controller=usuario&action=index');
            exit;
        }
    }

    // Actualizar usuario
    public function update() {
        $this->startSession();
        $this->checkAuthentication();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id_usuario'];
            
            // Verificar si el email ya existe (excluyendo el usuario actual)
            if ($this->usuarioModel->emailExists($_POST['email'], $id)) {
                $error = "Ya existe un usuario con ese email";
                $usuario = $this->usuarioModel->getById($id);
                $roles = $this->roleModel->getAll();
                include 'vistas/Usuarios/edit.php';
                return;
            }
            
            $usuario = new Usuario(
                $id,
                $_POST['nombre'],
                $_POST['email'],
                null, // No actualizamos la contraseña aquí
                $_POST['id_rol']
            );
            
            if ($this->usuarioModel->update($usuario)) {
                header('Location: ?controller=usuario&action=index&success=updated');
                exit;
            } else {
                $error = "Error al actualizar el usuario";
                $roles = $this->roleModel->getAll();
                include 'vistas/Usuarios/edit.php';
            }
        }
    }

    // Eliminar usuario
    public function delete() {
        $this->startSession();
        $this->checkAuthentication();
        
        $id = $_GET['id'] ?? null;
        if ($id) {
            // No permitir que el usuario se elimine a sí mismo
            if ($_SESSION['user_id'] == $id) {
                header('Location: ?controller=usuario&action=index&error=cannotdeleteyourself');
                exit;
            }
            
            if ($this->usuarioModel->delete($id)) {
                header('Location: ?controller=usuario&action=index&success=deleted');
                exit;
            } else {
                header('Location: ?controller=usuario&action=index&error=deleteerror');
                exit;
            }
        } else {
            header('Location: ?controller=usuario&action=index');
            exit;
        }
    }

    // Mostrar formulario de login
    public function login() {
        $this->startSession();
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: ?controller=usuario&action=dashboard');
            exit;
        }
        include 'vistas/Usuarios/login.php';
    }

    // Procesar autenticación
    public function authenticate() {
        $this->startSession();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $result = $this->usuarioModel->authenticate($email, $password);
            
            if ($result) {
                // Login exitoso
                $_SESSION['user_id'] = $result['usuario']->getIdUsuario();
                $_SESSION['user_name'] = $result['usuario']->getNombre();
                $_SESSION['user_email'] = $result['usuario']->getEmail();
                $_SESSION['user_role'] = $result['rol'];
                $_SESSION['user_role_id'] = $result['usuario']->getIdRol();
                
                header('Location: ?controller=usuario&action=dashboard');
                exit;
            } else {
                // Login fallido
                $error = "Email o contraseña incorrectos";
                include 'vistas/Usuarios/login.php';
            }
        }
    }

    // Dashboard del usuario
    public function dashboard() {
        $this->startSession();
        $this->checkAuthentication();
        
        // Obtener estadísticas
        $totalUsuarios = $this->usuarioModel->getTotalUsuarios();
        $usuariosPorRol = $this->usuarioModel->getUsuariosPorRol();
        
        include 'vistas/Usuarios/dashboard.php';
    }

    // Cerrar sesión
    public function logout() {
        $this->startSession();
        session_destroy();
        header('Location: ?controller=usuario&action=login');
        exit;
    }

    // Verificar autenticación
    private function checkAuthentication() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?controller=usuario&action=login');
            exit;
        }
    }

    // Cambiar contraseña
    public function changePassword() {
        $this->startSession();
        $this->checkAuthentication();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Verificar que las nuevas contraseñas coincidan
            if ($newPassword !== $confirmPassword) {
                $error = "Las nuevas contraseñas no coinciden";
                include 'vistas/Usuarios/change_password.php';
                return;
            }
            
            // Verificar contraseña actual
            $usuario = $this->usuarioModel->getById($_SESSION['user_id']);
            if ($currentPassword !== $usuario->getPassword()) {
                $error = "La contraseña actual es incorrecta";
                include 'vistas/Usuarios/change_password.php';
                return;
            }
            
            // Actualizar contraseña
            if ($this->usuarioModel->updatePassword($_SESSION['user_id'], $newPassword)) {
                $success = "Contraseña actualizada correctamente";
                include 'vistas/Usuarios/change_password.php';
            } else {
                $error = "Error al actualizar la contraseña";
                include 'vistas/Usuarios/change_password.php';
            }
        } else {
            include 'vistas/Usuarios/change_password.php';
        }
    }

    // Perfil del usuario
    public function profile() {
        $this->startSession();
        $this->checkAuthentication();
        
        $usuario = $this->usuarioModel->getById($_SESSION['user_id']);
        $rol = $this->roleModel->getById($usuario->getIdRol());
        
        include 'vistas/Usuarios/profile.php';
    }
}
?>