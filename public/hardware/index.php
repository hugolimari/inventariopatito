<?php
declare(strict_types=1);

/**
 * HU 14 — Catálogo de Hardware (index con tabla y polimorfismo)
 * HU 10 — Alertas de stock crítico (fila roja si stock < 3)
 * HU 05 — Enlace de eliminación (link a eliminar.php?id=X)
 */

// Cargar configuración y autoloader
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Repositories\HardwareRepository;

// Crear repositorio (carga datos de prueba automáticamente)
$repo = new HardwareRepository();
$items = $repo->findAll();

// Contar alertas de stock crítico
$alertas = count(array_filter($items, fn($item) => $item->esStockCritico()));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Hardware - <?= APP_NAME ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            color: #667eea;
            font-size: 2em;
            margin-bottom: 5px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
            margin: 5px 5px 5px 0;
        }

        .badge.info { background: #d1ecf1; color: #0c5460; }
        .badge.success { background: #d4edda; color: #155724; }
        .badge.danger { background: #f8d7da; color: #721c24; }

        /* ── Stats ───────────────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 25px 0;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
        }

        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card .number {
            font-size: 2.5em;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-card .label {
            font-size: 0.9em;
            opacity: 0.9;
        }

        /* ── Table ───────────────────────────── */
        .section-title {
            color: #667eea;
            font-size: 1.3em;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .table-container {
            margin: 25px 0;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th {
            background: #f8f9fa;
            color: #667eea;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #667eea;
        }

        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e9ecef;
        }

        tr:hover {
            background: #f8f9fa;
        }

        /* Fila con alerta de stock crítico (HU 10) */
        tr.stock-critico {
            background-color: #ffcccc;
        }

        tr.stock-critico:hover {
            background-color: #ffbbbb;
        }

        .stock-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.85em;
            font-weight: bold;
        }

        .stock-ok { background: #d4edda; color: #155724; }
        .stock-warn { background: #f8d7da; color: #721c24; }

        /* ── Buttons ─────────────────────────── */
        .btn {
            display: inline-block;
            padding: 8px 18px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s, transform 0.1s;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
            font-size: 0.85em;
            padding: 5px 12px;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #888;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖥️ Catálogo de Hardware</h1>
        <div>
            <span class="badge info"><?= APP_NAME ?></span>
            <span class="badge success">Módulo de Inventario</span>
            <?php if ($alertas > 0): ?>
                <span class="badge danger">⚠️ <?= $alertas ?> alerta(s) de stock</span>
            <?php endif; ?>
        </div>

        <!-- Tarjetas de estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Total Productos</div>
                <div class="number"><?= count($items) ?></div>
            </div>
            <div class="stat-card <?= $alertas > 0 ? 'warning' : '' ?>">
                <div class="label">Stock Crítico</div>
                <div class="number"><?= $alertas ?></div>
            </div>
        </div>

        <!-- Barra de acciones -->
        <div class="actions-bar">
            <h2 class="section-title" style="margin:0; border:none; padding:0;">📋 Inventario Completo</h2>
            <a href="crear.php" class="btn btn-primary">➕ Agregar Hardware</a>
        </div>

        <!-- Tabla del catálogo (HU 14) -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Categoría</th>
                        <th>Precio (USD)</th>
                        <th>Stock</th>
                        <th>Detalles Técnicos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <!--
                            HU 10: Si esStockCritico() es true, la fila se resalta en rojo.
                            POLIMORFISMO: obtenerDetallesTecnicos() se llama sin saber
                            si $item es Procesador o TarjetaGrafica.
                        -->
                        <tr <?php if ($item->esStockCritico()): ?>style="background-color: #ffcccc;"<?php endif; ?>>
                            <td><?= $item->getId() ?></td>
                            <td><?= htmlspecialchars($item->getMarca()) ?></td>
                            <td><strong><?= htmlspecialchars($item->getModelo()) ?></strong></td>
                            <td><?= htmlspecialchars($item->getCategoria()) ?></td>
                            <td>$<?= number_format($item->getPrecio(), 2) ?></td>
                            <td>
                                <span class="stock-badge <?= $item->esStockCritico() ? 'stock-warn' : 'stock-ok' ?>">
                                    <?= $item->getStock() ?> uds.
                                    <?= $item->esStockCritico() ? '⚠️' : '✅' ?>
                                </span>
                            </td>
                            <!-- POLIMORFISMO: misma llamada, distinto resultado -->
                            <td><?= htmlspecialchars($item->obtenerDetallesTecnicos()) ?></td>
                            <td>
                                <!-- HU 05: Enlace de eliminación -->
                                <a href="eliminar.php?id=<?= $item->getId() ?>"
                                   class="btn btn-danger"
                                   onclick="return confirm('¿Eliminar <?= htmlspecialchars($item->getModelo()) ?>?')">
                                    🗑️ Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <a href="../index.php" class="btn btn-primary" style="margin-bottom: 10px;">← Volver al Dashboard</a>
            <p>Tecnología Web II — Módulo de Hardware • <?= date('Y') ?></p>
        </div>
    </div>
</body>
</html>
