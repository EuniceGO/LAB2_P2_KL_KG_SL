<?php include 'layout/menu.php'; ?>

<div class="container mt-5">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-primary">
            <i class="fas fa-image me-2"></i> Lista de Imágenes
        </h1>
        <a href="?c=imagen&a=create" class="btn btn-success shadow-sm">
            <i class="fas fa-plus-circle me-1"></i> Nueva Imagen
        </a>
    </div>

    <!-- Buscador -->
    <div class="mb-3">
        <input type="text" id="buscador" class="form-control w-50" 
               placeholder="Buscar imagen por descripción...">
    </div>

    <!-- Tabla de imágenes -->
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0" id="tablaImagenes">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>ID Imagen</th>
                            <th>Imagen</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($imagenes)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay imágenes registradas</p>
                                    <a href="?c=imagen&a=create" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Subir primera imagen
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($imagenes as $imagen): ?>
                                <tr>
                                    <td class="text-center fw-semibold"><?= $imagen->getIdImagen() ?></td>
                                    <td class="text-center">
                                        <img src="<?= $imagen->getUrlImagen() ?>" 
                                             alt="Imagen" 
                                             class="img-thumbnail" 
                                             style="max-width:100px; max-height:100px;">
                                    </td>
                                    <td><?= htmlspecialchars($imagen->getDescripcion()) ?></td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            
                                            <a href="?c=imagen&a=delete&id=<?= $imagen->getIdImagen() ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               title="Eliminar"
                                               onclick="return confirm('¿Seguro que deseas eliminar esta imagen?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script de búsqueda en tiempo real -->
<script>
document.getElementById("buscador").addEventListener("keyup", function() {
    const filtro = this.value.toLowerCase();
    const filas = document.querySelectorAll("#tablaImagenes tbody tr");

    filas.forEach(fila => {
        if(fila.querySelector('td[colspan]')) return; // fila de "no hay imágenes"
        const texto = fila.cells[2].innerText.toLowerCase(); // buscar solo en descripción
        fila.style.display = texto.includes(filtro) ? "" : "none";
    });
});
</script>
