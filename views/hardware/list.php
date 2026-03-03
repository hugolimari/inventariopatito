<?php
/**
 * View for displaying hardware list.
 * Variables expected:
 *   - items (array of associative arrays with hardware data from database)
 *   - total (int)
 *   - currentPage (int)
 *   - totalPages (int)
 *   - search (string)
 *   - title (string)
 *   - currentUser (array from session)
 */

// helpers
function h($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }

// ensure optional variables have sensible defaults to avoid warnings
$search = $search ?? '';
$selectedCategory = $selectedCategory ?? 0;
$categories = $categories ?? [];
$brands = $brands ?? [];
// agrupar marcas por categoría para uso en JS dinámico
$brandsByCategory = [];
foreach ($brands as $b) {
    $brandsByCategory[$b['id_categoria']][] = $b;
}

$alertas = count(array_filter($items, fn($i) => $i['stock'] <= 5));
$flashSuccess = $this->getFlash('success') ?? null;
$flashError = $this->getFlash('error') ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? APP_NAME) ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { background:white; padding:30px; border-radius:20px;
            box-shadow:0 20px 60px rgba(0,0,0,0.3); max-width:1200px; margin:0 auto; }
        h1 { color:#667eea; font-size:2em; margin-bottom:5px; }
        .badge { display:inline-block; padding:4px 10px; border-radius:12px;
            font-size:0.8em; font-weight:bold; margin:5px; }
        .badge.info{background:#d1ecf1;color:#0c5460;}
        .badge.success{background:#d4edda;color:#155724;}
        .badge.danger{background:#f8d7da;color:#721c24;}
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;margin:25px 0;}
        .stat-card{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;border-radius:15px;text-align:center;}
        .stat-card.warning{background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);}
        .stat-card .number{font-size:2.5em;font-weight:bold;margin:10px 0;}
        .stat-card .label{font-size:0.9em;opacity:0.9;}
        table{width:100%;border-collapse:collapse;background:white;}
        th{background:#f8f9fa;color:#667eea;padding:12px;text-align:left;font-weight:600;border-bottom:2px solid #667eea;}
        td{padding:10px 12px;border-bottom:1px solid #e9ecef;}
        tr:hover{background:#f8f9fa;}
        tr.stock-critico{background:#ffcccc;} tr.stock-critico:hover{background:#ffbbbb;}
        .stock-badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:0.85em;font-weight:bold;}
        /* inline salida form row */
        .salida-row td { background:#fefefe; padding:12px; }
        .stock-ok{background:#d4edda;color:#155724;} .stock-warn{background:#f8d7da;color:#721c24;}
        .btn{display:inline-block;padding:8px 18px;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;transition:background .3s,transform .1s;}
        .btn:hover{transform:translateY(-1px);} .btn-primary{background:#667eea;color:white;} .btn-primary:hover{background:#5568d3;} .btn-danger{background:#dc3545;color:white;font-size:0.85em;padding:5px 12px;} .btn-danger:hover{background:#c82333;} .btn-secondary{background:#6c757d;color:white;} .btn-secondary:hover{background:#5a6268;} .btn-success{background:#28a745;color:white;font-size:0.85em;padding:5px 12px;} .btn-success:hover{background:#218838;} .btn-warning{background:#ffc107;color:#333;font-size:0.85em;padding:5px 12px;} .btn-warning:hover{background:#e0a800;}
        .search-form{
    margin-bottom:20px;
    display:flex;
    gap:10px;
    align-items:center;
    flex-wrap:wrap;
}
.search-form input[type=text], .search-form select{
    padding:6px 10px;
    border:1px solid #ccc;
    border-radius:4px;
    font-size:14px;
}
.search-form select{min-width:180px;}
.search-form input[type=text]{flex:1;}
.search-form button{white-space:nowrap;}
        .flash {padding:10px;border-radius:5px;margin-bottom:15px;}
        .flash.success{background:#d4edda;color:#155724;border-left:4px solid #28a745;}
        .flash.error{background:#f8d7da;color:#721c24;border-left:4px solid #dc3545;}
        
        /* modal styles */
        .modal-overlay {
            display:none;position:fixed;top:0;left:0;right:0;bottom:0;
            background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;
        }
        .modal-overlay.active { display:flex; }
        .modal-box {
            background:white;border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,0.3);
            padding:32px;max-width:420px;width:90%;animation:slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform:translateY(-20px);opacity:0; }
            to { transform:translateY(0);opacity:1; }
        }
        .modal-header {
            display:flex;align-items:center;gap:12px;margin-bottom:20px;
        }
        .modal-icon {
            width:44px;height:44px;border-radius:50%;background:#667eea;color:white;
            display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;
        }
        .modal-title {
            font-size:20px;font-weight:700;color:#2c3e50;margin:0;
        }
        .modal-body {
            margin-bottom:24px;color:#566573;line-height:1.6;font-size:14px;
        }
        .modal-input-group {
            display:flex;flex-direction:column;gap:12px;margin-bottom:20px;
        }
        .modal-input-group label {
            font-weight:600;color:#2c3e50;font-size:13px;
        }
        .modal-input-group input,
        .modal-input-group select {
            padding:10px 12px;border:2px solid #e0e7ff;border-radius:8px;font-size:14px;
            transition:border-color 0.2s;background:white;
        }
        .modal-input-group input:focus,
        .modal-input-group select:focus {
            outline:none;border-color:#667eea;box-shadow:0 0 0 3px rgba(102,126,234,0.1);
        }
        .modal-actions {
            display:flex;gap:12px;justify-content:flex-end;
        }
        .modal-btn {
            padding:10px 20px;border:none;border-radius:8px;font-weight:600;cursor:pointer;
            transition:all 0.2s;font-size:14px;
        }
        .modal-btn-cancel {
            background:#f0f4f8;color:#566573;border:1px solid #e0e7ff;
        }
        .modal-btn-cancel:hover {
            background:#e8ecf1;
        }
        .modal-btn-confirm {
            background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;
        }
        .modal-btn-confirm:hover {
            transform:translateY(-2px);box-shadow:0 4px 12px rgba(102,126,234,0.4);
        }
        .modal-errors {
            background:#fff5f5;border-left:4px solid #dc3545;padding:12px;border-radius:8px;
            color:#721c24;font-size:13px;margin-bottom:16px;display:none;
        }
        .modal-errors.show { display:block; }
        
        /* brand buttons */
        .modal-brands-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }
        .modal-brand-btn {
            padding: 8px 14px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            color: #333;
        }
        .modal-brand-btn:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        .modal-brand-btn.selected {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        .modal-brand-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* improved inline form row */
        .salida-row td {
            background:linear-gradient(to right,#f8f9fa,#ffffff);padding:20px;border-top:2px solid #e0e7ff;
        }
        .salida-form-wrapper {
            display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;
        }
        .salida-form-field {
            flex:1;min-width:150px;
        }
        .salida-form-field label {
            display:block;margin-bottom:6px;font-weight:600;color:#2c3e50;font-size:13px;
        }
        .salida-form-field input {
            width:100%;padding:10px 12px;border:2px solid #e0e7ff;border-radius:8px;
            font-size:14px;transition:border-color 0.2s;
        }
        .salida-form-field input:focus {
            outline:none;border-color:#667eea;box-shadow:0 0 0 3px rgba(102,126,234,0.1);
        }
        .salida-form-actions {
            display:flex;gap:8px;
        }
        .btn-secondary{background:#6c757d;color:white;}
        .btn-secondary:hover{background:#5a6268;}
        .inline-errors {
            background:#fff5f5;border-left:4px solid #dc3545;padding:12px;
            border-radius:8px;color:#721c24;font-size:13px;margin-top:12px;display:none;
        }
        .inline-errors.show { display:block; }
        
        .pagination{margin-top:20px;}
        .pagination a{margin:0 3px;text-decoration:none;color:#667eea;}
    </style>
</head>
<body>
    <div class="container">
        <h1><?= h($title ?? 'Inventario de Hardware') ?></h1>
        <?php if (!empty($currentUser)): ?>
            <div style="text-align:right; margin-bottom:10px; display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <strong>Usuario:</strong> <?= h($currentUser['username']) ?>
                    <?php if (!empty($currentUser['role_name'])): ?>
                        (<?= h($currentUser['role_name']) ?>)
                    <?php endif; ?>
                </div>
                <div>
                    <a class="btn btn-primary" href="/auth/logout.php">Cerrar sesión</a>
                    <?php if (($currentUser['role'] ?? 0) === 1): ?>
                        <a class="btn btn-secondary" href="/admin/users.php">Gestión de Usuarios</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($flashSuccess): ?><div class="flash success"><?= h($flashSuccess) ?></div><?php endif; ?>
        <?php if ($flashError): ?><div class="flash error"><?= h($flashError) ?></div><?php endif; ?>

        <form method="get" class="search-form">
            <input type="text" name="search" value="<?= h($search) ?>" placeholder="Buscar por marca">

            <select name="category">
                <option value="0">Todas las categorías</option>
                <?php foreach (($categories ?? []) as $cat): ?>
                    <?php $catId = $cat['id_categoria'] ?? null; ?>
                    <?php $sel = (isset($selectedCategory) && (int)$selectedCategory === (int)$catId) ? 'selected' : ''; ?>
                    <option value="<?= h((string)$catId) ?>" <?= $sel ?>><?= h($cat['nombre'] ?? '') ?></option>
                <?php endforeach; ?>
            </select>

            <button class="btn btn-primary" type="submit">Buscar</button>
        </form>
        <script>
            // auto-submit when category changes
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('.search-form');
                const select = form.querySelector('select[name="category"]');
                if (select) {
                    select.addEventListener('change', function() {
                        form.submit();
                    });
                }
            });
        </script>

        <?php if (($currentUser['role'] ?? 0) === 1): ?>
            <a href="#" class="btn btn-primary" id="btnAddHardware">+ Agregar hardware</a>
        <?php endif; ?>

        <script>
            // dismiss flash messages after 3 seconds
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    document.querySelectorAll('.flash').forEach(el => el.remove());
                }, 3000);
            });
        </script>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?= $total ?></div>
                <div class="label">Total de items</div>
            </div>
            <div class="stat-card warning">
                <div class="number"><?= $alertas ?></div>
                <div class="label">Alertas stock crítico</div>
            </div>
        </div>

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
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item):
                    $isCritical = $item['stock'] <= 5;
                    $rowClass = $isCritical ? 'stock-critico' : '';
                ?>
                    <tr class="<?= $rowClass ?>">
                        <td><?= h($item['id_hardware']) ?></td>
                        <td><?= h($item['marca_nombre'] ?? '') ?></td>
                        <td><?= h($item['modelo']) ?></td>
                        <td><?= h($item['categoria_nombre']) ?></td>
                        <td><?= number_format($item['precio'],2) ?></td>
                        <td id="stock-<?= h($item['id_hardware']) ?>">
                            <?= h($item['stock']) ?>
                            <?php if ($item['stock'] <= 5): ?>
                                <span class="stock-badge stock-warn">¡Crítico!</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (($currentUser['role'] ?? 0) === 1): ?>
                                <a class="btn btn-danger" href="eliminar.php?id=<?= h($item['id_hardware']) ?>" onclick="return confirm('¿Está seguro de eliminar este hardware?');">Eliminar</a>
                            <?php endif; ?>
                            <?php if (($currentUser['role'] ?? 0) === 3): ?>
                                <a class="btn btn-secondary btn-salida" href="#" data-id="<?= h($item['id_hardware']) ?>">Salida</a>
                            <?php endif; ?>
                            <?php if (($currentUser['role'] ?? 0) === 1): ?>
                                <a class="btn btn-primary btn-edit-modal" href="#" data-id="<?= h($item['id_hardware']) ?>" data-marca-id="<?= h($item['id_marca'] ?? '') ?>" data-modelo="<?= h($item['modelo']) ?>" data-precio="<?= h($item['precio']) ?>" data-stock="<?= h($item['stock']) ?>" data-categoria="<?= h($item['id_categoria']) ?>" data-role="1">Editar</a>
                                <a class="btn btn-success" href="entrada.php?id=<?= h($item['id_hardware']) ?>">Entrada</a>
                                <a class="btn btn-warning" href="rma.php?id=<?= h($item['id_hardware']) ?>">RMA</a>
                            <?php elseif (($currentUser['role'] ?? 0) === 2): ?>
                                <a class="btn btn-primary btn-edit-modal" href="#" data-id="<?= h($item['id_hardware']) ?>" data-marca-id="<?= h($item['id_marca'] ?? '') ?>" data-modelo="<?= h($item['modelo']) ?>" data-precio="<?= h($item['precio']) ?>" data-stock="<?= h($item['stock']) ?>" data-categoria="<?= h($item['id_categoria']) ?>" data-role="2">Editar</a>
                                <a class="btn btn-success btn-entrada-modal" href="#" data-id="<?= h($item['id_hardware']) ?>">Entrada</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal para agregar hardware -->
        <div class="modal-overlay" id="createHardwareModal">
            <div class="modal-box">
                <div class="modal-header">
                    <div class="modal-icon">📦</div>
                    <h2 class="modal-title">Agregar Hardware</h2>
                </div>
                <div class="modal-errors" id="createErrors"></div>
                <form id="createHardwareForm">
                    <div class="modal-input-group">
                        <label for="createCategoria">Categoría <span style="color:#dc3545">*</span></label>
                        <select id="createCategoria" name="id_categoria" required>
                            <option value="">-- Seleccione una categoría --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= h($cat['id_categoria']) ?>"><?= h($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="modal-input-group">
                        <label>Marca <span style="color:#dc3545">*</span></label>
                        <div class="modal-brands-container" id="createBrandsContainer"></div>
                        <input type="hidden" id="createMarca" name="id_marca" required>
                    </div>
                    <div class="modal-input-group">
                        <label for="createModelo">Modelo/Descripción <span style="color:#dc3545">*</span></label>
                        <input type="text" id="createModelo" name="modelo" required>
                    </div>
                    <div class="modal-input-group" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label for="createPrecio">Precio <span style="color:#dc3545">*</span></label>
                            <input type="number" id="createPrecio" name="precio" step="0.01" required>
                        </div>
                        <div>
                            <label for="createStock">Stock <span style="color:#dc3545">*</span></label>
                            <input type="number" id="createStock" name="stock" required>
                        </div>
                    </div>
                </form>
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="document.getElementById('createHardwareModal').classList.remove('active')">Cancelar</button>
                    <button type="button" class="modal-btn modal-btn-confirm" id="btnCreateConfirm">Guardar hardware</button>
                </div>
            </div>
        </div>

        <!-- Modal para editar hardware -->
        <div class="modal-overlay" id="editHardwareModal">
            <div class="modal-box">
                <div class="modal-header">
                    <div class="modal-icon">✏️</div>
                    <h2 class="modal-title">Editar Hardware</h2>
                </div>
                <div class="modal-errors" id="editErrors"></div>
                <form id="editHardwareForm">
                    <input type="hidden" id="editHardwareId">
                    <div class="modal-input-group">
                        <label for="editCategoria">Categoría <span style="color:#dc3545">*</span></label>
                        <select id="editCategoria" name="id_categoria" required>
                            <option value="">-- Seleccione una categoría --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= h($cat['id_categoria']) ?>"><?= h($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="modal-input-group">
                        <label>Marca <span style="color:#dc3545">*</span></label>
                        <div class="modal-brands-container" id="editBrandsContainer"></div>
                        <input type="hidden" id="editMarca" name="id_marca" required>
                    </div>
                    <div class="modal-input-group">
                        <label for="editModelo">Modelo/Descripción <span style="color:#dc3545">*</span></label>
                        <input type="text" id="editModelo" name="modelo" required>
                    </div>
                    <div class="modal-input-group" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label for="editPrecio">Precio <span style="color:#dc3545">*</span></label>
                            <input type="number" id="editPrecio" name="precio" step="0.01" required>
                        </div>
                        <div>
                            <label for="editStock">Stock <span style="color:#dc3545">*</span></label>
                            <input type="number" id="editStock" name="stock" required>
                        </div>
                    </div>
                    <input type="hidden" id="editUserRole" value="<?= ($currentUser['role'] ?? 0) ?>">
                </form>
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="document.getElementById('editHardwareModal').classList.remove('active')">Cancelar</button>
                    <button type="button" class="modal-btn modal-btn-confirm" id="btnEditConfirm">Guardar cambios</button>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación de salida -->
        <div class="modal-overlay" id="salidaModal">
            <div class="modal-box">
                <div class="modal-header">
                    <div class="modal-icon">📦</div>
                    <h2 class="modal-title">Registrar salida</h2>
                </div>
                <div class="modal-body">
                    Se registrará la salida del componente <strong id="modalHardwareInfo"></strong>
                </div>
                <div class="modal-errors" id="modalErrors"></div>
                <div class="modal-input-group">
                    <label for="modalCantidad">Cantidad a retirar <span style="color:#dc3545">*</span></label>
                    <input type="number" id="modalCantidad" min="1" required placeholder="Ej: 5">
                </div>
                <div class="modal-input-group">
                    <label for="modalObservacion">Observación (opcional)</label>
                    <input type="text" id="modalObservacion" placeholder="Ej: Componentes para laptop Dell">
                </div>
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" id="modalCancel">Cancelar</button>
                    <button type="button" class="modal-btn modal-btn-confirm" id="modalConfirm">Confirmar salida</button>
                </div>
            </div>
        </div>

        <!-- Modal de entrada para almacenero -->
        <div class="modal-overlay" id="entradaModal">
            <div class="modal-box">
                <div class="modal-header">
                    <div class="modal-icon">📥</div>
                    <h2 class="modal-title">Registrar entrada</h2>
                </div>
                <div class="modal-body">
                    Se registrará la entrada del componente <strong id="entradaHardwareInfo"></strong>
                </div>
                <div class="modal-errors" id="entradaErrors"></div>
                <div class="modal-input-group">
                    <label for="entradaCantidad">Cantidad a ingresar <span style="color:#dc3545">*</span></label>
                    <input type="number" id="entradaCantidad" min="1" required placeholder="Ej: 10">
                </div>
                <div class="modal-input-group">
                    <label for="entradaObservacion">Observación (opcional)</label>
                    <input type="text" id="entradaObservacion" placeholder="Ej: Compra a proveedor XYZ">
                </div>
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" id="entradaCancel">Cancelar</button>
                    <button type="button" class="modal-btn modal-btn-confirm" id="entradaConfirm">Confirmar entrada</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // flash message helper
        function showFlash(message, type='success') {
            const container = document.querySelector('.container');
            const div = document.createElement('div');
            div.className = `flash ${type}`;
            div.textContent = message;
            container.insertBefore(div, container.firstChild);
            setTimeout(() => div.remove(), 4000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // marcas agrupadas por categoría inyectadas desde PHP
            const brandsByCategory = <?= json_encode($brandsByCategory) ?>;

            function populateBrandButtons(containerId, inputId, categoryId, selectedId=null) {
                const container = document.getElementById(containerId);
                const input = document.getElementById(inputId);
                container.innerHTML = '';
                
                if (!categoryId) {
                    const emptyMsg = document.createElement('p');
                    emptyMsg.textContent = 'Selecciona una categoría primero';
                    emptyMsg.style.color = '#999';
                    emptyMsg.style.fontSize = '13px';
                    emptyMsg.style.marginBottom = '10px';
                    container.appendChild(emptyMsg);
                    return;
                }
                
                const list = brandsByCategory[categoryId] || [];
                if (list.length === 0) {
                    const emptyMsg = document.createElement('p');
                    emptyMsg.textContent = 'No hay marcas disponibles para esta categoría';
                    emptyMsg.style.color = '#999';
                    emptyMsg.style.fontSize = '13px';
                    emptyMsg.style.marginBottom = '10px';
                    container.appendChild(emptyMsg);
                    return;
                }
                
                list.forEach(b => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'modal-brand-btn';
                    btn.textContent = b.nombre;
                    btn.dataset.marcaId = b.id_marca;
                    
                    if (selectedId && selectedId == b.id_marca) {
                        btn.classList.add('selected');
                        input.value = b.id_marca;
                    }
                    
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        // remove selection from other buttons
                        container.querySelectorAll('.modal-brand-btn').forEach(b => b.classList.remove('selected'));
                        // select this button
                        btn.classList.add('selected');
                        input.value = b.id_marca;
                    });
                    
                    container.appendChild(btn);
                });
            }

            // cuando cambie la categoría en los modales, actualizar las marcas
            const createCategoriaSel = document.getElementById('createCategoria');
            if (createCategoriaSel) {
                createCategoriaSel.addEventListener('change', function() {
                    populateBrandButtons('createBrandsContainer', 'createMarca', this.value);
                });
            }
            const editCategoriaSel = document.getElementById('editCategoria');
            if (editCategoriaSel) {
                editCategoriaSel.addEventListener('change', function() {
                    populateBrandButtons('editBrandsContainer', 'editMarca', this.value);
                });
            }

            const modalOverlay = document.getElementById('salidaModal');
            const modalCancel = document.getElementById('modalCancel');
            const modalConfirm = document.getElementById('modalConfirm');
            const modalErrors = document.getElementById('modalErrors');
            const modalCantidad = document.getElementById('modalCantidad');
            const modalObservacion = document.getElementById('modalObservacion');
            const modalHardwareInfo = document.getElementById('modalHardwareInfo');
            
            let currentHardwareId = null;
            let currentHardwareName = null;

            // Abrir modal al hacer clic en "Salida"
            document.querySelectorAll('a.btn-salida').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentHardwareId = this.dataset.id;
                    
                    // Get hardware info from the row
                    const row = this.closest('tr');
                    const marca = row.querySelector('td:nth-child(2)').textContent.trim();
                    const modelo = row.querySelector('td:nth-child(3)').textContent.trim();
                    currentHardwareName = marca + ' ' + modelo;
                    
                    // Show modal
                    modalHardwareInfo.textContent = currentHardwareName;
                    modalErrors.classList.remove('show');
                    modalErrors.textContent = '';
                    modalCantidad.value = '';
                    modalObservacion.value = '';
                    modalCantidad.focus();
                    modalOverlay.classList.add('active');
                });
            });

            // Cerrar modal
            function closeModal() {
                modalOverlay.classList.remove('active');
                currentHardwareId = null;
            }

            modalCancel.addEventListener('click', closeModal);
            modalOverlay.addEventListener('click', function(e) {
                if (e.target === modalOverlay) closeModal();
            });

            // ========== MODAL ENTRADA PARA ALMACENERO ==========
            const entradaModal = document.getElementById('entradaModal');
            const entradaCancel = document.getElementById('entradaCancel');
            const entradaConfirm = document.getElementById('entradaConfirm');
            const entradaErrors = document.getElementById('entradaErrors');
            const entradaCantidad = document.getElementById('entradaCantidad');
            const entradaObservacion = document.getElementById('entradaObservacion');
            const entradaHardwareInfo = document.getElementById('entradaHardwareInfo');
            
            let currentEntradaHardwareId = null;
            let currentEntradaHardwareName = null;

            // Abrir modal al hacer clic en "Entrada"
            document.querySelectorAll('a.btn-entrada-modal').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentEntradaHardwareId = this.dataset.id;
                    
                    // Get hardware info from the row
                    const row = this.closest('tr');
                    const marca = row.querySelector('td:nth-child(2)').textContent.trim();
                    const modelo = row.querySelector('td:nth-child(3)').textContent.trim();
                    currentEntradaHardwareName = marca + ' ' + modelo;
                    
                    // Show modal
                    entradaHardwareInfo.textContent = currentEntradaHardwareName;
                    entradaErrors.classList.remove('show');
                    entradaErrors.textContent = '';
                    entradaCantidad.value = '';
                    entradaObservacion.value = '';
                    entradaCantidad.focus();
                    entradaModal.classList.add('active');
                });
            });

            // Cerrar modal entrada
            function closeEntradaModal() {
                entradaModal.classList.remove('active');
                currentEntradaHardwareId = null;
            }

            entradaCancel.addEventListener('click', closeEntradaModal);
            entradaModal.addEventListener('click', function(e) {
                if (e.target === entradaModal) closeEntradaModal();
            });

            // Confirmar entrada
            entradaConfirm.addEventListener('click', function() {
                const cantidad = entradaCantidad.value.trim();
                const observacion = entradaObservacion.value.trim();

                // Validar cantidad
                if (!cantidad || parseInt(cantidad) < 1) {
                    entradaErrors.textContent = 'Ingresa una cantidad válida (mínimo 1)';
                    entradaErrors.classList.add('show');
                    return;
                }

                // Enviar AJAX
                const formData = new FormData();
                formData.append('cantidad', cantidad);
                formData.append('observacion', observacion);

                entradaConfirm.disabled = true;
                entradaConfirm.style.opacity = '0.6';

                fetch(`/hardware/entrada.php?id=${currentEntradaHardwareId}`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(r => r.json())
                .then(json => {
                    if (json.success) {
                        // Actualizar stock en la tabla
                        if (json.stock !== undefined) {
                            const stockCell = document.getElementById(`stock-${currentEntradaHardwareId}`);
                            let newStock = json.stock;
                            let html = newStock;
                            if (newStock <= 5) {
                                html += ' <span class="stock-badge stock-warn">¡Crítico!</span>';
                            }
                            stockCell.innerHTML = html;
                        }
                        showFlash('✓ Entrada registrada exitosamente', 'success');
                        closeEntradaModal();
                    } else {
                        const errMsg = json.error || (json.errors ? JSON.stringify(json.errors) : 'Error desconocido');
                        entradaErrors.textContent = errMsg;
                        entradaErrors.classList.add('show');
                    }
                })
                .catch(err => {
                    entradaErrors.textContent = 'Error de conexión: ' + err.message;
                    entradaErrors.classList.add('show');
                })
                .finally(() => {
                    entradaConfirm.disabled = false;
                    entradaConfirm.style.opacity = '1';
                });
            });

            // Permitir Enter para confirmar entrada
            entradaCantidad.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') entradaConfirm.click();
            });
            entradaObservacion.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') entradaConfirm.click();
            });

            // Confirmar salida
            modalConfirm.addEventListener('click', function() {
                const cantidad = modalCantidad.value.trim();
                const observacion = modalObservacion.value.trim();

                // Validar cantidad
                if (!cantidad || parseInt(cantidad) < 1) {
                    modalErrors.textContent = 'Ingresa una cantidad válida (mínimo 1)';
                    modalErrors.classList.add('show');
                    return;
                }

                // Enviar AJAX
                const formData = new FormData();
                formData.append('cantidad', cantidad);
                formData.append('observacion', observacion);

                modalConfirm.disabled = true;
                modalConfirm.style.opacity = '0.6';

                fetch(`/hardware/salida.php?id=${currentHardwareId}`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(r => r.json())
                .then(json => {
                    if (json.success) {
                        // Actualizar stock en la tabla
                        if (json.stock !== undefined) {
                            const stockCell = document.getElementById(`stock-${currentHardwareId}`);
                            let newStock = json.stock;
                            let html = newStock;
                            if (newStock <= 5) {
                                html += ' <span class="stock-badge stock-warn">¡Crítico!</span>';
                            }
                            stockCell.innerHTML = html;
                        }
                        showFlash('✓ Salida registrada exitosamente', 'success');
                        closeModal();
                    } else {
                        const errMsg = json.error || (json.errors ? JSON.stringify(json.errors) : 'Error desconocido');
                        modalErrors.textContent = errMsg;
                        modalErrors.classList.add('show');
                    }
                })
                .catch(err => {
                    modalErrors.textContent = 'Error de conexión: ' + err.message;
                    modalErrors.classList.add('show');
                })
                .finally(() => {
                    modalConfirm.disabled = false;
                    modalConfirm.style.opacity = '1';
                });
            });

            // Permitir Enter para confirmar
            modalCantidad.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') modalConfirm.click();
            });
            modalObservacion.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') modalConfirm.click();
            });

            // ========== MODAL CREAR HARDWARE ==========
            const btnAddHardware = document.getElementById('btnAddHardware');
            const createHardwareModal = document.getElementById('createHardwareModal');
            const btnCreateConfirm = document.getElementById('btnCreateConfirm');
            const createHardwareForm = document.getElementById('createHardwareForm');
            const createErrors = document.getElementById('createErrors');

            if (btnAddHardware) {
                btnAddHardware.addEventListener('click', function(e) {
                    e.preventDefault();
                    createHardwareForm.reset();
                    createErrors.classList.remove('show');
                    createErrors.textContent = '';
                    // clear brand buttons until category selected
                    document.getElementById('createMarca').value = '';
                    document.getElementById('createBrandsContainer').innerHTML = '';
                    const emptyMsg = document.createElement('p');
                    emptyMsg.textContent = 'Selecciona una categoría primero';
                    emptyMsg.style.color = '#999';
                    emptyMsg.style.fontSize = '13px';
                    emptyMsg.style.marginBottom = '10px';
                    document.getElementById('createBrandsContainer').appendChild(emptyMsg);
                    createHardwareModal.classList.add('active');
                    document.getElementById('createCategoria').focus();
                });
            }

            if (btnCreateConfirm) {
                btnCreateConfirm.addEventListener('click', function() {
                    const marcaId = document.getElementById('createMarca').value.trim();
                    const modelo = document.getElementById('createModelo').value.trim();
                    const precio = document.getElementById('createPrecio').value.trim();
                    const stock = document.getElementById('createStock').value.trim();
                    const categoria = document.getElementById('createCategoria').value.trim();

                    // Validar
                    if (!marcaId || !modelo || !precio || !stock || !categoria) {
                        createErrors.textContent = 'Todos los campos son requeridos';
                        createErrors.classList.add('show');
                        return;
                    }

                    if (isNaN(precio) || isNaN(stock)) {
                        createErrors.textContent = 'Precio y stock deben ser números';
                        createErrors.classList.add('show');
                        return;
                    }

                    btnCreateConfirm.disabled = true;
                    btnCreateConfirm.style.opacity = '0.6';

                    const formData = new FormData(createHardwareForm);
                    fetch('/hardware/crear.php', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    })
                    .then(r => r.json())
                    .then(json => {
                        if (json.success) {
                            showFlash('✓ ' + (json.message || 'Hardware creado exitosamente'), 'success');
                            createHardwareModal.classList.remove('active');
                            createHardwareForm.reset();
                            // Recargar la lista
                            setTimeout(() => location.reload(), 500);
                        } else {
                            const errMsg = json.error || (json.errors ? JSON.stringify(json.errors) : 'Error desconocido');
                            createErrors.textContent = errMsg;
                            createErrors.classList.add('show');
                        }
                    })
                    .catch(err => {
                        createErrors.textContent = 'Error de conexión: ' + err.message;
                        createErrors.classList.add('show');
                    })
                    .finally(() => {
                        btnCreateConfirm.disabled = false;
                        btnCreateConfirm.style.opacity = '1';
                    });
                });
            }

            // ========== MODAL EDITAR HARDWARE ==========
            const editHardwareModal = document.getElementById('editHardwareModal');
            const btnEditConfirm = document.getElementById('btnEditConfirm');
            const editHardwareForm = document.getElementById('editHardwareForm');
            const editErrors = document.getElementById('editErrors');

            document.querySelectorAll('.btn-edit-modal').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    const marcaId = this.dataset.marcaId;
                    const modelo = this.dataset.modelo;
                    const precio = this.dataset.precio;
                    const stock = this.dataset.stock;
                    const categoria = this.dataset.categoria;
                    const userRole = this.dataset.role || 1;

                    document.getElementById('editHardwareId').value = id;
                    document.getElementById('editCategoria').value = categoria;
                    // populate marca buttons based on category and mark the current brand
                    populateBrandButtons('editBrandsContainer', 'editMarca', categoria, marcaId);
                    document.getElementById('editModelo').value = modelo;
                    document.getElementById('editPrecio').value = precio;
                    document.getElementById('editStock').value = stock;
                    editErrors.classList.remove('show');
                    editErrors.textContent = '';

                    // Si es almacenero (rol 2), deshabilitar Precio y Stock
                    const editPrecio = document.getElementById('editPrecio');
                    const editStock = document.getElementById('editStock');
                    if (userRole === '2' || userRole === 2) {
                        editPrecio.disabled = true;
                        editStock.disabled = true;
                        editPrecio.style.opacity = '0.6';
                        editStock.style.opacity = '0.6';
                        editPrecio.style.backgroundColor = '#f0f0f0';
                        editStock.style.backgroundColor = '#f0f0f0';
                    } else {
                        editPrecio.disabled = false;
                        editStock.disabled = false;
                        editPrecio.style.opacity = '1';
                        editStock.style.opacity = '1';
                        editPrecio.style.backgroundColor = 'white';
                        editStock.style.backgroundColor = 'white';
                    }

                    editHardwareModal.classList.add('active');
                    // Focus on first brand button if available
                    const firstBtn = document.querySelector('#editBrandsContainer .modal-brand-btn');
                    if (firstBtn) firstBtn.focus();
                });
            });

            btnEditConfirm.addEventListener('click', function() {
                const marcaId = document.getElementById('editMarca').value.trim();
                const modelo = document.getElementById('editModelo').value.trim();
                const precio = document.getElementById('editPrecio').value.trim();
                const stock = document.getElementById('editStock').value.trim();
                const categoria = document.getElementById('editCategoria').value.trim();
                const userRole = document.getElementById('editUserRole').value;

                // Validar campos obligatorios
                if (!marcaId || !modelo || !categoria) {
                    editErrors.textContent = 'Marca, modelo y categoría son requeridos';
                    editErrors.classList.add('show');
                    return;
                }

                // Para admin, requiere precio y stock; para almacenero no
                if (userRole === '1' && (!precio || !stock)) {
                    editErrors.textContent = 'Todos los campos son requeridos';
                    editErrors.classList.add('show');
                    return;
                }

                if (precio && isNaN(precio)) {
                    editErrors.textContent = 'Precio debe ser un número válido';
                    editErrors.classList.add('show');
                    return;
                }

                if (stock && isNaN(stock)) {
                    editErrors.textContent = 'Stock debe ser un número válido';
                    editErrors.classList.add('show');
                    return;
                }

                btnEditConfirm.disabled = true;
                btnEditConfirm.style.opacity = '0.6';

                const id = document.getElementById('editHardwareId').value;
                const formData = new FormData(editHardwareForm);

                fetch(`/hardware/editar.php?id=${id}`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(r => r.json())
                .then(json => {
                    if (json.success) {
                        showFlash('✓ ' + (json.message || 'Hardware actualizado exitosamente'), 'success');
                        editHardwareModal.classList.remove('active');
                        // Recargar la lista
                        setTimeout(() => location.reload(), 500);
                    } else {
                        const errMsg = json.error || (json.errors ? JSON.stringify(json.errors) : 'Error desconocido');
                        editErrors.textContent = errMsg;
                        editErrors.classList.add('show');
                    }
                })
                .catch(err => {
                    editErrors.textContent = 'Error de conexión: ' + err.message;
                    editErrors.classList.add('show');
                })
                .finally(() => {
                    btnEditConfirm.disabled = false;
                    btnEditConfirm.style.opacity = '1';
                });
            });

            // Cerrar modales haciendo click en el overlay
            createHardwareModal.addEventListener('click', function(e) {
                if (e.target === createHardwareModal) {
                    createHardwareModal.classList.remove('active');
                }
            });
            editHardwareModal.addEventListener('click', function(e) {
                if (e.target === editHardwareModal) {
                    editHardwareModal.classList.remove('active');
                }
            });
        });
    </script>
    <script>
        // dismiss flash messages after 4 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelectorAll('.flash').forEach(el => el.remove());
            }, 4000);
        });
    </script>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php if ($p === $currentPage): ?>
                        <strong><?= $p ?></strong>
                    <?php else: ?>
                        <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>&category=<?= urlencode((string)($selectedCategory ?? 0)) ?>"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
