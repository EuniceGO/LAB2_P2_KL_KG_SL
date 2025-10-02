<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Cliente</title>
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
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
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
        .stats-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
        }
        .factura-item {
            border-left: 4px solid #667eea;
            transition: all 0.3s;
        }
        .factura-item:hover {
            background-color: #f8f9fa;
            border-left-color: #764ba2;
        }
        .welcome-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-store"></i> Mi Tienda
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($cliente['nombre']); ?>
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
        <!-- Mensajes -->
        <?php if (isset($_GET['success'])): ?>
            <?php if ($_GET['success'] === 'registro'): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> ¡Bienvenido! Tu cuenta ha sido creada exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['success'] === 'login'): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> ¡Bienvenido de vuelta!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (isset($_GET['error']) && $_GET['error'] === 'factura_no_encontrada'): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle"></i> La factura solicitada no fue encontrada o no tienes permisos para verla.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Header de bienvenida -->
        <div class="welcome-header text-center">
            <h1><i class="fas fa-user-circle"></i> Bienvenido, <?php echo htmlspecialchars($cliente['nombre']); ?></h1>
            <p class="mb-0">Gestiona tus facturas y revisa tu historial de compras</p>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3><i class="fas fa-receipt"></i> <?php echo count($facturas); ?></h3>
                        <p class="mb-0">Total de Facturas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <?php 
                        $totalGastado = 0;
                        foreach ($facturas as $factura) {
                            $totalGastado += $factura['total'];
                        }
                        ?>
                        <h3><i class="fas fa-dollar-sign"></i> $<?php echo number_format($totalGastado, 2); ?></h3>
                        <p class="mb-0">Total Gastado</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <?php 
                        $ultimaFactura = !empty($facturas) ? $facturas[0]['fecha_factura'] : 'N/A';
                        if ($ultimaFactura !== 'N/A') {
                            $fecha = new DateTime($ultimaFactura);
                            $ultimaFactura = $fecha->format('d/m/Y');
                        }
                        ?>
                        <h3><i class="fas fa-calendar"></i></h3>
                        <p class="mb-0">Última Compra</p>
                        <small><?php echo $ultimaFactura; ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Facturas -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-list"></i> Mis Facturas</h4>
            </div>
            <div class="card-body">
                <?php if (empty($facturas)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-receipt text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">No tienes facturas aún</h4>
                        <p class="text-muted">Cuando realices una compra, tus facturas aparecerán aquí.</p>
                        <a href="index.php" class="btn btn-custom">
                            <i class="fas fa-shopping-cart"></i> Ir a Comprar
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> Número</th>
                                    <th><i class="fas fa-calendar"></i> Fecha</th>
                                    <th><i class="fas fa-dollar-sign"></i> Total</th>
                                    <th><i class="fas fa-info-circle"></i> Estado</th>
                                    <th><i class="fas fa-cogs"></i> Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($facturas as $factura): ?>
                                    <tr class="factura-item">
                                        <td>
                                            <strong><?php echo htmlspecialchars($factura['numero_factura']); ?></strong>
                                        </td>
                                        <td>
                                            <?php 
                                            $fecha = new DateTime($factura['fecha_factura']);
                                            echo $fecha->format('d/m/Y H:i');
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">$<?php echo number_format($factura['total'], 2); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">Completada</span>
                                        </td>
                                        <td>
                                            <a href="index.php?c=clienteauth&a=verFactura&id=<?php echo $factura['id_factura']; ?>" 
                                               class="btn btn-sm btn-custom">
                                                <i class="fas fa-eye"></i> Ver Detalles
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Información del Cliente -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user"></i> Mi Información</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-user"></i> Nombre:</strong> <?php echo htmlspecialchars($cliente['nombre']); ?></p>
                        <p><strong><i class="fas fa-envelope"></i> Email:</strong> <?php echo htmlspecialchars($cliente['email']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-phone"></i> Teléfono:</strong> <?php echo htmlspecialchars($cliente['telefono'] ?: 'No especificado'); ?></p>
                        <p><strong><i class="fas fa-map-marker-alt"></i> Dirección:</strong> <?php echo htmlspecialchars($cliente['direccion'] ?: 'No especificada'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="text-center mt-4 mb-5">
            <a href="index.php" class="btn btn-custom me-2">
                <i class="fas fa-shopping-cart"></i> Continuar Comprando
            </a>
            <a href="index.php?c=clienteauth&a=logout" class="btn btn-outline-danger">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>