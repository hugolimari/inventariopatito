<?php
declare(strict_types=1);

/**
 * HU 03 — Formulario de ingreso de hardware.
 * Crea un Procesador o TarjetaGrafica según la selección del usuario.
 */

session_start();

// Cargar configuración y autoloader
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\Procesador;
use App\Models\TarjetaGrafica;
use App\Repositories\HardwareRepository;

// Generar token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$datos = [];
$errores = [];
$exito = false;

// ── Procesar formulario POST ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verificar CSRF
    $tokenRecibido = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $tokenRecibido)) {
        die('Token CSRF inválido');
    }

    // Obtener y sanitizar datos comunes
    $datos = [
        'tipo'      => $_POST['tipo'] ?? 'procesador',
        'marca'     => htmlspecialchars(trim($_POST['marca'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'modelo'    => htmlspecialchars(trim($_POST['modelo'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'precio'    => (float) ($_POST['precio'] ?? 0),
        'stock'     => (int) ($_POST['stock'] ?? 0),
        'categoria' => htmlspecialchars(trim($_POST['categoria'] ?? ''), ENT_QUOTES, 'UTF-8'),
        // Campos específicos
        'nucleos'     => (int) ($_POST['nucleos'] ?? 0),
        'frecuencia'  => htmlspecialchars(trim($_POST['frecuencia'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'vram'        => htmlspecialchars(trim($_POST['vram'] ?? ''), ENT_QUOTES, 'UTF-8'),
    ];

    // ── Validaciones ────────────────────────────────────────

    if (empty($datos['marca'])) {
        $errores['marca'][] = 'La marca es requerida';
    }

    if (empty($datos['modelo'])) {
        $errores['modelo'][] = 'El modelo es requerido';
    }

    if ($datos['precio'] <= 0) {
        $errores['precio'][] = 'El precio debe ser mayor a 0';
    }

    if ($datos['stock'] < 0) {
        $errores['stock'][] = 'El stock no puede ser negativo';
    }

    if (empty($datos['categoria'])) {
        $errores['categoria'][] = 'La categoría es requerida';
    }

    // Validaciones específicas por tipo
    if ($datos['tipo'] === 'procesador') {
        if ($datos['nucleos'] <= 0) {
            $errores['nucleos'][] = 'El número de núcleos debe ser mayor a 0';
        }
        if (empty($datos['frecuencia'])) {
            $errores['frecuencia'][] = 'La frecuencia es requerida (ej: 4.3GHz)';
        }
    } elseif ($datos['tipo'] === 'tarjeta_grafica') {
        if (empty($datos['vram'])) {
            $errores['vram'][] = 'La VRAM es requerida (ej: 8GB GDDR6)';
        }
    }

    // ── Crear objeto y guardar ──────────────────────────────
    if (empty($errores)) {
        $repo = new HardwareRepository();

        if ($datos['tipo'] === 'procesador') {
            $item = new Procesador(
                id: null,
                marca: $datos['marca'],
                modelo: $datos['modelo'],
                precio: $datos['precio'],
                stock: $datos['stock'],
                categoria: $datos['categoria'],
                nucleos: $datos['nucleos'],
                frecuencia: $datos['frecuencia']
            );
        } else {
            $item = new TarjetaGrafica(
                id: null,
                marca: $datos['marca'],
                modelo: $datos['modelo'],
                precio: $datos['precio'],
                stock: $datos['stock'],
                categoria: $datos['categoria'],
                vram: $datos['vram']
            );
        }

        $repo->save($item);
        $exito = true;

        // Regenerar token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Hardware - <?= APP_NAME ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
        }

        .header h1 {
            font-size: 1.8em;
            margin-bottom: 5px;
        }

        .content {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }

        .required {
            color: #dc3545;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }

        .has-error input,
        .has-error select {
            border-color: #dc3545;
        }

        .error {
            color: #dc3545;
            font-size: 0.85em;
            margin-top: 5px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .buttons {
            margin-top: 30px;
            display: flex;
            gap: 10px;
        }

        /* ── Campos condicionales ────────────── */
        .campos-especificos {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 10px;
            border: 2px dashed #667eea;
        }

        .campos-especificos h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.1em;
        }

        .hidden {
            display: none;
        }

        .tipo-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .tipo-option {
            flex: 1;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .tipo-option:hover {
            border-color: #667eea;
            background: #f0f0ff;
        }

        .tipo-option.active {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
        }

        .tipo-option .icon {
            font-size: 2em;
            margin-bottom: 8px;
        }

        .tipo-option .name {
            font-weight: 600;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>➕ Agregar Nuevo Hardware</h1>
            <p><?= APP_NAME ?> — Módulo de Inventario</p>
        </div>

        <div class="content">
            <?php if ($exito): ?>
                <div class="success-message">
                    <strong>✅ Hardware registrado exitosamente</strong>
                    <p>Se ha registrado: <strong><?= htmlspecialchars($datos['marca'] . ' ' . $datos['modelo']) ?></strong>
                       (<?= $datos['tipo'] === 'procesador' ? 'Procesador' : 'Tarjeta Gráfica' ?>)</p>
                </div>
                <div class="buttons">
                    <a href="index.php" class="btn btn-primary">← Volver al Catálogo</a>
                    <a href="crear.php" class="btn btn-secondary">➕ Agregar Otro</a>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <!-- Selector de tipo con cards visuales -->
                    <label>Tipo de Hardware <span class="required">*</span></label>
                    <div class="tipo-selector">
                        <div class="tipo-option active" data-tipo="procesador" onclick="seleccionarTipo('procesador')">
                            <div class="icon">🧠</div>
                            <div class="name">Procesador</div>
                        </div>
                        <div class="tipo-option" data-tipo="tarjeta_grafica" onclick="seleccionarTipo('tarjeta_grafica')">
                            <div class="icon">🎮</div>
                            <div class="name">Tarjeta Gráfica</div>
                        </div>
                    </div>
                    <input type="hidden" name="tipo" id="tipoInput" value="<?= htmlspecialchars($datos['tipo'] ?? 'procesador') ?>">

                    <!-- Campos comunes -->
                    <div class="form-grid">
                        <div class="form-group <?= isset($errores['marca']) ? 'has-error' : '' ?>">
                            <label>Marca <span class="required">*</span></label>
                            <input type="text" name="marca"
                                   value="<?= htmlspecialchars($datos['marca'] ?? '') ?>"
                                   placeholder="Ej: AMD, Intel, NVIDIA">
                            <?php if (isset($errores['marca'])): ?>
                                <?php foreach ($errores['marca'] as $error): ?>
                                    <div class="error"><?= $error ?></div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="form-group <?= isset($errores['modelo']) ? 'has-error' : '' ?>">
                            <label>Modelo <span class="required">*</span></label>
                            <input type="text" name="modelo"
                                   value="<?= htmlspecialchars($datos['modelo'] ?? '') ?>"
                                   placeholder="Ej: Ryzen 5 5600X">
                            <?php if (isset($errores['modelo'])): ?>
                                <?php foreach ($errores['modelo'] as $error): ?>
                                    <div class="error"><?= $error ?></div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group <?= isset($errores['precio']) ? 'has-error' : '' ?>">
                            <label>Precio (USD) <span class="required">*</span></label>
                            <input type="number" name="precio" step="0.01" min="0.01"
                                   value="<?= $datos['precio'] ?? '' ?>"
                                   placeholder="199.99">
                            <?php if (isset($errores['precio'])): ?>
                                <?php foreach ($errores['precio'] as $error): ?>
                                    <div class="error"><?= $error ?></div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="form-group <?= isset($errores['stock']) ? 'has-error' : '' ?>">
                            <label>Stock <span class="required">*</span></label>
                            <input type="number" name="stock" min="0"
                                   value="<?= $datos['stock'] ?? '' ?>"
                                   placeholder="10">
                            <?php if (isset($errores['stock'])): ?>
                                <?php foreach ($errores['stock'] as $error): ?>
                                    <div class="error"><?= $error ?></div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group <?= isset($errores['categoria']) ? 'has-error' : '' ?>">
                        <label>Categoría <span class="required">*</span></label>
                        <select name="categoria">
                            <option value="">-- Seleccionar --</option>
                            <option value="Procesadores" <?= ($datos['categoria'] ?? '') === 'Procesadores' ? 'selected' : '' ?>>Procesadores</option>
                            <option value="Tarjetas Gráficas" <?= ($datos['categoria'] ?? '') === 'Tarjetas Gráficas' ? 'selected' : '' ?>>Tarjetas Gráficas</option>
                            <option value="Memorias RAM" <?= ($datos['categoria'] ?? '') === 'Memorias RAM' ? 'selected' : '' ?>>Memorias RAM</option>
                            <option value="Almacenamiento" <?= ($datos['categoria'] ?? '') === 'Almacenamiento' ? 'selected' : '' ?>>Almacenamiento</option>
                        </select>
                        <?php if (isset($errores['categoria'])): ?>
                            <?php foreach ($errores['categoria'] as $error): ?>
                                <div class="error"><?= $error ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Campos específicos: PROCESADOR -->
                    <div id="campos-procesador" class="campos-especificos">
                        <h3>🧠 Especificaciones del Procesador</h3>
                        <div class="form-grid">
                            <div class="form-group <?= isset($errores['nucleos']) ? 'has-error' : '' ?>">
                                <label>Número de Núcleos <span class="required">*</span></label>
                                <input type="number" name="nucleos" min="1"
                                       value="<?= $datos['nucleos'] ?? '' ?>"
                                       placeholder="6">
                                <?php if (isset($errores['nucleos'])): ?>
                                    <?php foreach ($errores['nucleos'] as $error): ?>
                                        <div class="error"><?= $error ?></div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="form-group <?= isset($errores['frecuencia']) ? 'has-error' : '' ?>">
                                <label>Frecuencia <span class="required">*</span></label>
                                <input type="text" name="frecuencia"
                                       value="<?= htmlspecialchars($datos['frecuencia'] ?? '') ?>"
                                       placeholder="4.3GHz">
                                <?php if (isset($errores['frecuencia'])): ?>
                                    <?php foreach ($errores['frecuencia'] as $error): ?>
                                        <div class="error"><?= $error ?></div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Campos específicos: TARJETA GRÁFICA -->
                    <div id="campos-tarjeta" class="campos-especificos hidden">
                        <h3>🎮 Especificaciones de la Tarjeta Gráfica</h3>
                        <div class="form-group <?= isset($errores['vram']) ? 'has-error' : '' ?>">
                            <label>VRAM <span class="required">*</span></label>
                            <input type="text" name="vram"
                                   value="<?= htmlspecialchars($datos['vram'] ?? '') ?>"
                                   placeholder="8GB GDDR6">
                            <?php if (isset($errores['vram'])): ?>
                                <?php foreach ($errores['vram'] as $error): ?>
                                    <div class="error"><?= $error ?></div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="buttons">
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">💾 Guardar Hardware</button>
                    </div>
                </form>

                <script>
                    /**
                     * Muestra/oculta los campos específicos según el tipo seleccionado.
                     */
                    function seleccionarTipo(tipo) {
                        // Actualizar input oculto
                        document.getElementById('tipoInput').value = tipo;

                        // Actualizar visual de cards
                        document.querySelectorAll('.tipo-option').forEach(el => {
                            el.classList.toggle('active', el.dataset.tipo === tipo);
                        });

                        // Mostrar/ocultar campos
                        const camposCpu = document.getElementById('campos-procesador');
                        const camposGpu = document.getElementById('campos-tarjeta');

                        if (tipo === 'procesador') {
                            camposCpu.classList.remove('hidden');
                            camposGpu.classList.add('hidden');
                        } else {
                            camposCpu.classList.add('hidden');
                            camposGpu.classList.remove('hidden');
                        }
                    }

                    // Inicializar según el tipo seleccionado (por si hay errores de validación)
                    document.addEventListener('DOMContentLoaded', function() {
                        const tipoActual = document.getElementById('tipoInput').value;
                        seleccionarTipo(tipoActual);
                    });
                </script>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
