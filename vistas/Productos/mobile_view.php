<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producto - <?php echo htmlspecialchars($producto->getNombre()); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .product-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 20px auto;
            max-width: 400px;
        }
        .product-header {
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        .product-header::before {
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
        .product-icon {
            font-size: 3rem;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }
        .product-name {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
            position: relative;
            z-index: 2;
        }
        .product-info {
            padding: 30px 25px;
        }
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
            border-left: 4px solid #667eea;
        }
        .info-icon {
            font-size: 1.5rem;
            margin-right: 15px;
            color: #667eea;
            width: 30px;
        }
        .info-content {
            flex-grow: 1;
        }
        .info-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
        }
        .price-highlight {
            color: #27ae60 !important;
            font-size: 1.8rem !important;
        }
        .footer-actions {
            padding: 20px;
            background: #f8f9fa;
            text-align: center;
        }
        .btn-custom {
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
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            color: white;
        }
        .btn-add-cart {
            background: linear-gradient(45deg, #28a745, #20c997) !important;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4) !important;
        }
        .btn-add-cart:hover {
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.6) !important;
        }
        .btn-secondary {
            background: linear-gradient(45deg, #6c757d, #495057) !important;
        }
        .btn-info {
            background: linear-gradient(45deg, #17a2b8, #138496) !important;
        }
        .cantidad-selector {
            text-align: center;
        }
        .cantidad-selector .btn {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cantidad-selector input {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            font-size: 1.1rem;
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
        .product-image-section {
            padding: 20px;
            text-align: center;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #dee2e6;
        }
        .product-image {
            max-width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .product-image:hover {
            transform: scale(1.05);
        }
        .no-image-placeholder {
            width: 100%;
            height: 250px;
            background: linear-gradient(135deg, #e9ecef, #f8f9fa);
            border: 2px dashed #6c757d;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #6c757d;
        }
        .no-image-placeholder i {
            font-size: 4rem;
            margin-bottom: 10px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="product-card">
            <!-- Header del producto -->
            <div class="product-header">
                <div class="qr-badge">
                    <i class="fas fa-qrcode"></i> QR Scan
                </div>
                <div class="product-icon">
                    <i class="fas fa-box"></i>
                </div>
                <h1 class="product-name"><?php echo htmlspecialchars($producto->getNombre()); ?></h1>
            </div>

            <!-- Sección de imagen del producto -->
            <div class="product-image-section">
                <?php if ($producto->getImagenUrl() && !empty($producto->getImagenUrl())): ?>
                    <img src="<?php echo htmlspecialchars($producto->getImagenUrl()); ?>" 
                         alt="<?php echo htmlspecialchars($producto->getNombre()); ?>" 
                         class="product-image"
                         onclick="ampliarImagen(this.src)"
                         style="cursor: pointer;">
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-touch"></i> Toca la imagen para ampliar
                        </small>
                    </div>
                <?php else: ?>
                    <div class="no-image-placeholder">
                        <i class="fas fa-image"></i>
                        <div>
                            <strong>Sin imagen disponible</strong>
                            <br>
                            <small>Este producto no tiene imagen asociada</small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Información del producto -->
            <div class="product-info">
                <!-- ID del producto -->
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-hashtag"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">ID del Producto</div>
                        <div class="info-value">#<?php echo $producto->getIdProducto(); ?></div>
                    </div>
                </div>

                <!-- Precio -->
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Precio</div>
                        <div class="info-value price-highlight">$<?php echo number_format($producto->getPrecio(), 2); ?></div>
                    </div>
                </div>

                <!-- Categoría -->
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Categoría</div>
                        <div class="info-value">
                            <?php 
                            if (isset($categoria) && $categoria) {
                                echo htmlspecialchars($categoria->getNombre());
                            } else {
                                echo "ID: " . $producto->getIdCategoria();
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Fecha de escaneo -->
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Consultado el</div>
                        <div class="info-value"><?php echo date('d/m/Y H:i'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="footer-actions">
                <!-- Formulario para agregar al carrito -->
                <form method="POST" action="?c=carrito&a=agregar" class="mb-3">
                    <input type="hidden" name="id_producto" value="<?php echo $producto->getIdProducto(); ?>">
                    <input type="hidden" name="from_mobile" value="1">
                    
                    <div class="cantidad-selector mb-3">
                        <label for="cantidad" class="form-label text-muted">
                            <i class="fas fa-sort-numeric-up"></i> Cantidad:
                        </label>
                        <div class="input-group" style="max-width: 150px; margin: 0 auto;">
                            <button type="button" class="btn btn-outline-secondary" onclick="cambiarCantidad(-1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" name="cantidad" id="cantidad" 
                                   value="1" min="1" max="99" style="font-weight: bold;">
                            <button type="button" class="btn btn-outline-secondary" onclick="cambiarCantidad(1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-custom btn-add-cart">
                        <i class="fas fa-cart-plus"></i> Agregar al Carrito
                    </button>
                </form>
                
                <a href="?c=producto&a=index" class="btn-custom btn-secondary">
                    <i class="fas fa-home"></i> Ver Todos los Productos
                </a>
                
                <a href="?c=carrito&a=mobile" class="btn-custom btn-info">
                    <i class="fas fa-shopping-cart"></i> Ver Mi Carrito
                </a>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-mobile-alt"></i> Vista optimizada para móvil
                    </small>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="text-center mt-3">
            <div class="card" style="background: rgba(255,255,255,0.9); border-radius: 15px; max-width: 400px; margin: 0 auto;">
                <div class="card-body">
                    <h6 class="text-muted">
                        <i class="fas fa-info-circle"></i> Información del Sistema
                    </h6>
                    <small class="text-muted">
                        Acceso desde código QR<br>
                        IP: <?php echo $_SERVER['SERVER_ADDR'] ?? 'N/A'; ?><br>
                        Hora: <?php echo date('H:i:s'); ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cambiarCantidad(cambio) {
            const cantidadInput = document.getElementById('cantidad');
            let cantidad = parseInt(cantidadInput.value) || 1;
            cantidad += cambio;
            
            if (cantidad < 1) cantidad = 1;
            if (cantidad > 99) cantidad = 99;
            
            cantidadInput.value = cantidad;
        }
        
        // Animación de éxito al agregar al carrito
        document.querySelector('form').addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-add-cart');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Agregando...';
            btn.disabled = true;
        });
        
        // Función para ampliar imagen
        function ampliarImagen(imagenSrc) {
            // Crear modal para mostrar imagen ampliada
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.9);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                cursor: pointer;
            `;
            
            const img = document.createElement('img');
            img.src = imagenSrc;
            img.style.cssText = `
                max-width: 100%;
                max-height: 100%;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(255,255,255,0.3);
            `;
            
            const cerrarBtn = document.createElement('div');
            cerrarBtn.innerHTML = '<i class="fas fa-times"></i>';
            cerrarBtn.style.cssText = `
                position: absolute;
                top: 20px;
                right: 20px;
                color: white;
                font-size: 2rem;
                cursor: pointer;
                z-index: 10000;
                width: 50px;
                height: 50px;
                background: rgba(0,0,0,0.5);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            `;
            
            modal.appendChild(img);
            modal.appendChild(cerrarBtn);
            document.body.appendChild(modal);
            
            // Cerrar modal al hacer clic
            modal.addEventListener('click', function() {
                document.body.removeChild(modal);
            });
            
            // Animación de entrada
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.transition = 'opacity 0.3s ease';
                modal.style.opacity = '1';
            }, 10);
        }
    </script>
</body>
</html>