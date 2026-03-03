<?php
/**
 * Vista: Formulario para crear hardware (6 tipos).
 * Variables: $datos (array), $errores (array)
 */
?>

<div class="page-header">
    <div>
        <h1>➕ Agregar Nuevo Hardware</h1>
        <span class="subtitle">Registrar un componente en el inventario</span>
    </div>
    <a href="<?= BASE_URL ?>?controller=hardware&action=index" class="btn btn-secondary">← Volver al catálogo</a>
</div>

<div class="card">
    <form method="POST" action="<?= BASE_URL ?>?controller=hardware&action=guardar">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <!-- Selector de tipo (6 opciones) -->
        <div class="form-group">
            <label>Tipo de Hardware <span class="required">*</span></label>
            <div class="tipo-selector" style="flex-wrap: wrap;">
                <div class="tipo-option active" data-tipo="Procesador" onclick="seleccionarTipo('Procesador')">
                    <div class="icon">🧠</div><div class="name">Procesador</div>
                </div>
                <div class="tipo-option" data-tipo="TarjetaGrafica" onclick="seleccionarTipo('TarjetaGrafica')">
                    <div class="icon">🎮</div><div class="name">Tarjeta Gráfica</div>
                </div>
                <div class="tipo-option" data-tipo="MemoriaRAM" onclick="seleccionarTipo('MemoriaRAM')">
                    <div class="icon">🧩</div><div class="name">Memoria RAM</div>
                </div>
                <div class="tipo-option" data-tipo="Almacenamiento" onclick="seleccionarTipo('Almacenamiento')">
                    <div class="icon">💾</div><div class="name">Almacenamiento</div>
                </div>
                <div class="tipo-option" data-tipo="PlacaBase" onclick="seleccionarTipo('PlacaBase')">
                    <div class="icon">🔧</div><div class="name">Placa Base</div>
                </div>
                <div class="tipo-option" data-tipo="FuentePoder" onclick="seleccionarTipo('FuentePoder')">
                    <div class="icon">⚡</div><div class="name">Fuente de Poder</div>
                </div>
            </div>
            <input type="hidden" name="tipo" id="tipoInput" value="<?= htmlspecialchars($datos['tipo'] ?? 'Procesador') ?>">
        </div>

        <!-- Categoría auto (solo lectura) -->
        <div class="form-group">
            <label>Categoría (automática)</label>
            <input type="text" id="categoriaDisplay" value="CPU" readonly style="opacity: 0.7; cursor: not-allowed;">
        </div>

        <!-- Campos comunes -->
        <div class="form-grid">
            <div class="form-group <?= isset($errores['marca']) ? 'has-error' : '' ?>">
                <label>Marca <span class="required">*</span></label>
                <input type="text" name="marca" value="<?= htmlspecialchars($datos['marca'] ?? '') ?>" placeholder="Ej: AMD, Intel, NVIDIA">
                <?php if (isset($errores['marca'])): ?><?php foreach ($errores['marca'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
            </div>
            <div class="form-group <?= isset($errores['modelo']) ? 'has-error' : '' ?>">
                <label>Modelo <span class="required">*</span></label>
                <input type="text" name="modelo" value="<?= htmlspecialchars($datos['modelo'] ?? '') ?>" placeholder="Ej: Ryzen 5 5600X">
                <?php if (isset($errores['modelo'])): ?><?php foreach ($errores['modelo'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group <?= isset($errores['precio']) ? 'has-error' : '' ?>">
                <label>Precio (USD) <span class="required">*</span></label>
                <input type="number" name="precio" step="0.01" min="0.01" value="<?= $datos['precio'] ?? '' ?>" placeholder="199.99">
                <?php if (isset($errores['precio'])): ?><?php foreach ($errores['precio'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
            </div>
            <div class="form-group <?= isset($errores['stock']) ? 'has-error' : '' ?>">
                <label>Stock <span class="required">*</span></label>
                <input type="number" name="stock" min="0" value="<?= $datos['stock'] ?? '' ?>" placeholder="10">
                <?php if (isset($errores['stock'])): ?><?php foreach ($errores['stock'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group <?= isset($errores['estado']) ? 'has-error' : '' ?>">
                <label>Estado <span class="required">*</span></label>
                <select name="estado">
                    <option value="Llegada" <?= ($datos['estado'] ?? 'Llegada') === 'Llegada' ? 'selected' : '' ?>>Llegada</option>
                    <option value="Inventariado" <?= ($datos['estado'] ?? '') === 'Inventariado' ? 'selected' : '' ?>>Inventariado</option>
                    <option value="Baja" <?= ($datos['estado'] ?? '') === 'Baja' ? 'selected' : '' ?>>Baja</option>
                </select>
            </div>
            <div class="form-group">
                <label>Vida Útil (meses)</label>
                <input type="number" name="vida_util_meses" min="1" value="<?= $datos['vida_util_meses'] ?? 36 ?>" placeholder="36">
            </div>
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" id="etiquetado" name="etiquetado" <?= ($datos['etiquetado'] ?? false) ? 'checked' : '' ?>>
                <label for="etiquetado">Etiquetado ✔</label>
            </div>
        </div>

        <!-- ═══ CAMPOS ESPECÍFICOS POR TIPO ═══ -->

        <!-- Procesador -->
        <div id="campos-Procesador" class="campos-especificos">
            <h3>🧠 Especificaciones del Procesador</h3>
            <div class="form-grid">
                <div class="form-group <?= isset($errores['nucleos']) ? 'has-error' : '' ?>">
                    <label>Núcleos <span class="required">*</span></label>
                    <input type="number" name="nucleos" min="1" value="<?= $datos['nucleos'] ?? '' ?>" placeholder="6">
                    <?php if (isset($errores['nucleos'])): ?><?php foreach ($errores['nucleos'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
                </div>
                <div class="form-group <?= isset($errores['frecuencia']) ? 'has-error' : '' ?>">
                    <label>Frecuencia <span class="required">*</span></label>
                    <input type="text" name="frecuencia" value="<?= htmlspecialchars($datos['frecuencia'] ?? '') ?>" placeholder="5.3GHz">
                    <?php if (isset($errores['frecuencia'])): ?><?php foreach ($errores['frecuencia'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tarjeta Gráfica -->
        <div id="campos-TarjetaGrafica" class="campos-especificos hidden">
            <h3>🎮 Especificaciones de la Tarjeta Gráfica</h3>
            <div class="form-group <?= isset($errores['vram']) ? 'has-error' : '' ?>">
                <label>VRAM <span class="required">*</span></label>
                <input type="text" name="vram" value="<?= htmlspecialchars($datos['vram'] ?? '') ?>" placeholder="12GB GDDR6X">
                <?php if (isset($errores['vram'])): ?><?php foreach ($errores['vram'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
            </div>
        </div>

        <!-- Memoria RAM -->
        <div id="campos-MemoriaRAM" class="campos-especificos hidden">
            <h3>🧩 Especificaciones de Memoria RAM</h3>
            <div class="form-grid">
                <div class="form-group <?= isset($errores['ram_capacidad']) ? 'has-error' : '' ?>">
                    <label>Capacidad <span class="required">*</span></label>
                    <input type="text" name="ram_capacidad" value="<?= htmlspecialchars($datos['ram_capacidad'] ?? '') ?>" placeholder="32GB (2x16GB)">
                    <?php if (isset($errores['ram_capacidad'])): ?><?php foreach ($errores['ram_capacidad'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
                </div>
                <div class="form-group <?= isset($errores['ram_tipo']) ? 'has-error' : '' ?>">
                    <label>Tipo <span class="required">*</span></label>
                    <input type="text" name="ram_tipo" value="<?= htmlspecialchars($datos['ram_tipo'] ?? '') ?>" placeholder="DDR5">
                    <?php if (isset($errores['ram_tipo'])): ?><?php foreach ($errores['ram_tipo'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
                </div>
            </div>
            <div class="form-group <?= isset($errores['ram_velocidad']) ? 'has-error' : '' ?>">
                <label>Velocidad <span class="required">*</span></label>
                <input type="text" name="ram_velocidad" value="<?= htmlspecialchars($datos['ram_velocidad'] ?? '') ?>" placeholder="6000MHz">
                <?php if (isset($errores['ram_velocidad'])): ?><?php foreach ($errores['ram_velocidad'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
            </div>
        </div>

        <!-- Almacenamiento -->
        <div id="campos-Almacenamiento" class="campos-especificos hidden">
            <h3>💾 Especificaciones de Almacenamiento</h3>
            <div class="form-grid">
                <div class="form-group <?= isset($errores['alm_capacidad']) ? 'has-error' : '' ?>">
                    <label>Capacidad <span class="required">*</span></label>
                    <input type="text" name="alm_capacidad" value="<?= htmlspecialchars($datos['alm_capacidad'] ?? '') ?>" placeholder="1TB">
                    <?php if (isset($errores['alm_capacidad'])): ?><?php foreach ($errores['alm_capacidad'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
                </div>
                <div class="form-group <?= isset($errores['alm_tipo']) ? 'has-error' : '' ?>">
                    <label>Tipo <span class="required">*</span></label>
                    <input type="text" name="alm_tipo" value="<?= htmlspecialchars($datos['alm_tipo'] ?? '') ?>" placeholder="NVMe PCIe 4.0">
                    <?php if (isset($errores['alm_tipo'])): ?><?php foreach ($errores['alm_tipo'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label>Velocidad de Lectura</label>
                <input type="text" name="alm_velocidad" value="<?= htmlspecialchars($datos['alm_velocidad'] ?? '') ?>" placeholder="7000MB/s">
            </div>
        </div>

        <!-- Placa Base -->
        <div id="campos-PlacaBase" class="campos-especificos hidden">
            <h3>🔧 Especificaciones de Placa Base</h3>
            <div class="form-grid">
                <div class="form-group <?= isset($errores['socket']) ? 'has-error' : '' ?>">
                    <label>Socket <span class="required">*</span></label>
                    <input type="text" name="socket" value="<?= htmlspecialchars($datos['socket'] ?? '') ?>" placeholder="AM5">
                    <?php if (isset($errores['socket'])): ?><?php foreach ($errores['socket'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
                </div>
                <div class="form-group <?= isset($errores['formato']) ? 'has-error' : '' ?>">
                    <label>Formato <span class="required">*</span></label>
                    <input type="text" name="formato" value="<?= htmlspecialchars($datos['formato'] ?? '') ?>" placeholder="ATX">
                    <?php if (isset($errores['formato'])): ?><?php foreach ($errores['formato'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Fuente de Poder -->
        <div id="campos-FuentePoder" class="campos-especificos hidden">
            <h3>⚡ Especificaciones de Fuente de Poder</h3>
            <div class="form-grid">
                <div class="form-group <?= isset($errores['potencia']) ? 'has-error' : '' ?>">
                    <label>Potencia <span class="required">*</span></label>
                    <input type="text" name="potencia" value="<?= htmlspecialchars($datos['potencia'] ?? '') ?>" placeholder="850W">
                    <?php if (isset($errores['potencia'])): ?><?php foreach ($errores['potencia'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
                </div>
                <div class="form-group <?= isset($errores['certificacion']) ? 'has-error' : '' ?>">
                    <label>Certificación <span class="required">*</span></label>
                    <input type="text" name="certificacion" value="<?= htmlspecialchars($datos['certificacion'] ?? '') ?>" placeholder="80 Plus Gold">
                    <?php if (isset($errores['certificacion'])): ?><?php foreach ($errores['certificacion'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?><?php endif; ?>
                </div>
            </div>
        </div>

        <div style="margin-top: 28px; display: flex; gap: 10px;">
            <a href="<?= BASE_URL ?>?controller=hardware&action=index" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Guardar Hardware</button>
        </div>
    </form>
</div>

<script>
const tipoCat = {
    'Procesador':'CPU', 'TarjetaGrafica':'GPU', 'MemoriaRAM':'RAM',
    'Almacenamiento':'SSD', 'PlacaBase':'Motherboard', 'FuentePoder':'PSU'
};
const tipos = Object.keys(tipoCat);

function seleccionarTipo(tipo) {
    document.getElementById('tipoInput').value = tipo;
    document.getElementById('categoriaDisplay').value = tipoCat[tipo] || tipo;
    document.querySelectorAll('.tipo-option').forEach(el => {
        el.classList.toggle('active', el.dataset.tipo === tipo);
    });
    tipos.forEach(t => {
        const el = document.getElementById('campos-' + t);
        if (el) el.classList.toggle('hidden', t !== tipo);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    seleccionarTipo(document.getElementById('tipoInput').value);
});
</script>
