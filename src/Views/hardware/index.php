<?php
/**
 * Vista: Catálogo de Hardware con búsqueda y nuevos campos.
 * Variables: $items (array), $alertas (int), $query (string)
 * Se incluye dentro de layout.php.
 */
$msg = $_GET['msg'] ?? null;
?>

<?php if ($msg === 'creado'): ?>
    <div class="alert alert-success">✅ Hardware registrado exitosamente.</div>
<?php elseif ($msg === 'actualizado'): ?>
    <div class="alert alert-success">✅ Hardware actualizado correctamente.</div>
<?php elseif ($msg === 'eliminado'): ?>
    <div class="alert alert-success">🗑️ Hardware eliminado correctamente.</div>
<?php elseif ($msg === 'no_encontrado'): ?>
    <div class="alert alert-danger">❌ Hardware no encontrado.</div>
<?php endif; ?>

<!-- Header -->
<div class="page-header">
    <div>
        <h1>🖥️ Catálogo de Hardware</h1>
        <span class="subtitle">Inventario completo de componentes</span>
    </div>
    <a href="<?= BASE_URL ?>?controller=hardware&action=crear" class="btn btn-primary">➕ Agregar Hardware</a>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="number"><?= count($items) ?></div>
        <div class="label">Total Productos</div>
    </div>
    <div class="stat-card <?= $alertas > 0 ? 'warning' : '' ?>">
        <div class="number"><?= $alertas ?></div>
        <div class="label">Stock Crítico</div>
    </div>
</div>

<!-- Search -->
<form method="GET" action="<?= BASE_URL ?>" class="search-bar">
    <input type="hidden" name="controller" value="hardware">
    <input type="hidden" name="action" value="index">
    <input type="text" name="q" placeholder="🔍 Buscar por marca, modelo o categoría..."
           value="<?= htmlspecialchars($query ?? '') ?>">
    <button type="submit" class="btn btn-primary">Buscar</button>
    <?php if (!empty($query)): ?>
        <a href="<?= BASE_URL ?>?controller=hardware&action=index" class="btn btn-secondary">Limpiar</a>
    <?php endif; ?>
</form>

<?php if (!empty($query)): ?>
    <p style="color: var(--text-secondary); font-size: 0.88em; margin-bottom: 16px;">
        Mostrando resultados para: <strong style="color: var(--accent-light)">"<?= htmlspecialchars($query) ?>"</strong>
        (<?= count($items) ?> encontrados)
    </p>
<?php endif; ?>

<!-- Table -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Etiquetado</th>
                <th>Vida Útil</th>
                <th>Estado</th>
                <th>Detalles</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr>
                    <td colspan="11" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        No se encontraron productos.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <tr <?php if ($item->esStockCritico()): ?>class="stock-critico"<?php endif; ?>>
                        <td><?= $item->getId() ?></td>
                        <td><?= htmlspecialchars($item->getMarca()) ?></td>
                        <td><strong><?= htmlspecialchars($item->getModelo()) ?></strong></td>
                        <td><?= htmlspecialchars($item->getCategoria()) ?></td>
                        <td>$<?= number_format($item->getPrecio(), 2) ?></td>
                        <td>
                            <span class="badge <?= $item->esStockCritico() ? 'badge-danger' : 'badge-success' ?>">
                                <?= $item->getStock() ?> uds. <?= $item->esStockCritico() ? '⚠️' : '✅' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($item->getEtiquetado()): ?>
                                <span class="badge badge-success">Sí ✔</span>
                            <?php else: ?>
                                <span class="badge badge-warning">No</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $item->getVidaUtilMeses() ?> meses</td>
                        <td>
                            <?php
                            $estadoClase = match($item->getEstado()) {
                                'Inventariado' => 'badge-success',
                                'Llegada'      => 'badge-info',
                                'Baja'         => 'badge-danger',
                                default        => 'badge-info'
                            };
                            ?>
                            <span class="badge <?= $estadoClase ?>"><?= htmlspecialchars($item->getEstado()) ?></span>
                        </td>
                        <td><?= htmlspecialchars($item->obtenerDetallesTecnicos()) ?></td>
                        <td style="white-space: nowrap;">
                            <a href="<?= BASE_URL ?>?controller=hardware&action=editar&id=<?= $item->getId() ?>"
                               class="btn btn-secondary btn-sm">✏️ Editar</a>
                            <a href="<?= BASE_URL ?>?controller=hardware&action=eliminar&id=<?= $item->getId() ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('¿Eliminar <?= htmlspecialchars($item->getModelo()) ?>?')">
                                🗑️
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
