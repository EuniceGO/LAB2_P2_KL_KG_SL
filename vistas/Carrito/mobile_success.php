<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producto Agregado - ¡Éxito!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
            max-width: 400px;
            margin: 20px;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
            animation: bounceIn 0.7s ease-out 0.2s both;
        }
        @keyframes bounceIn {
            0%, 20%, 40%, 60%, 80% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }
        .success-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .success-message {
            color: #6c757d;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .cart-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .cart-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .btn-success-mobile {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            border-radius: 25px;
            color: white;
            padding: 12px 30px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
            margin: 5px;
        }
        .btn-success-mobile:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.6);
            color: white;
        }
        .btn-outline-mobile {
            background: transparent;
            border: 2px solid #6c757d;
            color: #6c757d;
            box-shadow: none;
        }
        .btn-outline-mobile:hover {
            background: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1 class="success-title">¡Producto Agregado!</h1>
        <p class="success-message">
            El producto se agregó exitosamente a tu carrito de compras.
        </p>

        <!-- Resumen del carrito -->
        <div class="cart-summary">
            <h6 class="text-muted mb-3">
                <i class="fas fa-shopping-cart"></i> Resumen de tu Carrito
            </h6>
            <div class="cart-info">
                <span>Productos:</span>
                <strong><?php echo $resumenCarrito['cantidad_total']; ?> artículos</strong>
            </div>
            <div class="cart-info">
                <span>Subtotal:</span>
                <strong class="text-success">$<?php echo number_format($resumenCarrito['subtotal'], 2); ?></strong>
            </div>
            <div class="cart-info">
                <span>Total:</span>
                <strong class="text-primary">$<?php echo number_format($resumenCarrito['total'], 2); ?></strong>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="mt-4">
            <a href="?c=carrito&a=mobile" class="btn-success-mobile">
                <i class="fas fa-shopping-cart"></i> Ver Mi Carrito
            </a>
            <br>
            <a href="?c=carrito&a=checkout" class="btn-success-mobile">
                <i class="fas fa-credit-card"></i> Ir al Checkout
            </a>
            <br>
            <a href="?c=producto&a=index" class="btn-success-mobile btn-outline-mobile">
                <i class="fas fa-plus"></i> Seguir Comprando
            </a>
        </div>

        <!-- Auto-redirect opcional -->
        <div class="mt-4">
            <small class="text-muted">
                <i class="fas fa-clock"></i> 
                Redirigiendo al carrito en <span id="countdown">5</span> segundos...
                <br>
                <a href="#" onclick="clearInterval(redirectTimer); document.getElementById('countdown').innerHTML = '∞';">
                    Cancelar redirección
                </a>
            </small>
        </div>

        <div class="mt-3">
            <small class="text-muted">
                <i class="fas fa-mobile-alt"></i> Vista optimizada para móvil
            </small>
        </div>
    </div>

    <script>
        // Auto-redirect después de 5 segundos
        let timeLeft = 5;
        const countdownElement = document.getElementById('countdown');
        
        const redirectTimer = setInterval(() => {
            timeLeft--;
            countdownElement.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(redirectTimer);
                window.location.href = '?c=carrito&a=mobile';
            }
        }, 1000);
    </script>
</body>
</html>