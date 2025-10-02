<?php
require_once 'modelos/RoleModel.php';
require_once 'clases/Role.php';

class RoleController {
    private $roleModel;

    public function __construct() {
        $this->roleModel = new RoleModel();
    }

    // Mostrar todos los roles
    public function index() {
        $buscar = isset($_GET['buscar']) ? $_GET['buscar'] : null;
        if ($buscar) {
            $roles = $this->roleModel->search($buscar);
        } else {
            $roles = $this->roleModel->getAll();
        }
        include 'vistas/Roles/index.php';
    }

    // Mostrar formulario para crear nuevo rol
    public function create() {
        include 'vistas/Roles/create.php';
    }

    // Guardar nuevo rol
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $role = new Role(
                null,
                $_POST['nombre'],
                $_POST['descripcion']
            );
            
            // Verificar si el nombre del rol ya existe
            if ($this->roleModel->getByNombre($_POST['nombre'])) {
                $error = "Ya existe un rol con ese nombre";
                include 'vistas/Roles/create.php';
                return;
            }
            
            if ($this->roleModel->insert($role)) {
                header('Location: ?controller=role&action=index&success=created');
                exit;
            } else {
                $error = "Error al crear el rol";
                include 'vistas/Roles/create.php';
            }
        }
    }

    // Mostrar formulario para editar rol
    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $role = $this->roleModel->getById($id);
            if ($role) {
                include 'vistas/Roles/edit.php';
            } else {
                header('Location: ?controller=role&action=index&error=notfound');
                exit;
            }
        } else {
            header('Location: ?controller=role&action=index');
            exit;
        }
    }

    // Actualizar rol
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id_rol'];
            $role = new Role(
                $id,
                $_POST['nombre'],
                $_POST['descripcion']
            );
            
            // Verificar si el nombre del rol ya existe (excluyendo el rol actual)
            $roleExistente = $this->roleModel->getByNombre($_POST['nombre']);
            if ($roleExistente && $roleExistente->getIdRol() != $id) {
                $error = "Ya existe un rol con ese nombre";
                include 'vistas/Roles/edit.php';
                return;
            }
            
            if ($this->roleModel->update($role)) {
                header('Location: ?controller=role&action=index&success=updated');
                exit;
            } else {
                $error = "Error al actualizar el rol";
                include 'vistas/Roles/edit.php';
            }
        }
    }

    // Eliminar rol
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            // Verificar si el rol puede ser eliminado
            if (!$this->roleModel->canDelete($id)) {
                header('Location: ?controller=role&action=index&error=cannotdelete');
                exit;
            }
            
            if ($this->roleModel->delete($id)) {
                header('Location: ?controller=role&action=index&success=deleted');
                exit;
            } else {
                header('Location: ?controller=role&action=index&error=deleteerror');
                exit;
            }
        } else {
            header('Location: ?controller=role&action=index');
            exit;
        }
    }

    // Ver detalles del rol
    public function show() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $role = $this->roleModel->getById($id);
            if ($role) {
                // También podemos mostrar los usuarios que tienen este rol
                require_once 'modelos/UsuarioModel.php';
                $usuarioModel = new UsuarioModel();
                $usuarios = $usuarioModel->getByRole($id);
                include 'vistas/Roles/show.php';
            } else {
                header('Location: ?controller=role&action=index&error=notfound');
                exit;
            }
        } else {
            header('Location: ?controller=role&action=index');
            exit;
        }
    }
}
?>