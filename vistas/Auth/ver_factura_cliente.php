<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura <?php echo htmlspecialchars($factura['numero_factura']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            color: white;
            transition: transform 0.2s;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            color: white;
        }
        .factura-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .producto-item {
            border-left: 4px solid #667eea;
            transition: all 0.3s;
            margin-bottom: 10px;
        }
        .producto-item:hover {
            background-color: #f8f9fa;
            border-left-color: #764ba2;
        }
        .total-section {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
        }
        .producto-imagen {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
            }
            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom no-print">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-store"></i> Mi Tienda
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($factura['cliente_nombre']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php?c=clienteauth&a=panelCliente">
                            <i class="fas fa-tachometer-alt"></i> Panel
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="index.php?c=clienteauth&a=logout">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Botones de navegación -->
        <div class="row no-print mb-3">
            <div class="col-md-6">
                <a href="index.php?c=clienteauth&a=panelCliente" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Panel
                </a>
            </div>
            <div class="col-md-6 text-end">
                <button onclick="window.print()" class="btn btn-custom">
                    <i class="fas fa-print"></i> Imprimir Factura
                </button>
            </div>
        </div>

        <!-- Header de la factura -->
        <div class="factura-header text-center">
            <h1><i class="fas fa-receipt"></i> Factura #<?php echo htmlspecialchars($factura['numero_factura']); ?></h1>
            <p class="mb-0">Fecha: <?php 
                $fecha = new DateTime($factura['fecha_factura']);
                echo $fecha->format('d/m/Y H:i:s');
            ?></p>
        </div>

        <div class="row">
            <!-- Información de la empresa -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-building"></i> Información de la Empresa</h5>
                    </div>
                    <div class="card-body">
                        <h6><strong>Mi Tienda Online</strong></h6>
                        <p class="mb-1">Dirección: Calle Principal #123</p>
                        <p class="mb-1">Teléfono: +1 234 567 8900</p>
                        <p class="mb-1">Email: contacto@mitienda.com</p>
                        <p class="mb-0">NIT: 123456789-0</p>
                    </div>
                </div>
            </div>

            <!-- Información del cliente -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Información del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <h6><strong><?php echo htmlspecialchars($factura['cliente_nombre']); ?></strong></h6>
                        <p class="mb-1">Email: <?php echo htmlspecialchars($factura['cliente_email']); ?></p>
                        <?php if (!empty($factura['cliente_telefono'])): ?>
                            <p class="mb-1">Teléfono: <?php echo htmlspecialchars($factura['cliente_telefono']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($factura['cliente_direccion'])): ?>
                            <p class="mb-0">Dirección: <?php echo htmlspecialchars($factura['cliente_direccion']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalles de productos -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Productos Comprados</h5>
            </div>
            <div class="card-body">
                <?php if (empty($detalles)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-box-open text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">No hay productos en esta factura</h5>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Descripción</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalles as $detalle): ?>
                                    <tr class="producto-item">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($detalle['producto_imagen'])): ?>
                                                    <img src="<?php echo htmlspecialchars($detalle['producto_imagen']); ?>" 
                                                         alt="<?php echo htmlspecialchars($detalle['producto_nombre']); ?>"
                                                         class="producto-imagen me-3"
                                                         onerror="this.src='https://via.placeholder.com/50x50?text=No+Img'">
                                                <?php else: ?>
                                                    <div class="producto-imagen me-3 bg-light d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <strong><?php echo htmlspecialchars($detalle['producto_nombre']); ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($detalle['producto_descripcion'] ?: 'Sin descripción'); ?>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary"><?php echo $detalle['cantidad']; ?></span>
                                        </td>
                                        <td class="text-end">
                                            $<?php echo number_format($detalle['precio_unitario'], 2); ?>
                                        </td>
                                        <td class="text-end">
                                            <strong>$<?php echo number_format($detalle['subtotal'], 2); ?></strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Total de la factura -->
        <div class="row mt-4">
            <div class="col-md-6 offset-md-6">
                <div class="total-section">
                    <h3 class="text-center">
                        <i class="fas fa-calculator"></i> Total de la Factura
                    </h3>
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-1">Subtotal:</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-1">${{ number_format($factura['subtotal'], 2) }}</p>
                        </div>
                    </div>
                    <?php if ($factura['impuestos'] > 0): ?>
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-1">Impuestos:</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-1">${{ number_format($factura['impuestos'], 2) }}</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <hr style="border-color: rgba(255,255,255,0.5);">
                    <div class="row">
                        <div class="col-6">
                            <h4>TOTAL:</h4>
                        </div>
                        <div class="col-6 text-end">
                            <h4>${{ number_format($factura['total'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-5 mb-4 no-print">
            <p class="text-muted">
                <i class="fas fa-info-circle"></i> 
                Esta factura fue generada electrónicamente. Para cualquier consulta, 
                contacta nuestro servicio al cliente.
            </p>
            <div class="mt-3">
                <a href="index.php?c=clienteauth&a=panelCliente" class="btn btn-custom me-2">
                    <i class="fas fa-arrow-left"></i> Volver al Panel
                </a>
                <a href="index.php" class="btn btn-outline-primary">
                    <i class="fas fa-shopping-cart"></i> Continuar Comprando
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>