<?php include 'layout/menu.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">
                    <i class="fas fa-edit"></i> Editar Imagen
                </h2>
            </div>
            <div class="card-body">
                <form action="?c=imagen&a=update&id=<?= $imagen->getIdImagen() ?>" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label for="id_producto" class="form-label">
                            <i class="fas fa-box"></i> Producto
                        </label>
                        <select name="id_producto" id="id_producto" class="form-select" required>
                            <?php if (!empty($productos)): ?>
                                <?php foreach ($productos as $prod): ?>
                                    <option value="<?= $prod->getIdProducto() ?>" 
                                            <?= $imagen->getIdProducto() == $prod->getIdProducto() ? 'selected' : '' ?>>
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
                               value="<?= htmlspecialchars($imagen->getUrlImagen()) ?>"
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
                                  placeholder="Descripción opcional de la imagen..."><?= htmlspecialchars($imagen->getDescripcion()) ?></textarea>
                        <div class="form-text">Descripción opcional para la imagen</div>
                    </div>

                    <!-- Vista previa de la imagen actual -->
                    <?php if (!empty($imagen->getUrlImagen())): ?>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-eye"></i> Vista previa actual
                            </label>
                            <div class="text-center">
                                <img src="<?= htmlspecialchars($imagen->getUrlImagen()) ?>" 
                                     alt="Vista previa" 
                                     class="img-thumbnail" 
                                     style="max-height: 200px;"
                                     onerror="this.src='https://via.placeholder.com/200x150?text=Imagen+no+encontrada'">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between">
                        <a href="?c=imagen&a=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Imagen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
