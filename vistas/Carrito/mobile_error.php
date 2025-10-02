<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - No se pudo agregar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
            max-width: 400px;
            margin: 20px;
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .error-message {
            color: #6c757d;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn-error-mobile {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 25px;
            color: white;
            padding: 12px 30px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            margin: 5px;
        }
        .btn-error-mobile:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            color: white;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1 class="error-title">Error al Agregar</h1>
        <p class="error-message">
            <?php echo isset($mensaje) ? htmlspecialchars($mensaje) : 'No se pudo agregar el producto al carrito.'; ?>
            <br><br>
            Por favor, intenta nuevamente o contacta al soporte técnico.
        </p>
        
        <div class="mt-4">
            <a href="javascript:history.back()" class="btn-error-mobile">
                <i class="fas fa-arrow-left"></i> Volver e Intentar
            </a>
            <br>
            <a href="?c=producto&a=index" class="btn-error-mobile">
                <i class="fas fa-home"></i> Ir al Inicio
            </a>
            <br>
            <a href="?c=carrito&a=mobile" class="btn-error-mobile">
                <i class="fas fa-shopping-cart"></i> Ver Mi Carrito
            </a>
        </div>
        
        <div class="mt-4">
            <small class="text-muted">
                <i class="fas fa-mobile-alt"></i> Vista optimizada para móvil
            </small>
        </div>
    </div>
</body>
</html>