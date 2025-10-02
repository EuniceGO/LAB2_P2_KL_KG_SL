<?php include 'layout/menu.php'; ?>

<div class="container mt-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">
            <i class="fas fa-receipt"></i> Factura <?php echo htmlspecialchars($datosFactura['numero_factura']); ?>
        </h1>
        <div class="btn-group">
            <a href="?c=carrito&a=historial" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Historial
            </a>
            <a href="?c=carrito&a=imprimirFactura&id=<?php echo $datosFactura['id_factura']; ?>" 
               class="btn btn-primary" target="_blank">
                <i class="fas fa-print"></i> Imprimir
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información de la factura -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información de la Factura</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Datos de la Factura</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Número:</strong></td>
                                    <td><?php echo htmlspecialchars($datosFactura['numero_factura']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha:</strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($datosFactura['fecha_factura'])); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td>
                                        <?php
                                        $badgeClass = [
                                            'pagada' => 'success',
                                            'pendiente' => 'warning',
                                            'cancelada' => 'danger'
                                        ];
                                        $class = $badgeClass[$datosFactura['estado']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $class; ?>">
                                            <?php echo ucfirst($datosFactura['estado']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Método de Pago:</strong></td>
                                    <td>
                                        <?php
                                        $iconos = [
                                            'efectivo' => 'fas fa-money-bill-wave text-success',
                                            'tarjeta' => 'fas fa-credit-card text-primary',
                                            'transferencia' => 'fas fa-university text-info'
                                        ];
                                        $icon = $iconos[$datosFactura['metodo_pago']] ?? 'fas fa-question';
                                        ?>
                                        <i class="<?php echo $icon; ?>"></i>
                                        <?php echo ucfirst($datosFactura['metodo_pago']); ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Datos del Cliente</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Nombre:</strong></td>
                                    <td><?php echo htmlspecialchars($datosFactura['cliente_nombre']); ?></td>
                                </tr>
                                <?php if ($datosFactura['cliente_email']): ?>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?php echo htmlspecialchars($datosFactura['cliente_email']); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($datosFactura['cliente_telefono']): ?>
                                <tr>
                                    <td><strong>Teléfono:</strong></td>
                                    <td><?php echo htmlspecialchars($datosFactura['cliente_telefono']); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($datosFactura['cliente_direccion']): ?>
                                <tr>
                                    <td><strong>Dirección:</strong></td>
                                    <td><?php echo nl2br(htmlspecialchars($datosFactura['cliente_direccion'])); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles de productos -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Productos Comprados</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Producto</th>
                                    <th>Precio Unitario</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detallesFactura as $detalle): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo $detalle['id_producto']; ?></span>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($detalle['nombre_producto']); ?></strong>
                                        </td>
                                        <td>
                                            $<?php echo number_format($detalle['precio_unitario'], 2); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?php echo $detalle['cantidad']; ?></span>
                                        </td>
                                        <td>
                                            <strong class="text-success">$<?php echo number_format($detalle['subtotal'], 2); ?></strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen de totales -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-calculator"></i> Resumen de Totales</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($datosFactura['subtotal'], 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>IVA (16%):</span>
                        <span>$<?php echo number_format($datosFactura['impuesto'], 2); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong class="text-primary">Total:</strong>
                        <strong class="text-primary fs-4">$<?php echo number_format($datosFactura['total'], 2); ?></strong>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info"></i> Información Adicional</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <p><strong>Creada:</strong> <?php echo date('d/m/Y H:i:s', strtotime($datosFactura['created_at'])); ?></p>
                        <?php if ($datosFactura['updated_at'] != $datosFactura['created_at']): ?>
                            <p><strong>Actualizada:</strong> <?php echo date('d/m/Y H:i:s', strtotime($datosFactura['updated_at'])); ?></p>
                        <?php endif; ?>
                        <?php if ($datosFactura['notas']): ?>
                            <p><strong>Notas:</strong> <?php echo htmlspecialchars($datosFactura['notas']); ?></p>
                        <?php endif; ?>
                        <p><strong>Total de productos:</strong> <?php echo count($detallesFactura); ?></p>
                        <p><strong>Cantidad total:</strong> <?php echo array_sum(array_column($detallesFactura, 'cantidad')); ?> artículos</p>
                    </small>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="?c=carrito&a=imprimirFactura&id=<?php echo $datosFactura['id_factura']; ?>" 
                           class="btn btn-primary" target="_blank">
                            <i class="fas fa-print"></i> Imprimir Factura
                        </a>
                        <a href="?c=carrito&a=historial" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> Ver Todas las Facturas
                        </a>
                        <a href="?c=producto&a=index" class="btn btn-outline-success">
                            <i class="fas fa-shopping-cart"></i> Seguir Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-borderless td {
    border: none !important;
    padding: 0.25rem 0.5rem;
}
</style>

<?php include 'layout/footer.php'; ?>