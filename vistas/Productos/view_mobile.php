<?php
// Vista móvil optimizada para ver desde QR
require_once __DIR__ . '/../../modelos/ProductoModel.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID de producto requerido');
}

$productoModel = new ProductoModel();
$producto = $productoModel->getById($id);

if (!$producto) {
    die('Producto no encontrado');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto->getNombre()); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .product-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .product-image {
            border-radius: 15px;
            max-width: 100%;
            height: auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .price-badge {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 1.5rem;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        .product-title {
            color: #333;
            font-weight: 700;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        .info-item {
            background: rgba(108, 117, 125, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .back-btn {
            background: linear-gradient(45deg, #6c757d, #495057);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            margin: 5px;
            display: inline-block;
        }
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.4);
            color: white;
        }
        .cart-btn {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            margin: 5px;
            display: inline-block;
            font-size: 1.1rem;
            font-weight: bold;
        }
        .cart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }
        .view-cart-btn {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            margin: 5px;
            display: inline-block;
        }
        .view-cart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="product-card p-4">
                    <div class="text-center mb-4">
                        <h1 class="product-title mb-3">
                            <i class="fas fa-box-open text-primary"></i>
                            <?php echo htmlspecialchars($producto->getNombre()); ?>
                        </h1>
                        
                        <?php if ($producto->getImagenUrl()): ?>
                            <div class="mb-4">
                                <img src="<?php echo htmlspecialchars($producto->getImagenUrl()); ?>" 
                                     alt="<?php echo htmlspecialchars($producto->getNombre()); ?>" 
                                     class="product-image"
                                     onerror="this.style.display='none'; document.getElementById('image-error').style.display='block';">
                                <div id="image-error" class="alert alert-warning mt-3" style="display: none;">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    No se pudo cargar la imagen del producto
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mb-4">
                                <div class="bg-light rounded p-4">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <p class="text-muted mt-2">Sin imagen disponible</p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="price-badge mb-4">
                            <i class="fas fa-dollar-sign"></i>
                            <?php echo number_format($producto->getPrecio(), 2); ?>
                        </div>
                    </div>
                    
                    <div class="product-info">
                        <div class="info-item">
                            <strong><i class="fas fa-tag text-primary"></i> ID del Producto:</strong>
                            <span class="float-end">#<?php echo $producto->getIdProducto(); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <strong><i class="fas fa-calendar text-info"></i> Visto:</strong>
                            <span class="float-end"><?php echo date('d/m/Y H:i'); ?></span>
                        </div>
                        
                        <?php if ($producto->getImagenUrl()): ?>
                            <div class="info-item">
                                <strong><i class="fas fa-link text-warning"></i> URL de la imagen:</strong>
                                <div class="mt-2">
                                    <small class="text-muted text-break">
                                        <?php echo htmlspecialchars($producto->getImagenUrl()); ?>
                                    </small>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="text-center mt-4">
                        <!-- Formulario para agregar al carrito -->
                        <form method="POST" action="?c=carrito&a=agregar" style="display: inline-block;">
                            <input type="hidden" name="id_producto" value="<?php echo $producto->getIdProducto(); ?>">
                            <input type="hidden" name="cantidad" value="1">
                            <input type="hidden" name="from_mobile" value="1">
                            <button type="submit" class="cart-btn">
                                <i class="fas fa-cart-plus"></i> Agregar al Carrito
                            </button>
                        </form>
                        
                        <br>
                        
                        <a href="?c=carrito&a=mobile" class="view-cart-btn">
                            <i class="fas fa-shopping-cart"></i> Ver Mi Carrito
                        </a>
                        
                        <a href="javascript:history.back()" class="back-btn">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>