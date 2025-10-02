<?php include 'layout/menu.php'; ?>

<div class="container mt-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white">
            <h2 class="h5 mb-0">✏️ Editar Categoría</h2>
        </div>
        <div class="card-body">
            <form action="?c=categoria&a=update&id=<?= $categoria->getIdCategoria() ?>" method="POST">
                
                <div class="mb-3">
                    <label class="form-label">Nombre:</label>
                    <input type="text" 
                           name="nombre" 
                           value="<?= $categoria->getNombre() ?>" 
                           class="form-control" 
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción:</label>
                    <textarea name="descripcion" 
                              class="form-control" 
                              rows="4" 
                              required><?= $categoria->getDescripcion() ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="?c=categoria&a=index" class="btn btn-secondary">
                        ⬅️ Volver
                    </a>
                    <button type="submit" class="btn btn-success">
                        💾 Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
