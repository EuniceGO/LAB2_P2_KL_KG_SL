<?php include 'layout/menu.php'; ?>

<div class="container mt-4">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">
            <i class="fas fa-box"></i> Lista de Productos
        </h1>
        <a href="?c=producto&a=create" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo Producto
        </a>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> ¡Producto creado exitosamente con código QR!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-edit"></i> Producto actualizado exitosamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-trash"></i> Producto eliminado exitosamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['qr_generated'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-qrcode"></i> ¡Código QR generado exitosamente!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['qr_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> Error al generar el código QR.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- 🔍 Filtros -->
    <div class="row mb-4">
        <div class="col-md-6 mb-2">
            <input type="text" id="filtroNombre" class="form-control" placeholder="Buscar por nombre...">
        </div>
        <div class="col-md-4 mb-2">
            <input type="text" id="filtroCategoria" class="form-control" placeholder="Buscar por categoría...">
        </div>
        <div class="col-md-2 mb-2">
            <button class="btn btn-primary w-100" onclick="aplicarFiltros()">
                <i class="fas fa-filter"></i> Filtrar
            </button>
        </div>
    </div>

    <!-- Tabla de productos -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0" id="tablaProductos">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Categoría</th>
                            <th>QR</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productos)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay productos registrados</p>
                                    <a href="?c=producto&a=create" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Crear primer producto
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($productos as $prod): ?>
                                <tr>
                                    <td class="fw-bold"><?= $prod->getIdProducto() ?></td>
                                    <td class="text-center">
                                        <?php if ($prod->getImagenUrl()): ?>
                                            <img src="<?= htmlspecialchars($prod->getImagenUrl()) ?>" 
                                                 alt="<?= htmlspecialchars($prod->getNombre()) ?>" 
                                                 style="width: 50px; height: 40px; object-fit: cover; border-radius: 5px;" 
                                                 title="<?= htmlspecialchars($prod->getNombre()) ?>"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                            <span class="text-muted" style="display: none;" title="Error al cargar imagen">
                                                <i class="fas fa-image"></i>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted" title="Sin imagen">
                                                <i class="fas fa-image"></i>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($prod->getNombre()) ?></td>
                                    <td class="text-success fw-bold">$<?= number_format($prod->getPrecio(), 2) ?></td>
                                    <td><?= htmlspecialchars($prod->getIdCategoria()) ?></td>
                                    <td class="text-center">
                                        <?php if ($prod->getCodigoQr() && file_exists($prod->getCodigoQr())): ?>
                                            <img src="<?= $prod->getCodigoQr() ?>" alt="QR" style="width: 40px; height: 40px;" title="Código QR disponible">
                                        <?php else: ?>
                                            <span class="text-muted" title="Sin código QR">
                                                <i class="fas fa-qrcode"></i>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="?c=carrito&a=agregar&id=<?= $prod->getIdProducto() ?>" 
                                               class="btn btn-sm btn-success" title="Agregar al carrito">
                                                <i class="fas fa-cart-plus"></i>
                                            </a>
                                            <a href="?c=producto&a=edit&id=<?= $prod->getIdProducto() ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?c=producto&a=viewQR&id=<?= $prod->getIdProducto() ?>" 
                                               class="btn btn-sm btn-outline-success" title="Ver código QR">
                                                <i class="fas fa-qrcode"></i>
                                            </a>
                                            <a href="vistas/Productos/ver_imagenes.php?nombre=<?= $prod->getNombre() ?>" 
                                               class="btn btn-sm btn-outline-info" title="Ver imágenes">
                                                <i class="fas fa-images"></i>
                                            </a>
                                            <a href="?c=producto&a=delete&id=<?= $prod->getIdProducto() ?>" 
                                               class="btn btn-sm btn-outline-danger" title="Eliminar"
                                               onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?')">
                                                <i class="fas fa-trash"></i>
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

    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Paginación de productos">
            <ul class="pagination" id="paginacion"></ul>
        </nav>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const filas = Array.from(document.querySelectorAll("#tablaProductos tbody tr"))
                        .filter(f => !f.querySelector('td[colspan]'));
    const filasPorPagina = 10;
    const paginacionUl = document.getElementById("paginacion");

    function mostrarPagina(pagina, filasVisibles) {
        const inicio = (pagina - 1) * filasPorPagina;
        const fin = inicio + filasPorPagina;
        filas.forEach(f => f.style.display = "none");
        filasVisibles.slice(inicio, fin).forEach(f => f.style.display = "");

        // Paginación
        const totalPaginas = Math.ceil(filasVisibles.length / filasPorPagina);
        paginacionUl.innerHTML = "";
        if (totalPaginas <= 1) return;

        const crearLi = (num, texto = null, disabled = false, active = false) => {
            const li = document.createElement("li");
            li.className = "page-item" + (disabled ? " disabled" : "") + (active ? " active" : "");
            li.innerHTML = `<a class="page-link" href="#">${texto || num}</a>`;
            li.querySelector("a").addEventListener("click", e => {
                e.preventDefault();
                if (!disabled && !active) mostrarPagina(num, filasVisibles);
            });
            return li;
        };

        paginacionUl.appendChild(crearLi(1 - 1, "Anterior", 1 === 1));
        for (let i = 1; i <= totalPaginas; i++) {
            paginacionUl.appendChild(crearLi(i, null, false, i === 1));
        }
        paginacionUl.appendChild(crearLi(totalPaginas, "Siguiente", totalPaginas === 1));
    }

    function aplicarFiltros() {
        const filtroNombre = document.getElementById("filtroNombre").value.toLowerCase();
        const filtroCategoria = document.getElementById("filtroCategoria").value.toLowerCase();

        const filasFiltradas = filas.filter(fila => {
            const nombre = fila.cells[1].innerText.toLowerCase();
            const categoria = fila.dataset.categoria.toLowerCase();
            return nombre.includes(filtroNombre) && categoria.includes(filtroCategoria);
        });

        mostrarPagina(1, filasFiltradas);
    }

    // Inicial
    mostrarPagina(1, filas);
    window.aplicarFiltros = aplicarFiltros;
});
</script>

<?php include 'layout/footer.php'; ?>
