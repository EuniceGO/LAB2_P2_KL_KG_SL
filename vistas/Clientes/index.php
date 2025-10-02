<?php
/**
 * Vista para mostrar lista de clientes
 */

// Verificar si ClienteModel existe
if (!class_exists('ClienteModel')) {
    require_once '../modelos/ClienteModel.php';
}

$clienteModel = new ClienteModel();

// Paginación
$limite = 20;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina - 1) * $limite;

// Búsqueda
$termino = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

if (!empty($termino)) {
    $clientes = $clienteModel->buscar($termino);
    $totalClientes = count($clientes);
} else {
    $clientes = $clienteModel->obtenerTodos($limite, $offset);
    $totalClientes = $clienteModel->contarTotal();
}

$totalPaginas = ceil($totalClientes / $limite);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3><i class="fas fa-users"></i> Gestión de Clientes</h3>
                                <p class="mb-0">Base de datos de clientes registrados</p>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-light text-dark fs-6"><?= number_format($totalClientes) ?> clientes</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Búsqueda -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <form method="GET" class="d-flex">
                                    <input type="text" name="buscar" class="form-control" 
                                           placeholder="Buscar por nombre o email..." 
                                           value="<?= htmlspecialchars($termino) ?>">
                                    <button type="submit" class="btn btn-outline-primary ms-2">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <?php if (!empty($termino)): ?>
                                        <a href="?" class="btn btn-outline-secondary ms-2">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>

                        <!-- Tabla de clientes -->
                        <?php if (!empty($clientes)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
                                            <th>Registro</th>
                                            <th>Total Facturas</th>
                                            <th>Total Compras</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <tr>
                                                <td><?= $cliente['id_cliente'] ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($cliente['nombre']) ?></strong>
                                                </td>
                                                <td>
                                                    <a href="mailto:<?= htmlspecialchars($cliente['email']) ?>">
                                                        <?= htmlspecialchars($cliente['email']) ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php if (!empty($cliente['telefono'])): ?>
                                                        <a href="tel:<?= htmlspecialchars($cliente['telefono']) ?>">
                                                            <?= htmlspecialchars($cliente['telefono']) ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">No especificado</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?= date('d/m/Y H:i', strtotime($cliente['fecha_registro'])) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?= $cliente['total_facturas'] ?? 0 ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong class="text-success">
                                                        $<?= number_format($cliente['total_compras'] ?? 0, 2) ?>
                                                    </strong>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="ver_cliente.php?id=<?= $cliente['id_cliente'] ?>" 
                                                           class="btn btn-outline-primary" title="Ver detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="?c=carrito&a=historialCliente&id=<?= $cliente['id_cliente'] ?>" 
                                                           class="btn btn-outline-success" title="Ver compras">
                                                            <i class="fas fa-shopping-cart"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación -->
                            <?php if ($totalPaginas > 1 && empty($termino)): ?>
                                <nav aria-label="Paginación de clientes">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($pagina > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?pagina=<?= $pagina - 1 ?>">Anterior</a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = max(1, $pagina - 2); $i <= min($totalPaginas, $pagina + 2); $i++): ?>
                                            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                                <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($pagina < $totalPaginas): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?pagina=<?= $pagina + 1 ?>">Siguiente</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No se encontraron clientes</h4>
                                <?php if (!empty($termino)): ?>
                                    <p>No se encontraron clientes que coincidan con: <strong><?= htmlspecialchars($termino) ?></strong></p>
                                    <a href="?" class="btn btn-primary">Ver todos los clientes</a>
                                <?php else: ?>
                                    <p>Aún no hay clientes registrados en el sistema.</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer text-center">
                        <div class="btn-group">
                            <a href="?c=carrito&a=historial" class="btn btn-outline-primary">
                                <i class="fas fa-file-invoice"></i> Ver Facturas
                            </a>
                            <a href="?c=producto&a=index" class="btn btn-outline-success">
                                <i class="fas fa-box"></i> Ver Productos
                            </a>
                            <a href="test_carrito_completo.php" class="btn btn-outline-info">
                                <i class="fas fa-test-tube"></i> Probar Sistema
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>