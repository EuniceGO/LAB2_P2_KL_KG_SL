<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Acceso QR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 2px,
                rgba(255,255,255,0.1) 2px,
                rgba(255,255,255,0.1) 4px
            );
            animation: shine 3s linear infinite;
        }
        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        .login-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
        }
        .login-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
            position: relative;
            z-index: 2;
        }
        .login-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 5px;
            position: relative;
            z-index: 2;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-floating {
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 12px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 25px;
            color: white;
            padding: 12px 30px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            margin-bottom: 15px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            color: white;
        }
        .btn-register {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            border-radius: 25px;
            color: white;
            padding: 10px 25px;
            font-weight: 500;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.6);
            color: white;
        }
        .product-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .product-name {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .product-price {
            color: #27ae60;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .alert {
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .qr-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            z-index: 3;
        }
        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #dee2e6;
        }
        .divider span {
            background: white;
            padding: 0 15px;
            color: #6c757d;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <div class="qr-badge">
                <i class="fas fa-qrcode"></i> QR Access
            </div>
            <div class="login-icon">
                <i class="fas fa-user-circle"></i>
            </div>
            <h1 class="login-title">Iniciar Sesión</h1>
            <p class="login-subtitle">Para continuar escaneando productos</p>
        </div>

        <!-- Body -->
        <div class="login-body">
            <!-- Mostrar información del producto si existe -->
            <?php if (isset($producto) && $producto): ?>
                <div class="product-info">
                    <div class="product-name">
                        <i class="fas fa-box"></i> <?php echo htmlspecialchars($producto->getNombre()); ?>
                    </div>
                    <div class="product-price">
                        <i class="fas fa-dollar-sign"></i> $<?php echo number_format($producto->getPrecio(), 2); ?>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Inicia sesión para agregar este producto al carrito
                    </small>
                </div>
            <?php endif; ?>

            <!-- Mostrar errores -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Formulario de login -->
            <form method="POST" action="?controller=usuario&action=authenticate">
                <!-- Campos ocultos para mantener el contexto móvil -->
                <input type="hidden" name="from_mobile" value="1">
                <?php if (isset($producto) && $producto): ?>
                    <input type="hidden" name="producto_id" value="<?php echo $producto->getIdProducto(); ?>">
                <?php endif; ?>

                <div class="form-floating">
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="correo@ejemplo.com" required>
                    <label for="email">
                        <i class="fas fa-envelope"></i> Correo Electrónico
                    </label>
                </div>

                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Contraseña" required>
                    <label for="password">
                        <i class="fas fa-lock"></i> Contraseña
                    </label>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>

            <!-- Divider -->
            <div class="divider">
                <span>¿No tienes cuenta?</span>
            </div>

            <!-- Botón de registro -->
            <a href="?controller=usuario&action=registro" class="btn btn-register">
                <i class="fas fa-user-plus"></i> Crear Cuenta Nueva
            </a>

            <!-- Información adicional -->
            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fas fa-mobile-alt"></i> Vista optimizada para móvil<br>
                    <i class="fas fa-qrcode"></i> Acceso desde código QR<br>
                    <i class="fas fa-clock"></i> <?php echo date('H:i:s'); ?>
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus en el campo de email
        document.getElementById('email').focus();
        
        // Animación del botón de login
        document.querySelector('form').addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-login');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Iniciando sesión...';
            btn.disabled = true;
        });
    </script>
</body>
</html>