<?php
require_once 'layout/menu.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header del Dashboard Cliente -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user-circle"></i> Panel de Cliente
                    </h1>
                    <p class="text-muted">Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                </div>
                <div class="text-end">
                    <small class="text-muted">
                        <i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i'); ?>
                    </small>
                </div>
            </div>

            <?php if (isset($_GET['success']) && $_GET['success'] === 'login'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> ¡Bienvenido! Has iniciado sesión correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Estadísticas del Cliente -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total de Compras
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $totalFacturas; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Gastado
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        $<?php echo number_format($totalGastado, 2); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Promedio por Compra
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        $<?php echo $totalFacturas > 0 ? number_format($totalGastado / $totalFacturas, 2) : '0.00'; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calculator fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Estado
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Cliente Activo
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de Facturas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-receipt"></i> Mis Compras
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <div class="dropdown-header">Opciones:</div>
                            <a class="dropdown-item" href="#" onclick="window.print()">
                                <i class="fas fa-print fa-sm fa-fw mr-2 text-gray-400"></i>
                                Imprimir
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($misFacturas)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID Factura</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($misFacturas as $factura): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?php echo $factura['id_factura']; ?></strong>
                                            </td>
                                            <td>
                                                <?php echo date('d/m/Y H:i', strtotime($factura['fecha_factura'])); ?>
                                            </td>
                                            <td>
                                                <span class="text-success font-weight-bold">
                                                    $<?php echo number_format($factura['total'], 2); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-success">Pagada</span>
                                            </td>
                                            <td>
                                                <a href="?controller=factura&action=verFactura&id=<?php echo $factura['id_factura']; ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Ver Detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="?controller=factura&action=descargarPDF&id=<?php echo $factura['id_factura']; ?>" 
                                                   class="btn btn-sm btn-outline-secondary" title="Descargar PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-500">No tienes compras registradas</h5>
                            <p class="text-muted">Cuando realices tu primera compra, aparecerá aquí.</p>
                            <a href="?controller=categoria&action=index" class="btn btn-primary">
                                <i class="fas fa-shopping-bag"></i> Ir a Comprar
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layout/footer.php'; ?>