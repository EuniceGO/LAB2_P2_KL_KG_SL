<?php include 'layout/menu.php'; ?>

<div class="container mt-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">
            <i class="fas fa-users"></i> Lista de Usuarios
        </h1>
        <a href="?controller=usuario&action=create" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </a>
    </div>

    <!-- Sección de Reportes -->
    <div class="d-flex justify-content-start mb-4">
        <a href="?controller=usuario&action=verReportes" class="btn btn-info">
            <i class="fas fa-chart-bar"></i> Ver Reportes
        </a>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php
            switch ($_GET['success']) {
                case 'created': echo 'Usuario creado exitosamente'; break;
                case 'updated': echo 'Usuario actualizado exitosamente'; break;
                case 'deleted': echo 'Usuario eliminado exitosamente'; break;
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php
            switch ($_GET['error']) {
                case 'notfound': echo 'Usuario no encontrado'; break;
                case 'deleteerror': echo 'Error al eliminar el usuario'; break;
                case 'cannotdeleteyourself': echo 'No puedes eliminar tu propia cuenta'; break;
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Búsqueda -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" class="d-flex">
                <input type="hidden" name="controller" value="usuario">
                <input type="hidden" name="action" value="index">
                <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar por nombre, email o rol..." 
                       value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </form>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-table"></i> Usuarios Registrados</h5>
        </div>
        <div class="card-body">
            <?php if (empty($usuarios)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> No hay usuarios registrados.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo $usuario->getIdUsuario(); ?></td>
                                    <td><?php echo htmlspecialchars($usuario->getNombre()); ?></td>
                                    <td><?php echo htmlspecialchars($usuario->getEmail()); ?></td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?php echo isset($rolesArray[$usuario->getIdRol()]) ? $rolesArray[$usuario->getIdRol()] : 'Sin rol'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="?controller=usuario&action=edit&id=<?php echo $usuario->getIdUsuario(); ?>" 
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <?php if ($_SESSION['user_id'] != $usuario->getIdUsuario()): ?>
                                                <a href="?controller=usuario&action=delete&id=<?php echo $usuario->getIdUsuario(); ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
