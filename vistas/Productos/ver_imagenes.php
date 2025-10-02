<?php
// vistas/Productos/ver_imagenes.php
require_once '../../modelos/ProductoModel.php';

$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : null;
if (!$nombre) {
    echo "Nombre de producto no especificado.";
    exit;
}


$model = new ProductoModel();
$imagen_producto = $model->getProductosByNombre($nombre);

if (empty($imagen_producto)) {
    echo "No hay imágenes para este producto.";
    exit;
}


?>
<h1>Imágenes del producto: <?= htmlspecialchars($nombre) ?></h1>
<ul>
<?php foreach ($imagen_producto as $img): ?>
    <li>
        <img src="<?= htmlspecialchars($img['url_imagen']) ?>" alt="Imagen de <?= htmlspecialchars($img['nombre']) ?>" style="max-width:200px;">
    </li>
<?php endforeach; ?>
</ul>
<a href="index.php">Volver a productos</a>