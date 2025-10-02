<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .registro-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 500px;
        }
        .registro-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .registro-header i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        .btn-registro {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: white;
        }
        .btn-registro:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .input-group-text {
            background-color: #f8f9fa;
            border-color: #ced4da;
        }
        .alert {
            border-radius: 10px;
        }
        .login-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .login-link:hover {
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="registro-card">
                    <div class="registro-header">
                        <i class="fas fa-user-plus"></i>
                        <h2>Crear Cuenta</h2>
                        <p class="text-muted">Únete a nuestro sistema</p>
                    </div>

                    <?php if (isset($errores) && !empty($errores)): ?>
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Errores encontrados:</h6>
                            <ul class="mb-0">
                                <?php foreach ($errores as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="?controller=usuario&action=procesarRegistro">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre Completo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>" 
                                       placeholder="Tu nombre completo" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                       placeholder="tu@email.com" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control" id="telefono" name="telefono" 
                                               value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>" 
                                               placeholder="Tu teléfono">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <input type="text" class="form-control" id="direccion" name="direccion" 
                                               value="<?php echo htmlspecialchars($_POST['direccion'] ?? ''); ?>" 
                                               placeholder="Tu dirección">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Mínimo 6 caracteres" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                       placeholder="Repite tu contraseña" required>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-registro btn-lg">
                                <i class="fas fa-user-plus"></i> Crear Cuenta
                            </button>
                        </div>
                    </form>

                    <div class="text-center">
                        <p class="mb-0">¿Ya tienes una cuenta? 
                            <a href="?controller=usuario&action=login" class="login-link">
                                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                            </a>
                        </p>
                    </div>

                    <div class="text-center mt-3">
                        <a href="index.php" class="text-muted">
                            <i class="fas fa-arrow-left"></i> Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación de contraseñas en tiempo real
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>