<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .cart-container {
            max-width: 500px;
            margin: 20px auto;
            padding: 0 15px;
        }
        .cart-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .cart-header {
            background: linear-gradient(45deg, #17a2b8, #138496);
            color: white;
            padding: 25px 20px;
            text-align: center;
        }
        .cart-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
        }
        .cart-counter {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 15px;
            margin-top: 10px;
            display: inline-block;
        }
        .cart-content {
            padding: 20px;
        }
        .cart-item {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #17a2b8;
        }
        .item-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .item-details {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
        }
        .item-price {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .price-info {
            color: #28a745;
            font-weight: bold;
        }
        .quantity-badge {
            background: #17a2b8;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
        }
        .summary-card {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .summary-total {
            border-top: 2px solid #17a2b8;
            padding-top: 15px;
            margin-top: 15px;
            font-size: 1.2rem;
            font-weight: bold;
            color: #17a2b8;
        }
        .btn-mobile {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            border-radius: 25px;
            color: white;
            padding: 12px 25px;
            font-weight: 600;
            text-decoration: none;
            display: block;
            text-align: center;
            margin: 10px 0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }
        .btn-mobile:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.6);
            color: white;
        }
        .btn-secondary-mobile {
            background: linear-gradient(45deg, #6c757d, #495057);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.4);
        }
        .btn-outline-mobile {
            background: transparent;
            border: 2px solid #17a2b8;
            color: #17a2b8;
            box-shadow: none;
        }
        .empty-cart {
            text-align: center;
            padding: 50px 20px;
        }
        .empty-icon {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <div class="cart-card">
            <!-- Header -->
            <div class="cart-header">
                <h1 class="cart-title">
                    <i class="fas fa-shopping-cart"></i> Mi Carrito
                </h1>
                <div class="cart-counter">
                    <?php echo $resumenCarrito['cantidad_total']; ?> productos
                </div>
            </div>

            <!-- Contenido -->
            <div class="cart-content">
                <?php if ($resumenCarrito['esta_vacio']): ?>
                    <!-- Carrito vacío -->
                    <div class="empty-cart">
                        <div class="empty-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h4 class="text-muted mb-3">Tu carrito está vacío</h4>
                        <p class="text-muted mb-4">¡Agrega algunos productos para comenzar!</p>
                        <a href="?c=producto&a=index" class="btn-mobile">
                            <i class="fas fa-box"></i> Ver Productos
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Productos en el carrito -->
                    <?php foreach ($resumenCarrito['productos'] as $item): ?>
                        <div class="cart-item">
                            <div class="item-name">
                                <?php echo htmlspecialchars($item['nombre']); ?>
                            </div>
                            <div class="item-details">
                                ID: <?php echo $item['id']; ?> | Cat: <?php echo $item['categoria']; ?>
                            </div>
                            <div class="item-price">
                                <div class="price-info">
                                    $<?php echo number_format($item['precio'], 2); ?> × <?php echo $item['cantidad']; ?>
                                </div>
                                <div>
                                    <span class="quantity-badge">
                                        Cant: <?php echo $item['cantidad']; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 text-end">
                                <strong class="text-primary">
                                    $<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?>
                                </strong>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Resumen de totales -->
                    <div class="summary-card">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($resumenCarrito['subtotal'], 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>IVA (16%):</span>
                            <span>$<?php echo number_format($resumenCarrito['impuesto'], 2); ?></span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>Total:</span>
                            <span>$<?php echo number_format($resumenCarrito['total'], 2); ?></span>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="mt-4">
                        <a href="?c=carrito&a=checkout" class="btn-mobile">
                            <i class="fas fa-credit-card"></i> Proceder al Pago
                        </a>
                        <a href="?c=carrito&a=index" class="btn-mobile btn-outline-mobile">
                            <i class="fas fa-desktop"></i> Ver en Versión Web
                        </a>
                        <a href="?c=producto&a=index" class="btn-mobile btn-secondary-mobile">
                            <i class="fas fa-plus"></i> Seguir Comprando
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fas fa-mobile-alt"></i> Vista optimizada para móvil<br>
                        Última actualización: <?php echo date('H:i:s'); ?>
                    </small>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="text-center mt-3">
            <div class="card" style="background: rgba(255,255,255,0.9); border-radius: 15px;">
                <div class="card-body">
                    <h6 class="text-muted">
                        <i class="fas fa-shield-alt"></i> Compra Segura
                    </h6>
                    <small class="text-muted">
                        Tus datos están protegidos<br>
                        Envío gratuito en todas las compras
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>