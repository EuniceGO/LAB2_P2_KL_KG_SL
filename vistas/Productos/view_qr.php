<?php include 'layout/menu.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-qrcode"></i> Código QR del Producto</h4>
                </div>
                <div class="card-body text-center">
                    <?php if ($producto): ?>
                        <h5><?php echo htmlspecialchars($producto->getNombre()); ?></h5>
                        <p class="text-muted">Precio: $<?php echo number_format($producto->getPrecio(), 2); ?></p>
                        
                        <!-- Mostrar imagen del producto si existe -->
                        <?php if ($producto->getImagenUrl()): ?>
                            <div class="mb-4">
                                <h6><i class="fas fa-image"></i> Imagen del Producto</h6>
                                <div class="d-flex justify-content-center">
                                    <img src="<?php echo htmlspecialchars($producto->getImagenUrl()); ?>" 
                                         alt="<?php echo htmlspecialchars($producto->getNombre()); ?>" 
                                         class="img-fluid rounded shadow" 
                                         style="max-width: 300px; max-height: 250px; object-fit: cover;"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <div class="alert alert-warning" style="display: none;">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        No se pudo cargar la imagen del producto
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($producto->getCodigoQr()): ?>
                            <?php 
                            $qrPath = $producto->getCodigoQr();
                            // Verificar si es una URL (JSON) o un archivo
                            if (strpos($qrPath, 'http') === 0 || strpos($qrPath, '{') !== false): 
                            ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>Código QR en formato incorrecto</strong><br>
                                    Los datos del QR se están mostrando como texto en lugar de imagen.<br>
                                    <small>Ejecuta el script de corrección: <a href="fix_qr_codes.php" target="_blank">fix_qr_codes.php</a></small>
                                </div>
                                <div class="alert alert-info">
                                    <strong>Datos del QR:</strong><br>
                                    <code><?php echo htmlspecialchars($qrPath); ?></code>
                                </div>
                            <?php elseif (file_exists($qrPath)): ?>
                                <div class="mb-3">
                                    <img src="<?php echo $qrPath; ?>" alt="Código QR" class="img-fluid" style="max-width: 300px;">
                                </div>
                                <p><strong>Archivo:</strong> <?php echo basename($qrPath); ?></p>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>El archivo del código QR no existe</strong><br>
                                    Ruta: <code><?php echo htmlspecialchars($qrPath); ?></code><br>
                                    <small>Ejecuta el script de corrección: <a href="fix_qr_codes.php" target="_blank">fix_qr_codes.php</a></small>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Este producto no tiene código QR generado.
                                <br><small>Se generará automáticamente al crear o editar el producto.</small>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-4">
                            <a href="?c=producto&a=regenerateQR&id=<?php echo $producto->getIdProducto(); ?>" 
                               class="btn btn-primary">
                                <i class="fas fa-sync"></i> 
                                <?php echo $producto->getCodigoQr() ? 'Regenerar QR' : 'Generar QR'; ?>
                            </a>
                            <a href="?c=producto&a=index" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                        
                        <?php if ($producto->getCodigoQr()): ?>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <strong>Datos del QR:</strong><br>
                                    <?php
                                    echo json_encode([
                                        'id' => $producto->getIdProducto(),
                                        'nombre' => $producto->getNombre(),
                                        'precio' => $producto->getPrecio(),
                                        'categoria' => $producto->getIdCategoria(),
                                        'imagen_url' => $producto->getImagenUrl()
                                    ], JSON_PRETTY_PRINT);
                                    ?>
                                </small>
                            </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Producto no encontrado.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>