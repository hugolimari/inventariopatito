<?php
/**
 * View to display a single hardware item
 * Variables: item, title
 */

function h($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= h($title ?? APP_NAME) ?></title>
</head>
<body>
    <h1>Detalle de Hardware</h1>
    <?php if ($item): ?>
    <ul>
        <li>ID: <?= h($item['id_hardware']) ?></li>
        <li>Marca: <?= h($item['marca_nombre'] ?? '') ?></li>
        <li>Modelo: <?= h($item['modelo']) ?></li>
        <li>Categoría: <?= h($item['categoria_nombre']) ?></li>
        <li>Precio: <?= number_format($item['precio'],2) ?></li>
        <li>Stock: <?= h($item['stock']) ?></li>
    </ul>
    <?php else: ?>
        <p>Hardware no encontrado.</p>
    <?php endif; ?>
    <a href="index.php">Volver</a>
</body>
</html>