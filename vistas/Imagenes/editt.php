

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Editar Imagen</h2>
        </div>
        <div class="card-body">
            <form action="?c=imagen&a=update&id=<?= $imagen->getIdImagen() ?>" method="POST" enctype="multipart/form-data">
                

                <div class="mb-3">
                    <label for="id_producto" class="form-label">Producto</label>
                    <select class="form-select" id="id_producto" name="id_producto" required>
                        <?php foreach ($productos as $prod): ?>
                            <option value="<?= $prod->getIdProducto() ?>" 
                                <?= $imagen->getIdProducto() == $prod->getIdProducto() ? 'selected' : '' ?>>
                                <?= $prod->getNombre() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="url_imagen" class="form-label">URL de imagen</label>
                    <input type="text" class="form-control" id="url_imagen" name="url_imagen" required>
                    <div class="form-text">
                        Imagen actual: <strong><?= $imagen->getUrlImagen() ?></strong>
                    </div>
                </div>


                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" 
                           value="<?= $imagen->getDescripcion() ?>">
                </div>


                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>