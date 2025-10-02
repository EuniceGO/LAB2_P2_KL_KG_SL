<?php include 'layout/menu.php'; ?>

<div class="container mt-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">
            <i class="fas fa-shopping-cart"></i> Mi Carrito de Compras
        </h1>
        <div class="cart-counter">
            <span class="badge bg-primary fs-6"><?php echo $resumenCarrito['cantidad_total']; ?> productos</span>
        </div>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if (isset($_GET['agregado'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> ¡Producto agregado al carrito exitosamente!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['actualizado'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-edit"></i> Cantidad actualizada exitosamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['eliminado'])): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-trash"></i> Producto eliminado del carrito.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['vaciado'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-broom"></i> Carrito vaciado completamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> 
            <?php 
            switch($_GET['error']) {
                case 'carrito_vacio': echo 'El carrito está vacío.'; break;
                case 'id_invalido': echo 'ID de producto no válido.'; break;
                case 'datos_invalidos': echo 'Datos no válidos.'; break;
                default: echo 'Error desconocido.'; break;
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($resumenCarrito['esta_vacio']): ?>
        <!-- Carrito vacío -->
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h3 class="text-muted mb-3">Tu carrito está vacío</h3>
                <p class="text-muted mb-4">¡Agrega algunos productos para comenzar a comprar!</p>
                <a href="?c=producto&a=index" class="btn btn-primary btn-lg">
                    <i class="fas fa-box"></i> Ver Productos
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Contenido del carrito -->
        <div class="row">
            <!-- Lista de productos -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Productos en tu Carrito</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($resumenCarrito['productos'] as $item): ?>
                            <div class="cart-item border-bottom p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="product-icon me-3">
                                                <i class="fas fa-box fa-2x text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($item['nombre']); ?></h6>
                                                <small class="text-muted">ID: <?php echo $item['id']; ?> | Categoría: <?php echo $item['categoria']; ?></small>
                                                <br><small class="text-muted">Agregado: <?php echo date('d/m/Y H:i', strtotime($item['fecha_agregado'])); ?></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <strong class="text-success">$<?php echo number_format($item['precio'], 2); ?></strong>
                                        <br><small class="text-muted">c/u</small>
                                    </div>
                                    <div class="col-md-2">
                                        <form method="POST" action="?c=carrito&a=actualizar" class="d-inline">
                                            <input type="hidden" name="id_producto" value="<?php echo $item['id']; ?>">
                                            <div class="input-group input-group-sm">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="cambiarCantidad(<?php echo $item['id']; ?>, -1)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="form-control text-center" name="cantidad" 
                                                       id="cantidad_<?php echo $item['id']; ?>" value="<?php echo $item['cantidad']; ?>" 
                                                       min="0" max="99" onchange="this.form.submit()">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="cambiarCantidad(<?php echo $item['id']; ?>, 1)">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <strong class="text-primary">$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></strong>
                                        <br>
                                        <a href="?c=carrito&a=eliminar&id=<?php echo $item['id']; ?>" 
                                           class="btn btn-outline-danger btn-sm mt-1" 
                                           onclick="return confirm('¿Eliminar este producto del carrito?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Resumen del carrito -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calculator"></i> Resumen de Compra</h5>
                    </div>
                    <div class="card-body">
                        <div class="summary-row d-flex justify-content-between mb-2">
                            <span>Productos:</span>
                            <span><?php echo $resumenCarrito['cantidad_total']; ?> artículos</span>
                        </div>
                        <div class="summary-row d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($resumenCarrito['subtotal'], 2); ?></span>
                        </div>
                        <div class="summary-row d-flex justify-content-between mb-2">
                            <span>IVA (16%):</span>
                            <span>$<?php echo number_format($resumenCarrito['impuesto'], 2); ?></span>
                        </div>
                        <hr>
                        <div class="summary-row d-flex justify-content-between mb-3">
                            <strong class="text-primary">Total:</strong>
                            <strong class="text-primary fs-5">$<?php echo number_format($resumenCarrito['total'], 2); ?></strong>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="?c=carrito&a=checkout" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card"></i> Proceder al Pago
                            </a>
                            <a href="?c=producto&a=index" class="btn btn-outline-primary">
                                <i class="fas fa-plus"></i> Seguir Comprando
                            </a>
                            <a href="?c=carrito&a=vaciar" class="btn btn-outline-danger" 
                               onclick="return confirm('¿Estás seguro de vaciar el carrito?')">
                                <i class="fas fa-broom"></i> Vaciar Carrito
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function cambiarCantidad(idProducto, cambio) {
    const input = document.getElementById('cantidad_' + idProducto);
    let cantidad = parseInt(input.value) || 0;
    cantidad += cambio;
    
    if (cantidad < 0) cantidad = 0;
    if (cantidad > 99) cantidad = 99;
    
    input.value = cantidad;
    
    // Auto-submit del formulario
    input.closest('form').submit();
}
</script>

<?php include 'layout/footer.php'; ?>