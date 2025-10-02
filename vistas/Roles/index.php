<?php include 'layout/menu.php'; ?>

<div class="container mt-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">
            <i class="fas fa-user-tag"></i> Lista de Roles
        </h1>
        <a href="?controller=role&action=create" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo Rol
        </a>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php
            switch ($_GET['success']) {
                case 'created': echo 'Rol creado exitosamente'; break;
                case 'updated': echo 'Rol actualizado exitosamente'; break;
                case 'deleted': echo 'Rol eliminado exitosamente'; break;
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php
            switch ($_GET['error']) {
                case 'notfound': echo 'Rol no encontrado'; break;
                case 'deleteerror': echo 'Error al eliminar el rol'; break;
                case 'cannotdelete': echo 'No se puede eliminar el rol porque tiene usuarios asignados'; break;
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Búsqueda -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" class="d-flex">
                <input type="hidden" name="controller" value="role">
                <input type="hidden" name="action" value="index">
                <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar por nombre o descripción..." 
                       value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </form>
        </div>
    </div>

    <!-- Tabla de roles -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-table"></i> Roles del Sistema</h5>
        </div>
        <div class="card-body">
            <?php if (empty($roles)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> No hay roles registrados.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roles as $role): ?>
                                <tr>
                                    <td><?php echo $role->getIdRol(); ?></td>
                                    <td>
                                        <span class="badge bg-primary fs-6">
                                            <?php echo htmlspecialchars($role->getNombre()); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($role->getDescripcion()); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="?controller=role&action=show&id=<?php echo $role->getIdRol(); ?>" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                            <a href="?controller=role&action=edit&id=<?php echo $role->getIdRol(); ?>" 
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a href="?controller=role&action=delete&id=<?php echo $role->getIdRol(); ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('¿Estás seguro de que quieres eliminar este rol?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </a>
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