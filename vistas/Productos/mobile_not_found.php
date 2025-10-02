<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producto No Encontrado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
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
            color: #ff6b6b;
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
        .btn-home {
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
        }
        .btn-home:hover {
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
        <h1 class="error-title">Producto No Encontrado</h1>
        <p class="error-message">
            Lo sentimos, el producto que buscas no existe o ha sido eliminado.
            <br><br>
            Verifica que el código QR sea válido o contacta al administrador.
        </p>
        <a href="?c=producto&a=index" class="btn-home">
            <i class="fas fa-home"></i> Ir al Inicio
        </a>
        
        <div class="mt-4">
            <small class="text-muted">
                <i class="fas fa-mobile-alt"></i> Vista optimizada para móvil
            </small>
        </div>
    </div>
</body>
</html>