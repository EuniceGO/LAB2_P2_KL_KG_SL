<?php
include('../../layout/menu.php');
require_once('../../modelos/CategoriaModel.php');
require_once '../../modelos/ProductoModel.php';

$categoria_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$categoria_id) {
    echo "ID de categoría no especificado.";
    exit;
}

$categoriaModel = new CategoriaModel();
$categoria = $categoriaModel->getById($categoria_id);
$productos = $categoriaModel->getProductosByCategoria($categoria_id);
?>
 <h1>Productos de la categoría: <?php echo htmlspecialchars($categoria->getNombre()); ?></h1>
    <?php if (empty($productos)): ?>
        <p>No hay productos en esta categoría.</p>
    <?php else: ?>
        <ul>
        <?php foreach ($productos as $producto): ?>
            <li>
                <?php echo htmlspecialchars($producto['nombre']); ?> - $<?php echo htmlspecialchars($producto['precio']); ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <a href="index.php">Volver a categorías</a>
