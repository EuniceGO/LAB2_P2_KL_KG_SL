<?php include 'layout/menu.php'; ?>

<div class="container mt-4">
    <!-- Encabezado -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-credit-card"></i> Checkout - Finalizar Compra
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="?c=carrito&a=procesar" id="checkoutForm">
                        <div class="row">
                            <!-- Información del cliente -->
                            <div class="col-lg-6">
                                <h5 class="mb-3">
                                    <i class="fas fa-user"></i> Información del Cliente
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre Completo *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           placeholder="Ingrese su nombre completo" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="ejemplo@correo.com">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           placeholder="(123) 456-7890">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <textarea class="form-control" id="direccion" name="direccion" rows="3" 
                                              placeholder="Dirección completa para entrega"></textarea>
                                </div>
                                
                                <!-- Método de pago -->
                                <h5 class="mb-3 mt-4">
                                    <i class="fas fa-money-bill"></i> Método de Pago
                                </h5>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="metodo_pago" id="efectivo" value="efectivo" checked>
                                    <label class="form-check-label" for="efectivo">
                                        <i class="fas fa-money-bill-wave text-success"></i> Efectivo
                                    </label>
                                </div>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="metodo_pago" id="tarjeta" value="tarjeta">
                                    <label class="form-check-label" for="tarjeta">
                                        <i class="fas fa-credit-card text-primary"></i> Tarjeta de Crédito/Débito
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="metodo_pago" id="transferencia" value="transferencia">
                                    <label class="form-check-label" for="transferencia">
                                        <i class="fas fa-university text-info"></i> Transferencia Bancaria
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Resumen del pedido -->
                            <div class="col-lg-6">
                                <h5 class="mb-3">
                                    <i class="fas fa-receipt"></i> Resumen del Pedido
                                </h5>
                                
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <!-- Lista de productos -->
                                        <div class="order-items mb-3">
                                            <?php foreach ($resumenCarrito['productos'] as $item): ?>
                                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white rounded">
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($item['nombre']); ?></strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            Cantidad: <?php echo $item['cantidad']; ?> × $<?php echo number_format($item['precio'], 2); ?>
                                                        </small>
                                                    </div>
                                                    <div class="text-end">
                                                        <strong class="text-primary">
                                                            $<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?>
                                                        </strong>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <hr>
                                        
                                        <!-- Totales -->
                                        <div class="order-totals">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Subtotal:</span>
                                                <span>$<?php echo number_format($resumenCarrito['subtotal'], 2); ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>IVA (16%):</span>
                                                <span>$<?php echo number_format($resumenCarrito['impuesto'], 2); ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Envío:</span>
                                                <span class="text-success">¡GRATIS!</span>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between">
                                                <strong class="text-primary">Total a Pagar:</strong>
                                                <strong class="text-primary fs-4">
                                                    $<?php echo number_format($resumenCarrito['total'], 2); ?>
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Términos y condiciones -->
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" id="terminos" required>
                                    <label class="form-check-label" for="terminos">
                                        Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#terminosModal">términos y condiciones</a> *
                                    </label>
                                </div>
                                
                                <!-- Botones de acción -->
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-shopping-bag"></i> Finalizar Compra
                                    </button>
                                    <a href="?c=carrito&a=index" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left"></i> Volver al Carrito
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de términos y condiciones -->
<div class="modal fade" id="terminosModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Términos y Condiciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>1. Política de Venta</h6>
                <p>Al realizar esta compra, usted acepta nuestros términos de venta y las condiciones de entrega.</p>
                
                <h6>2. Método de Pago</h6>
                <p>Los pagos se procesan de forma segura. Para pagos en efectivo, el pago se realiza contra entrega.</p>
                
                <h6>3. Entrega</h6>
                <p>Los productos se entregan en la dirección especificada en un plazo de 1-3 días hábiles.</p>
                
                <h6>4. Devoluciones</h6>
                <p>Aceptamos devoluciones dentro de los primeros 7 días posteriores a la entrega, siempre que el producto esté en perfecto estado.</p>
                
                <h6>5. Privacidad</h6>
                <p>Sus datos personales son protegidos y utilizados únicamente para procesar su pedido.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Acepto</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const btn = this.querySelector('button[type="submit"]');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    btn.disabled = true;
});
</script>

<?php include 'layout/footer.php'; ?>