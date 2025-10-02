<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-form {
            padding: 40px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px;
            color: white;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            color: white;
        }
        .register-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .register-link:hover {
            color: #764ba2;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .input-group-text {
            background: #f8f9fa;
            border-color: #ced4da;
        }
        .welcome-message {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-container">
                    <div class="login-header">
                        <h2><i class="fas fa-user-circle"></i> Portal del Cliente</h2>
                        <p class="mb-0">Accede a tu cuenta para ver tus facturas</p>
                    </div>
                    
                    <div class="login-form">
                        <?php if (isset($_GET['success']) && $_GET['success'] === 'logout'): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> Has cerrado sesión correctamente
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($errores) && !empty($errores)): ?>
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-exclamation-triangle"></i> Error de acceso:</h6>
                                <ul class="mb-0">
                                    <?php foreach ($errores as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="index.php?c=clienteauth&a=loginCliente">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                           placeholder="tu@email.com" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Tu contraseña" required>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-login btn-lg">
                                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="mb-2">¿No tienes una cuenta?</p>
                            <a href="index.php?c=clienteauth&a=mostrarRegistro" class="register-link">
                                <i class="fas fa-user-plus"></i> Registrarse
                            </a>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="index.php" class="text-muted">
                                <i class="fas fa-arrow-left"></i> Volver al inicio
                            </a>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <h6 class="text-muted mb-3">¿Qué puedes hacer como cliente?</h6>
                            <div class="row text-center">
                                <div class="col-12 mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-receipt text-primary"></i> Ver todas tus facturas
                                    </small>
                                </div>
                                <div class="col-12 mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-history text-success"></i> Historial de compras
                                    </small>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted">
                                        <i class="fas fa-download text-info"></i> Descargar detalles
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>