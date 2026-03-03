<?php
/**
 * Vista: Formulario para editar hardware.
 * Variables: $id (int), $datos (array), $errores (array)
 * Se incluye dentro de layout.php.
 */
?>

<div class="page-header">
    <div>
        <h1>✏️ Editar Hardware #<?= $id ?? ($datos['id'] ?? '') ?></h1>
        <span class="subtitle">Modificar datos del componente</span>
    </div>
    <a href="<?= BASE_URL ?>?controller=hardware&action=index" class="btn btn-secondary">← Volver al catálogo</a>
</div>

<div class="card">
    <form method="POST" action="<?= BASE_URL ?>?controller=hardware&action=actualizar">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <input type="hidden" name="id" value="<?= $id ?? 0 ?>">

        <!-- Tipo (solo lectura, se muestra como info) -->
        <div class="form-group">
            <label>Tipo de Hardware</label>
            <div class="tipo-selector">
                <div class="tipo-option <?= ($datos['tipo'] ?? '') === 'procesador' ? 'active' : '' ?>" data-tipo="procesador" onclick="seleccionarTipo('procesador')">
                    <div class="icon">🧠</div>
                    <div class="name">Procesador</div>
                </div>
                <div class="tipo-option <?= ($datos['tipo'] ?? '') === 'tarjeta_grafica' ? 'active' : '' ?>" data-tipo="tarjeta_grafica" onclick="seleccionarTipo('tarjeta_grafica')">
                    <div class="icon">🎮</div>
                    <div class="name">Tarjeta Gráfica</div>
                </div>
            </div>
            <input type="hidden" name="tipo" id="tipoInput" value="<?= htmlspecialchars($datos['tipo'] ?? 'procesador') ?>">
        </div>

        <!-- Campos comunes -->
        <div class="form-grid">
            <div class="form-group <?= isset($errores['marca']) ? 'has-error' : '' ?>">
                <label>Marca <span class="required">*</span></label>
                <input type="text" name="marca" value="<?= htmlspecialchars($datos['marca'] ?? '') ?>" placeholder="Ej: AMD, Intel, NVIDIA">
                <?php if (isset($errores['marca'])): ?>
                    <?php foreach ($errores['marca'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($errores['modelo']) ? 'has-error' : '' ?>">
                <label>Modelo <span class="required">*</span></label>
                <input type="text" name="modelo" value="<?= htmlspecialchars($datos['modelo'] ?? '') ?>" placeholder="Ej: Ryzen 5 5600X">
                <?php if (isset($errores['modelo'])): ?>
                    <?php foreach ($errores['modelo'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group <?= isset($errores['precio']) ? 'has-error' : '' ?>">
                <label>Precio (USD) <span class="required">*</span></label>
                <input type="number" name="precio" step="0.01" min="0.01" value="<?= $datos['precio'] ?? '' ?>" placeholder="199.99">
                <?php if (isset($errores['precio'])): ?>
                    <?php foreach ($errores['precio'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($errores['stock']) ? 'has-error' : '' ?>">
                <label>Stock <span class="required">*</span></label>
                <input type="number" name="stock" min="0" value="<?= $datos['stock'] ?? '' ?>" placeholder="10">
                <?php if (isset($errores['stock'])): ?>
                    <?php foreach ($errores['stock'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group <?= isset($errores['categoria']) ? 'has-error' : '' ?>">
                <label>Categoría <span class="required">*</span></label>
                <select name="categoria">
                    <option value="">-- Seleccionar --</option>
                    <option value="CPU" <?= ($datos['categoria'] ?? '') === 'CPU' ? 'selected' : '' ?>>CPU</option>
                    <option value="GPU" <?= ($datos['categoria'] ?? '') === 'GPU' ? 'selected' : '' ?>>GPU</option>
                    <option value="RAM" <?= ($datos['categoria'] ?? '') === 'RAM' ? 'selected' : '' ?>>RAM</option>
                    <option value="Almacenamiento" <?= ($datos['categoria'] ?? '') === 'Almacenamiento' ? 'selected' : '' ?>>Almacenamiento</option>
                </select>
                <?php if (isset($errores['categoria'])): ?>
                    <?php foreach ($errores['categoria'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($errores['estado']) ? 'has-error' : '' ?>">
                <label>Estado <span class="required">*</span></label>
                <select name="estado">
                    <option value="Llegada" <?= ($datos['estado'] ?? '') === 'Llegada' ? 'selected' : '' ?>>Llegada</option>
                    <option value="Inventariado" <?= ($datos['estado'] ?? '') === 'Inventariado' ? 'selected' : '' ?>>Inventariado</option>
                    <option value="Baja" <?= ($datos['estado'] ?? '') === 'Baja' ? 'selected' : '' ?>>Baja</option>
                </select>
                <?php if (isset($errores['estado'])): ?>
                    <?php foreach ($errores['estado'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Vida Útil (meses)</label>
                <input type="number" name="vida_util_meses" min="1" value="<?= $datos['vida_util_meses'] ?? 36 ?>" placeholder="36">
            </div>
            <div class="form-group">
                <div class="checkbox-group" style="margin-top: 28px;">
                    <input type="checkbox" id="etiquetado" name="etiquetado" <?= ($datos['etiquetado'] ?? false) ? 'checked' : '' ?>>
                    <label for="etiquetado">Etiquetado ✔</label>
                </div>
            </div>
        </div>

        <!-- Campos Procesador -->
        <div id="campos-procesador" class="campos-especificos">
            <h3>🧠 Especificaciones del Procesador</h3>
            <div class="form-grid">
                <div class="form-group <?= isset($errores['nucleos']) ? 'has-error' : '' ?>">
                    <label>Número de Núcleos <span class="required">*</span></label>
                    <input type="number" name="nucleos" min="1" value="<?= $datos['nucleos'] ?? '' ?>" placeholder="6">
                    <?php if (isset($errores['nucleos'])): ?>
                        <?php foreach ($errores['nucleos'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="form-group <?= isset($errores['frecuencia']) ? 'has-error' : '' ?>">
                    <label>Frecuencia <span class="required">*</span></label>
                    <input type="text" name="frecuencia" value="<?= htmlspecialchars($datos['frecuencia'] ?? '') ?>" placeholder="4.6GHz">
                    <?php if (isset($errores['frecuencia'])): ?>
                        <?php foreach ($errores['frecuencia'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Campos Tarjeta Gráfica -->
        <div id="campos-tarjeta" class="campos-especificos hidden">
            <h3>🎮 Especificaciones de la Tarjeta Gráfica</h3>
            <div class="form-group <?= isset($errores['vram']) ? 'has-error' : '' ?>">
                <label>VRAM <span class="required">*</span></label>
                <input type="text" name="vram" value="<?= htmlspecialchars($datos['vram'] ?? '') ?>" placeholder="8GB GDDR6">
                <?php if (isset($errores['vram'])): ?>
                    <?php foreach ($errores['vram'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div style="margin-top: 28px; display: flex; gap: 10px;">
            <a href="<?= BASE_URL ?>?controller=hardware&action=index" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Actualizar Hardware</button>
        </div>
    </form>
</div>

<script>
function seleccionarTipo(tipo) {
    document.getElementById('tipoInput').value = tipo;
    document.querySelectorAll('.tipo-option').forEach(el => {
        el.classList.toggle('active', el.dataset.tipo === tipo);
    });
    const cpu = document.getElementById('campos-procesador');
    const gpu = document.getElementById('campos-tarjeta');
    if (tipo === 'procesador') {
        cpu.classList.remove('hidden');
        gpu.classList.add('hidden');
    } else {
        cpu.classList.add('hidden');
        gpu.classList.remove('hidden');
    }
}
document.addEventListener('DOMContentLoaded', () => {
    seleccionarTipo(document.getElementById('tipoInput').value);
});
</script>
