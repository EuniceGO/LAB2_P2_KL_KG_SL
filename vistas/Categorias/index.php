<?php include 'layout/menu.php'; ?>

<div class="container mt-5">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-primary fw-bold">
            <i class="bi bi-folder2-open me-2"></i> Lista de Categorías
        </h1>
        <a href="?c=categoria&a=create" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Nueva Categoría
        </a>
    </div>

    <!-- Categoría con más productos -->
    <?php if ($categoriaTop): ?>
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="bi bi-bar-chart me-2"></i>
            <div>
                <strong>Categoría con más productos:</strong> 
                <?= htmlspecialchars($categoriaTop['nombre']) ?> 
                (<?= $categoriaTop['total_productos'] ?> productos)
            </div>
        </div>
    <?php endif; ?>

    <!-- Filtro por categoría -->
    <div class="mb-3">
        <input type="text" id="filtroCategoria" class="form-control w-50" 
               placeholder="Filtrar por nombre de categoría...">
    </div>

    <!-- Tabla de categorías -->
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-hover" id="tablaCategorias">
                    <thead class="table-primary text-center">
                        <tr>
                            <th style="width: 10%;">ID</th>
                            <th style="width: 25%;">Nombre</th>
                            <th style="width: 40%;">Descripción</th>
                            <th style="width: 25%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $cat): ?>
                            <tr>
                                <td class="text-center fw-semibold"><?= $cat->getIdCategoria() ?></td>
                                <td class="nombre-categoria"><?= htmlspecialchars($cat->getNombre()) ?></td>
                                <td><?= $cat->getDescripcion() ?></td>
                                <td class="text-center">
                                    <a href="?c=categoria&a=edit&id=<?= $cat->getIdCategoria() ?>" 
                                       class="btn btn-warning btn-sm me-1 shadow-sm">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                    <a href="?c=categoria&a=delete&id=<?= $cat->getIdCategoria() ?>" 
                                       class="btn btn-danger btn-sm me-1 shadow-sm"
                                       onclick="return confirm('¿Seguro que deseas eliminar esta categoría?')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </a>
                                    <a href="vistas/Categorias/productos.php?id=<?= $cat->getIdCategoria() ?>" 
                                       class="btn btn-info btn-sm shadow-sm">
                                        <i class="bi bi-box-seam"></i> Productos
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-3">
        <ul class="pagination" id="paginacionCategorias"></ul>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const filas = Array.from(document.querySelectorAll("#tablaCategorias tbody tr"));
    const filasPorPagina = 5; // Cambia según quieras
    const paginacionUl = document.getElementById("paginacionCategorias");
    let filasFiltradas = filas;

    // Función para mostrar una página específica
    function mostrarPagina(filasAMostrar, pagina) {
        const inicio = (pagina - 1) * filasPorPagina;
        const fin = inicio + filasPorPagina;

        filas.forEach(f => f.style.display = "none");
        filasAMostrar.slice(inicio, fin).forEach(f => f.style.display = "");

        // Construir paginación
        const totalPaginas = Math.ceil(filasAMostrar.length / filasPorPagina);
        paginacionUl.innerHTML = "";
        if (totalPaginas <= 1) return;

        const crearLi = (num, texto = null, disabled = false, active = false) => {
            const li = document.createElement("li");
            li.className = "page-item" + (disabled ? " disabled" : "") + (active ? " active" : "");
            li.innerHTML = `<a class="page-link" href="#">${texto || num}</a>`;
            li.querySelector("a").addEventListener("click", e => {
                e.preventDefault();
                if (!disabled && !active) mostrarPagina(filasAMostrar, num);
            });
            return li;
        };

        // Botón anterior
        paginacionUl.appendChild(crearLi(pagina - 1, "Anterior", pagina === 1));

        // Números de página
        for (let i = 1; i <= totalPaginas; i++) {
            paginacionUl.appendChild(crearLi(i, null, false, i === pagina));
        }

        // Botón siguiente
        paginacionUl.appendChild(crearLi(pagina + 1, "Siguiente", pagina === totalPaginas));
    }

    // Inicialmente mostramos la primera página
    mostrarPagina(filasFiltradas, 1);

    // Filtro en tiempo real
    document.getElementById("filtroCategoria").addEventListener("keyup", function() {
        const filtro = this.value.toLowerCase();
        filasFiltradas = filas.filter(fila => {
            const nombre = fila.querySelector(".nombre-categoria").innerText.toLowerCase();
            return nombre.includes(filtro);
        });
        mostrarPagina(filasFiltradas, 1); // Siempre mostrar la primera página al filtrar
    });
});
</script>

<?php include 'layout/footer.php'; ?>
