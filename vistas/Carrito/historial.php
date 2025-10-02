<?php 
require_once 'modelos/FacturaModel.php';
$facturaModel = new FacturaModel();

// Obtener parámetros de paginación
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$limite = 20;
$offset = ($pagina - 1) * $limite;

// Obtener facturas
$facturas = $facturaModel->obtenerFacturas($limite, $offset);
$estadisticas = $facturaModel->obtenerEstadisticasVentas();

include 'layout/menu.php'; 
?>

<div class="container mt-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">
            <i class="fas fa-receipt"></i> Historial de Facturas
        </h1>
        <div class="text-end">
            <small class="text-muted">Total de facturas: <?php echo $estadisticas['total']['total_facturas'] ?? 0; ?></small>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">Hoy</h4>
                            <p class="card-text">
                                <?php echo $estadisticas['hoy']['facturas_hoy'] ?? 0; ?> facturas<br>
                                <strong>$<?php echo number_format($estadisticas['hoy']['total_hoy'] ?? 0, 2); ?></strong>
                            </p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">Este Mes</h4>
                            <p class="card-text">
                                <?php echo $estadisticas['mes']['facturas_mes'] ?? 0; ?> facturas<br>
                                <strong>$<?php echo number_format($estadisticas['mes']['total_mes'] ?? 0, 2); ?></strong>
                            </p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">Total</h4>
                            <p class="card-text">
                                <?php echo $estadisticas['total']['total_facturas'] ?? 0; ?> facturas<br>
                                <strong>Historial completo</strong>
                            </p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-bar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de facturas -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Facturas Recientes</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($facturas)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay facturas registradas</h5>
                    <p class="text-muted">Las facturas aparecerán aquí cuando se realicen compras</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Número</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Método Pago</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($facturas as $factura): ?>
                                <tr>
                                    <td>
                                        <strong class="text-primary"><?php echo htmlspecialchars($factura['numero_factura']); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y H:i', strtotime($factura['fecha_factura'])); ?>
                                        <br><small class="text-muted"><?php echo date('D', strtotime($factura['fecha_factura'])); ?></small>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($factura['cliente_nombre']); ?>
                                        <?php if ($factura['cliente_email']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($factura['cliente_email']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong class="text-success">$<?php echo number_format($factura['total'], 2); ?></strong>
                                        <br><small class="text-muted">
                                            Sub: $<?php echo number_format($factura['subtotal'], 2); ?> + 
                                            IVA: $<?php echo number_format($factura['impuesto'], 2); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $iconos = [
                                            'efectivo' => 'fas fa-money-bill-wave text-success',
                                            'tarjeta' => 'fas fa-credit-card text-primary',
                                            'transferencia' => 'fas fa-university text-info'
                                        ];
                                        $icon = $iconos[$factura['metodo_pago']] ?? 'fas fa-question';
                                        ?>
                                        <i class="<?php echo $icon; ?>"></i>
                                        <?php echo ucfirst($factura['metodo_pago']); ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = [
                                            'pagada' => 'success',
                                            'pendiente' => 'warning',
                                            'cancelada' => 'danger'
                                        ];
                                        $class = $badgeClass[$factura['estado']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $class; ?>">
                                            <?php echo ucfirst($factura['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="?c=carrito&a=verFactura&id=<?php echo $factura['id_factura']; ?>" 
                                               class="btn btn-outline-primary" title="Ver factura">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="?c=carrito&a=imprimirFactura&id=<?php echo $factura['id_factura']; ?>" 
                                               class="btn btn-outline-secondary" title="Imprimir" target="_blank">
                                                <i class="fas fa-print"></i>
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

    <!-- Paginación -->
    <?php if (!empty($facturas) && count($facturas) == $limite): ?>
        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination">
                    <?php if ($pagina > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?c=carrito&a=historial&pagina=<?php echo $pagina - 1; ?>">
                                <i class="fas fa-chevron-left"></i> Anterior
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="page-item active">
                        <span class="page-link">Página <?php echo $pagina; ?></span>
                    </li>
                    
                    <li class="page-item">
                        <a class="page-link" href="?c=carrito&a=historial&pagina=<?php echo $pagina + 1; ?>">
                            Siguiente <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<style>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
</style>

<?php include 'layout/footer.php'; ?>