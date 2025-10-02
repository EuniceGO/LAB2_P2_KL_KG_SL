<?php include 'layout/menu.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h2 class="mb-0">
                    <i class="fas fa-plus"></i> Nueva Imagen
                </h2>
            </div>
            <div class="card-body">
                <form action="?c=imagen&a=store" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label for="id_producto" class="form-label">
                            <i class="fas fa-box"></i> Producto
                        </label>
                        <select name="id_producto" id="id_producto" class="form-select" required>
                            <option value="">Selecciona un producto</option>
                            <?php if (!empty($productos)): ?>
                                <?php foreach ($productos as $prod): ?>
                                    <option value="<?= $prod->getIdProducto() ?>">
                                        <?= htmlspecialchars($prod->getNombre()) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No hay productos disponibles</option>
                            <?php endif; ?>
                        </select>
                        <div class="form-text">Selecciona el producto al que pertenece esta imagen</div>
                    </div>

                    <div class="mb-3">
                        <label for="url_imagen" class="form-label">
                            <i class="fas fa-link"></i> URL de la Imagen
                        </label>
                        <input type="url" 
                               name="url_imagen" 
                               id="url_imagen" 
                               class="form-control" 
                               placeholder="https://ejemplo.com/imagen.jpg" 
                               required>
                        <div class="form-text">Introduce la URL completa de la imagen</div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left"></i> Descripción
                        </label>
                        <textarea name="descripcion" 
                                  id="descripcion" 
                                  class="form-control" 
                                  rows="3"
                                  placeholder="Descripción opcional de la imagen..."></textarea>
                        <div class="form-text">Descripción opcional para la imagen</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="?c=imagen&a=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Imagen
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Vista previa de imagen -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('url_imagen');
    
    urlInput.addEventListener('blur', function() {
        const url = this.value;
        let preview = document.getElementById('image-preview');
        
        if (preview) {
            preview.remove();
        }
        
        if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
            const previewDiv = document.createElement('div');
            previewDiv.id = 'image-preview';
            previewDiv.className = 'mt-2 text-center';
            previewDiv.innerHTML = '<div class="mb-2"><strong>Vista previa:</strong></div><img src="' + url + '" alt="Vista previa" class="img-thumbnail" style="max-height: 200px;" onerror="this.style.display=\'none\'; this.nextSibling.style.display=\'block\';"><div style="display:none;" class="text-muted">No se pudo cargar la imagen</div>';
            
            this.parentElement.appendChild(previewDiv);
        }
    });
});
</script>

<?php include 'layout/footer.php'; ?>