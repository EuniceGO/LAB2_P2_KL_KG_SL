<?php
// Iniciar sesión si no existe
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Productos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="?controller=usuario&action=dashboard">
                <i class="fas fa-store"></i> Sistema de Productos
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?controller=usuario&action=dashboard">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?c=categoria&a=index">
                                <i class="fas fa-tags"></i> Categorías
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?c=producto&a=index">
                                <i class="fas fa-box"></i> Productos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?c=imagen&a=index">
                                <i class="fas fa-images"></i> Imágenes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="?c=carrito&a=index">
                                <i class="fas fa-shopping-cart"></i> Carrito
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-counter">
                                    0
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?c=carrito&a=historial">
                                <i class="fas fa-receipt"></i> Facturas
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-users-cog"></i> Administración
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="vistas/Clientes/index.php">
                                    <i class="fas fa-users"></i> Clientes
                                </a></li>
                                <li><a class="dropdown-item" href="?controller=usuario&action=index">
                                    <i class="fas fa-user-cog"></i> Usuarios
                                </a></li>
                                <li><a class="dropdown-item" href="?controller=role&action=index">
                                    <i class="fas fa-user-tag"></i> Roles
                                </a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><h6 class="dropdown-header">
                                    <i class="fas fa-user-tag"></i> <?php echo htmlspecialchars($_SESSION['user_role']); ?>
                                </h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="?controller=usuario&action=profile">
                                    <i class="fas fa-user-edit"></i> Mi Perfil
                                </a></li>
                                <li><a class="dropdown-item" href="?controller=usuario&action=changePassword">
                                    <i class="fas fa-key"></i> Cambiar Contraseña
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="?controller=usuario&action=logout">
                                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?controller=usuario&action=login">
                                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container mt-4">

    <!-- Script para actualizar contador del carrito -->
    <script>
        // Función para actualizar el contador del carrito
        function actualizarContadorCarrito() {
            fetch('?c=carrito&a=contador')
                .then(response => response.json())
                .then(data => {
                    const contador = document.getElementById('cart-counter');
                    if (contador) {
                        contador.textContent = data.cantidad;
                        // Ocultar el badge si no hay productos
                        if (data.cantidad == 0) {
                            contador.style.display = 'none';
                        } else {
                            contador.style.display = 'inline';
                        }
                    }
                })
                .catch(error => {
                    console.log('Error al actualizar contador del carrito:', error);
                });
        }

        // Actualizar contador al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            actualizarContadorCarrito();
            
            // Actualizar cada 30 segundos
            setInterval(actualizarContadorCarrito, 30000);
        });
    </script>
