<?php include 'layout/menu.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">
                    <i class="fas fa-edit"></i> Editar Producto
                </h2>
            </div>
            <div class="card-body">
                <form action="?c=producto&a=update&id=<?= $producto->getIdProducto() ?>" method="POST">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            <i class="fas fa-tag"></i> Nombre del Producto
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="nombre" 
                               name="nombre" 
                               value="<?= htmlspecialchars($producto->getNombre()) ?>" 
                               placeholder="Ingrese el nombre del producto" 
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="precio" class="form-label">
                            <i class="fas fa-dollar-sign"></i> Precio
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                   step="0.01" 
                                   class="form-control" 
                                   id="precio" 
                                   name="precio" 
                                   value="<?= $producto->getPrecio() ?>" 
                                   placeholder="0.00" 
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="id_categoria" class="form-label">
                            <i class="fas fa-folder"></i> Categoría
                        </label>
                        <select name="id_categoria" id="id_categoria" class="form-select" required>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat->getIdCategoria() ?>" 
                                        <?= $producto->getIdCategoria() == $cat->getIdCategoria() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat->getNombre()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="?c=producto&a=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
