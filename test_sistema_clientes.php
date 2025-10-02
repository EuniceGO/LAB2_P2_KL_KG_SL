<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba - Sistema de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .test-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
        }
        .test-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 15px 15px 0 0;
        }
        .test-content {
            padding: 40px;
        }
        .btn-test {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: transform 0.2s;
            margin: 5px;
        }
        .btn-test:hover {
            transform: translateY(-2px);
            color: white;
        }
        .feature-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .feature-header {
            background: #f8f9fa;
            border-radius: 10px 10px 0 0;
            padding: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="test-container">
                    <div class="test-header">
                        <h1><i class="fas fa-rocket"></i> Sistema de Login de Clientes</h1>
                        <p class="mb-0">¡Sistema implementado exitosamente!</p>
                    </div>
                    
                    <div class="test-content">
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h3><i class="fas fa-check-circle text-success"></i> ¿Qué se ha implementado?</h3>
                                <hr>
                            </div>
                        </div>
                        
                        <!-- Características implementadas -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card feature-card">
                                    <div class="feature-header">
                                        <i class="fas fa-user-plus text-primary"></i> Registro de Clientes
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success"></i> Formulario de registro completo</li>
                                            <li><i class="fas fa-check text-success"></i> Validación de campos</li>
                                            <li><i class="fas fa-check text-success"></i> Creación automática de usuario</li>
                                            <li><i class="fas fa-check text-success"></i> Vinculación cliente-usuario</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card feature-card">
                                    <div class="feature-header">
                                        <i class="fas fa-sign-in-alt text-info"></i> Login de Clientes
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success"></i> Autenticación segura</li>
                                            <li><i class="fas fa-check text-success"></i> Sesiones de cliente</li>
                                            <li><i class="fas fa-check text-success"></i> Verificación de permisos</li>
                                            <li><i class="fas fa-check text-success"></i> Redirección automática</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card feature-card">
                                    <div class="feature-header">
                                        <i class="fas fa-tachometer-alt text-warning"></i> Panel del Cliente
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success"></i> Resumen de cuenta</li>
                                            <li><i class="fas fa-check text-success"></i> Estadísticas de compras</li>
                                            <li><i class="fas fa-check text-success"></i> Lista de facturas</li>
                                            <li><i class="fas fa-check text-success"></i> Información personal</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card feature-card">
                                    <div class="feature-header">
                                        <i class="fas fa-receipt text-danger"></i> Gestión de Facturas
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success"></i> Historial completo</li>
                                            <li><i class="fas fa-check text-success"></i> Detalles de productos</li>
                                            <li><i class="fas fa-check text-success"></i> Vista de impresión</li>
                                            <li><i class="fas fa-check text-success"></i> Vinculación automática</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botones de prueba -->
                        <div class="text-center mt-5">
                            <h4><i class="fas fa-play-circle"></i> Prueba el sistema</h4>
                            <div class="d-flex flex-wrap justify-content-center">
                                <a href="index.php?c=clienteauth&a=mostrarRegistro" class="btn btn-test btn-lg">
                                    <i class="fas fa-user-plus"></i> Registrar Cliente
                                </a>
                                <a href="index.php?c=clienteauth&a=mostrarLoginCliente" class="btn btn-test btn-lg">
                                    <i class="fas fa-sign-in-alt"></i> Login Cliente
                                </a>
                                <a href="index.php" class="btn btn-test btn-lg">
                                    <i class="fas fa-home"></i> Volver al Sistema
                                </a>
                            </div>
                        </div>
                        
                        <!-- Flujo de trabajo -->
                        <div class="mt-5">
                            <h4><i class="fas fa-list-ol"></i> Flujo de trabajo</h4>
                            <hr>
                            <div class="row">
                                <div class="col-md-3 text-center mb-3">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-user-plus fa-2x text-primary mb-2"></i>
                                        <h6>1. Registro</h6>
                                        <small>El cliente se registra creando cuenta</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-sign-in-alt fa-2x text-info mb-2"></i>
                                        <h6>2. Login</h6>
                                        <small>Inicia sesión con email y contraseña</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-shopping-cart fa-2x text-warning mb-2"></i>
                                        <h6>3. Compra</h6>
                                        <small>Realiza compras vinculadas automáticamente</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-receipt fa-2x text-success mb-2"></i>
                                        <h6>4. Historial</h6>
                                        <small>Ve todas sus facturas en el panel</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notas técnicas -->
                        <div class="mt-5 p-4 bg-light rounded">
                            <h5><i class="fas fa-code"></i> Notas técnicas</h5>
                            <ul>
                                <li><strong>Base de datos:</strong> Campo <code>id_usuario</code> agregado a tabla <code>clientes</code></li>
                                <li><strong>Rol:</strong> Rol 'Cliente' creado para diferenciación de usuarios</li>
                                <li><strong>Sesiones:</strong> Sesiones separadas para administradores y clientes</li>
                                <li><strong>Vinculación automática:</strong> Las compras se vinculan automáticamente al cliente logueado</li>
                                <li><strong>Seguridad:</strong> Contraseñas hasheadas y validaciones de permisos</li>
                                <li><strong>UI/UX:</strong> Interfaz moderna y responsive para clientes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>