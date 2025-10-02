<?php include 'layout/menu.php'; ?>

<div class="container mt-4">
    <!-- Encabezado del Dashboard -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="text-primary">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </h1>
            <p class="text-muted">Bienvenido, <?php echo $_SESSION['user_name']; ?> (<?php echo $_SESSION['user_role']; ?>)</p>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $totalUsuarios; ?></h4>
                            <span>Total Usuarios</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo count($usuariosPorRol); ?></h4>
                            <span>Roles Activos</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-tag fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo date('H:i'); ?></h4>
                            <span>Hora Actual</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo date('d/m/Y'); ?></h4>
                            <span>Fecha</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfica de Usuarios por Rol -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-pie"></i> Usuarios por Rol</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Rol</th>
                                <th>Cantidad</th>
                                <th>Porcentaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuariosPorRol as $rolStat): ?>
                                <?php $porcentaje = $totalUsuarios > 0 ? round(($rolStat['total_usuarios'] / $totalUsuarios) * 100, 1) : 0; ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rolStat['rol']); ?></td>
                                    <td><?php echo $rolStat['total_usuarios']; ?></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?php echo $porcentaje; ?>%">
                                                <?php echo $porcentaje; ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-cogs"></i> Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="?controller=usuario&action=index" class="btn btn-outline-primary">
                            <i class="fas fa-users"></i> Gestionar Usuarios
                        </a>
                        <a href="?controller=role&action=index" class="btn btn-outline-success">
                            <i class="fas fa-user-tag"></i> Gestionar Roles
                        </a>
                        <a href="?controller=usuario&action=profile" class="btn btn-outline-info">
                            <i class="fas fa-user-edit"></i> Mi Perfil
                        </a>
                        <a href="?controller=usuario&action=changePassword" class="btn btn-outline-warning">
                            <i class="fas fa-key"></i> Cambiar Contraseña
                        </a>
                        <a href="?c=producto&a=index" class="btn btn-outline-secondary">
                            <i class="fas fa-box"></i> Ver Productos
                        </a>
                        
                        <!-- Nuevo botón para Ver Reportes -->
                        <a href="?controller=usuario&action=verReportes" class="btn btn-outline-danger">
                            <i class="fas fa-chart-bar"></i> Ver Reportes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
