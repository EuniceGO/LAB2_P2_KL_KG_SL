<?php
// Verificar si es un usuario cliente
if (!isset($_SESSION['user_id']) || $_SESSION['user_role_id'] != 2) {
    header('Location: ?controller=usuario&action=login');
    exit;
}

include 'layout/menu.php';
?>

<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.product-image {
    height: 200px;
    object-fit: cover;
    border-radius: 10px 10px 0 0;
}

.price-badge {
    background: linear-gradient(45deg, #28a745, #20c997);
    border-radius: 20px;
    padding: 8px 15px;
    font-weight: bold;
    color: white;
    border: none;
}

.add-to-cart-btn {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    border-radius: 25px;
    padding: 10px 20px;
    font-weight: bold;
    transition: all 0.3s ease;
}

.add-to-cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,123,255,0.4);
}

.filter-section {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 30px;
}

.catalog-header {
    background: linear-gradient(135deg, #343a40, #495057);
    color: white;
    padding: 30px 0;
    border-radius: 15px;
    margin-bottom: 30px;
}
</style>

<div class="catalog-header text-center">
    <h1><i class="fas fa-store me-3"></i>Catálogo de Productos</h1>
    <p class="lead mb-0">Descubre nuestros productos y añádelos a tu carrito</p>
</div>

<!-- Filtros -->
<div class="filter-section">
    <div class="row align-items-center">
        <div class="col-md-6">
            <form method="GET" class="d-flex">
                <input type="hidden" name="c" value="producto">
                <input type="hidden" name="a" value="catalogo">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" 
                           name="buscar" 
                           class="form-control" 
                           placeholder="Buscar productos..." 
                           value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>
        </div>
        
        <div class="col-md-6">
            <form method="GET" class="d-flex">
                <input type="hidden" name="c" value="producto">
                <input type="hidden" name="a" value="catalogo">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-filter"></i></span>
                    <select name="categoria" class="form-select" onchange="this.form.submit()">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat->getIdCategoria(); ?>"
                                    <?php echo (isset($_GET['categoria']) && $_GET['categoria'] == $cat->getIdCategoria()) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat->getNombre()); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Información de filtros activos -->
<?php if (isset($_GET['buscar']) || isset($_GET['categoria'])): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    <?php if (isset($_GET['buscar']) && !empty($_GET['buscar'])): ?>
        Mostrando resultados para: "<strong><?php echo htmlspecialchars($_GET['buscar']); ?></strong>"
    <?php endif; ?>
    
    <?php if (isset($_GET['categoria']) && !empty($_GET['categoria'])): ?>
        <?php 
        $categoriaSeleccionada = array_filter($categorias, function($cat) {
            return $cat->getIdCategoria() == $_GET['categoria'];
        });
        if (!empty($categoriaSeleccionada)): 
            $cat = reset($categoriaSeleccionada);
        ?>
        Categoría: <strong><?php echo htmlspecialchars($cat->getNombre()); ?></strong>
        <?php endif; ?>
    <?php endif; ?>
    
    <a href="?c=producto&a=catalogo" class="btn btn-sm btn-outline-secondary ms-3">
        <i class="fas fa-times"></i> Limpiar filtros
    </a>
</div>
<?php endif; ?>

<!-- Grid de productos -->
<div class="row">
    <?php if (empty($productos)): ?>
        <div class="col-12">
            <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <h4>No se encontraron productos</h4>
                <p class="mb-0">No hay productos que coincidan con tu búsqueda. Intenta con otros términos.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($productos as $producto): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card product-card h-100 shadow-sm">
                    <?php if ($producto->getImagenUrl()): ?>
                        <img src="<?php echo htmlspecialchars($producto->getImagenUrl()); ?>" 
                             class="card-img-top product-image" 
                             alt="<?php echo htmlspecialchars($producto->getNombre()); ?>">
                    <?php else: ?>
                        <div class="card-img-top product-image d-flex align-items-center justify-content-center bg-light">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-primary">
                            <?php echo htmlspecialchars($producto->getNombre()); ?>
                        </h5>
                        
                        <div class="mb-2">
                            <span class="price-badge">
                                $<?php echo number_format($producto->getPrecio(), 2); ?>
                            </span>
                        </div>
                        
                        <div class="mt-auto">
                            <div class="d-grid gap-2">
                                <button class="btn add-to-cart-btn text-white" 
                                        onclick="agregarAlCarrito(<?php echo $producto->getIdProducto(); ?>)">
                                    <i class="fas fa-cart-plus me-2"></i>Agregar al Carrito
                                </button>
                                
                                <?php if ($producto->getCodigoQr()): ?>
                                    <a href="?c=producto&a=mobileView&id=<?php echo $producto->getIdProducto(); ?>" 
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-qrcode me-1"></i>Ver QR
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
// Función para agregar productos al carrito
function agregarAlCarrito(idProducto) {
    fetch('?c=carrito&a=agregar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'id_producto=' + idProducto + '&cantidad=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            mostrarMensaje('Producto agregado al carrito', 'success');
            
            // Actualizar contador del carrito
            actualizarContadorCarrito();
        } else {
            mostrarMensaje(data.message || 'Error al agregar el producto', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('Error al agregar el producto al carrito', 'error');
    });
}

// Función para mostrar mensajes
function mostrarMensaje(mensaje, tipo) {
    const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        <i class="fas ${iconClass} me-2"></i>
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remover después de 3 segundos
    setTimeout(() => {
        if (alertDiv && alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}
</script>

<?php include 'layout/footer.php'; ?>