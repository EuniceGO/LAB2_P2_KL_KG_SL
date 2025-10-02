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
                            <h4><?php echo $totalProductos ?? 0; ?></h4>
                            <span>Total Productos</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box fa-2x"></i>
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
                            <h4><?php echo $estadisticasFacturas['total']['total_facturas'] ?? 0; ?></h4>
                            <span>Total Facturas</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-receipt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda fila de estadísticas - Ventas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $estadisticasFacturas['hoy']['facturas_hoy'] ?? 0; ?></h4>
                            <span>Ventas Hoy</span>
                            <br><small>$<?php echo number_format($estadisticasFacturas['hoy']['total_hoy'] ?? 0, 2); ?></small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $estadisticasFacturas['mes']['facturas_mes'] ?? 0; ?></h4>
                            <span>Ventas Este Mes</span>
                            <br><small>$<?php echo number_format($estadisticasFacturas['mes']['total_mes'] ?? 0, 2); ?></small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $totalCategorias ?? 0; ?></h4>
                            <span>Categorías</span>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tags fa-2x"></i>
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
                    <h5><i class="fas fa-receipt"></i> Últimas Facturas</h5>
                </div>
                <div class="card-body">
                    <?php 
                    // Obtener las últimas 5 facturas
                    $ultimasFacturas = $facturaModel->obtenerFacturas(5, 0);
                    ?>
                    
                    <?php if (empty($ultimasFacturas)): ?>
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-receipt fa-2x mb-2"></i>
                            <p>No hay facturas registradas</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Número</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ultimasFacturas as $factura): ?>
                                        <tr>
                                            <td>
                                                <small class="text-primary">
                                                    <?php echo htmlspecialchars($factura['numero_factura']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small>
                                                    <?php echo htmlspecialchars($factura['cliente_nombre'] ?? 'N/A'); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-success fw-bold">
                                                    $<?php echo number_format($factura['total'] ?? 0, 2); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('d/m H:i', strtotime($factura['fecha_factura'])); ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="?c=carrito&a=historial" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-list"></i> Ver todas las facturas
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Acciones Rápidas -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-cogs"></i> Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="?controller=usuario&action=index" class="btn btn-outline-primary w-100">
                                <i class="fas fa-users"></i> Gestionar Usuarios
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="?controller=role&action=index" class="btn btn-outline-success w-100">
                                <i class="fas fa-user-tag"></i> Gestionar Roles
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="?c=producto&a=index" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-box"></i> Ver Productos
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="?c=carrito&a=historial" class="btn btn-outline-success w-100">
                                <i class="fas fa-receipt"></i> Ver Facturas/Compras
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="?controller=usuario&action=verReportes" class="btn btn-outline-danger w-100">
                                <i class="fas fa-chart-bar"></i> Ver Reportes
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="?controller=usuario&action=profile" class="btn btn-outline-info w-100">
                                <i class="fas fa-user-edit"></i> Mi Perfil
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="?controller=usuario&action=changePassword" class="btn btn-outline-warning w-100">
                                <i class="fas fa-key"></i> Cambiar Contraseña
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="?c=categoria&a=index" class="btn btn-outline-info w-100">
                                <i class="fas fa-tags"></i> Categorías
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
