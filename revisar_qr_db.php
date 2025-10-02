<?php
require_once 'config/cn.php';

echo "<h2>🔍 Revisión de Códigos QR en Base de Datos</h2>";

try {
    $cn = new CN();
    $sql = "SELECT id_producto, nombre, codigo_qr FROM Productos ORDER BY id_producto";
    $productos = $cn->consulta($sql);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>ID</th><th>Nombre</th><th>Código QR</th><th>Tipo</th><th>Acción</th>";
    echo "</tr>";
    
    foreach ($productos as $producto) {
        echo "<tr>";
        echo "<td>" . $producto['id_producto'] . "</td>";
        echo "<td>" . htmlspecialchars($producto['nombre']) . "</td>";
        echo "<td style='max-width: 300px; word-break: break-all;'>" . htmlspecialchars($producto['codigo_qr'] ?? 'NULL') . "</td>";
        
        $qr = $producto['codigo_qr'];
        if (!$qr) {
            echo "<td style='color: gray;'>Sin QR</td>";
            echo "<td>-</td>";
        } elseif (strpos($qr, 'http') === 0) {
            echo "<td style='color: orange;'>URL</td>";
            echo "<td><button onclick=\"regenerarQR(" . $producto['id_producto'] . ")\">Regenerar</button></td>";
        } elseif (strpos($qr, '{') !== false) {
            echo "<td style='color: red;'>JSON</td>";
            echo "<td><button onclick=\"regenerarQR(" . $producto['id_producto'] . ")\">Regenerar</button></td>";
        } elseif (file_exists($qr)) {
            echo "<td style='color: green;'>Archivo OK</td>";
            echo "<td>✓</td>";
        } else {
            echo "<td style='color: red;'>Archivo perdido</td>";
            echo "<td><button onclick=\"regenerarQR(" . $producto['id_producto'] . ")\">Regenerar</button></td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<style>
table { font-family: Arial, sans-serif; }
th, td { padding: 8px; text-align: left; }
button { background: #007cba; color: white; border: none; padding: 5px 10px; cursor: pointer; }
button:hover { background: #005a87; }
</style>

<script>
function regenerarQR(id) {
    if (confirm('¿Regenerar QR para producto ID ' + id + '?')) {
        window.location.href = 'regenerar_qr_individual.php?id=' + id;
    }
}
</script>

<br>
<a href="fix_qr_codes.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">🔧 Regenerar TODOS los QRs</a>
<a href="?c=producto&a=index" style="background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;">← Volver</a>