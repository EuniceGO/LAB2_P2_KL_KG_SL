<?php
/**
 * Vista para mostrar detalles de un cliente específico
 */

// Verificar si ClienteModel existe
if (!class_exists('ClienteModel')) {
    require_once '../modelos/ClienteModel.php';
}

$clienteModel = new ClienteModel();
$idCliente = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idCliente <= 0) {
    header('Location: index.php');
    exit;
}

$cliente = $clienteModel->buscarPorId($idCliente);

if (!$cliente) {
    header('Location: index.php');
    exit;
}

// Obtener historial de compras
$historialCompras = $clienteModel->obtenerHistorialCompras($idCliente);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliente: <?= htmlspecialchars($cliente['nombre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Información del Cliente -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-user"></i> Información del Cliente</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; font-size: 2rem;">
                                <i class="fas fa-user"></i>
                            </div>
                            <h5 class="mt-3"><?= htmlspecialchars($cliente['nombre']) ?></h5>
                            <p class="text-muted">ID: <?= $cliente['id_cliente'] ?></p>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Email:</label>
                                <p class="mb-0">
                                    <a href="mailto:<?= htmlspecialchars($cliente['email']) ?>">
                                        <?= htmlspecialchars($cliente['email']) ?>
                                    </a>
                                </p>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-bold">Teléfono:</label>
                                <p class="mb-0">
                                    <?php if (!empty($cliente['telefono'])): ?>
                                        <a href="tel:<?= htmlspecialchars($cliente['telefono']) ?>">
                                            <?= htmlspecialchars($cliente['telefono']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No especificado</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-bold">Dirección:</label>
                                <p class="mb-0">
                                    <?php if (!empty($cliente['direccion'])): ?>
                                        <?= nl2br(htmlspecialchars($cliente['direccion'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">No especificada</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-bold">Fecha de Registro:</label>
                                <p class="mb-0">
                                    <?= date('d/m/Y H:i', strtotime($cliente['fecha_registro'])) ?>
                                </p>
                            </div>
                            
                            <?php if ($cliente['fecha_actualizacion'] !== $cliente['fecha_registro']): ?>
                            <div class="col-12">
                                <label class="form-label fw-bold">Última Actualización:</label>
                                <p class="mb-0">
                                    <?= date('d/m/Y H:i', strtotime($cliente['fecha_actualizacion'])) ?>
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Estadísticas -->
                <div class="card mt-4">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-chart-bar"></i> Estadísticas</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $totalFacturas = count($historialCompras);
                        $totalCompras = array_sum(array_column($historialCompras, 'total'));
                        $promedioCompra = $totalFacturas > 0 ? $totalCompras / $totalFacturas : 0;
                        ?>
                        
                        <div class="row text-center">
                            <div class="col-6">
                                <h3 class="text-primary"><?= $totalFacturas ?></h3>
                                <p class="mb-0">Facturas</p>
                            </div>
                            <div class="col-6">
                                <h3 class="text-success">$<?= number_format($totalCompras, 2) ?></h3>
                                <p class="mb-0">Total Compras</p>
                            </div>
                            <div class="col-12 mt-3">
                                <h4 class="text-info">$<?= number_format($promedioCompra, 2) ?></h4>
                                <p class="mb-0">Promedio por Compra</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Historial de Compras -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h4><i class="fas fa-shopping-cart"></i> Historial de Compras</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($historialCompras)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Número</th>
                                            <th>Fecha</th>
                                            <th>Items</th>
                                            <th>Total</th>
                                            <th>Método</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($historialCompras as $factura): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($factura['numero_factura']) ?></strong>
                                                </td>
                                                <td>
                                                    <?= date('d/m/Y', strtotime($factura['fecha_factura'])) ?><br>
                                                    <small class="text-muted">
                                                        <?= date('H:i', strtotime($factura['fecha_factura'])) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?= $factura['total_items'] ?? 0 ?> items
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong class="text-success">
                                                        $<?= number_format($factura['total'], 2) ?>
                                                    </strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        <?= ucfirst($factura['metodo_pago']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $estadoClass = match($factura['estado']) {
                                                        'pagada' => 'bg-success',
                                                        'pendiente' => 'bg-warning text-dark',
                                                        'cancelada' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                    ?>
                                                    <span class="badge <?= $estadoClass ?>">
                                                        <?= ucfirst($factura['estado']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="?c=carrito&a=verFactura&numero=<?= $factura['numero_factura'] ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Ver factura">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay compras registradas</h5>
                                <p>Este cliente aún no ha realizado ninguna compra.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botones de navegación -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <div class="btn-group">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a Clientes
                    </a>
                    <a href="?c=carrito&a=historial" class="btn btn-primary">
                        <i class="fas fa-file-invoice"></i> Ver Todas las Facturas
                    </a>
                    <a href="?c=producto&a=index" class="btn btn-success">
                        <i class="fas fa-box"></i> Ver Productos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>