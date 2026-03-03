<?php
/**
 * View for hardware creation form
 * Variables expected:
 *   - data (associative array of old input)
 *   - errors (assoc array of validation messages)
 *   - categories (array of ['id_categoria' => int, 'nombre' => string])
 */

function h($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
$flashError = $this->getFlash('error') ?? null;
$brands = $brands ?? [];
$brandsByCategory = [];
foreach ($brands as $b) {
    $brandsByCategory[$b['id_categoria']][] = $b;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Hardware - <?= h(APP_NAME) ?></title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{
            font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
            background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
            min-height:100vh;padding:20px;
        }
        .container{
            max-width:600px;margin:0 auto;background:white;border-radius:20px;
            box-shadow:0 20px 60px rgba(0,0,0,0.3);overflow:hidden;
        }
        .header{
            background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
            color:white;padding:40px 30px;text-align:center;
        }
        .header h1{
            font-size:28px;margin-bottom:8px;font-weight:700;
        }
        .header p{
            opacity:0.9;font-size:14px;
        }
        .content{padding:40px 30px;}
        .form-group{margin-bottom:24px;}
        label{
            display:block;margin-bottom:8px;font-weight:600;
            color:#2c3e50;font-size:14px;
        }
        .required{color:#dc3545;}
        input,select{
            width:100%;padding:12px 14px;border:2px solid #e0e7ff;
            border-radius:10px;font-size:14px;transition:all 0.2s;
            background:#f8fafc;
        }
        input:focus,select:focus{
            outline:none;border-color:#667eea;background:white;
            box-shadow:0 0 0 3px rgba(102,126,234,0.1);
        }
        .form-group.has-error input,
        .form-group.has-error select{
            border-color:#dc3545;background:#fff5f5;
        }
        .error{
            color:#dc3545;font-size:13px;margin-top:6px;
            background:#fff5f5;padding:8px 10px;border-radius:6px;
            border-left:3px solid #dc3545;
        }
        .form-row{
            display:grid;grid-template-columns:1fr 1fr;gap:16px;
        }
        @media(max-width:600px){
            .form-row{grid-template-columns:1fr;}
        }
        .form-actions{
            display:flex;gap:12px;margin-top:32px;
        }
        .btn{
            flex:1;padding:12px 20px;border:none;border-radius:10px;
            font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;
            transition:all 0.2s;
        }
        .btn-primary{
            background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
            color:white;
        }
        .btn-primary:hover{
            transform:translateY(-2px);
            box-shadow:0 4px 12px rgba(102,126,234,0.4);
        }
        .btn-primary:disabled{
            opacity:0.6;cursor:not-allowed;transform:none;
        }
        .btn-secondary{
            background:#f0f4f8;color:#566573;border:1px solid #e0e7ff;
        }
        .btn-secondary:hover{background:#e8ecf1;}
        .flash{
            padding:12px 14px;border-radius:10px;margin-bottom:20px;
            border-left:4px solid;
        }
        .flash.error{
            background:#fff5f5;color:#721c24;border-left-color:#dc3545;
        }
        
        /* Modal styles */
        .modal-overlay{
            display:none;position:fixed;top:0;left:0;right:0;bottom:0;
            background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;
        }
        .modal-overlay.active{display:flex;}
        .modal-box{
            background:white;border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,0.3);
            padding:32px;max-width:420px;width:90%;animation:slideIn 0.3s ease-out;
        }
        @keyframes slideIn{
            from{transform:translateY(-20px);opacity:0;}
            to{transform:translateY(0);opacity:1;}
        }
        .modal-header{
            display:flex;align-items:center;gap:12px;margin-bottom:20px;
        }
        .modal-icon{
            width:44px;height:44px;border-radius:50%;background:#667eea;
            color:white;display:flex;align-items:center;justify-content:center;
            font-size:24px;
        }
        .modal-title{
            font-size:20px;font-weight:700;color:#2c3e50;margin:0;
        }
        .modal-content{
            margin-bottom:24px;
        }
        .modal-field{
            display:flex;justify-content:space-between;align-items:center;
            padding:12px;background:#f8fafc;border-radius:8px;margin-bottom:8px;
            border-left:3px solid #667eea;
        }
        .modal-field-label{
            font-weight:600;color:#566573;font-size:13px;
        }
        .modal-field-value{
            font-size:14px;color:#2c3e50;font-weight:600;
        }
        .modal-actions{
            display:flex;gap:12px;justify-content:flex-end;
        }
        .modal-btn{
            padding:10px 20px;border:none;border-radius:8px;font-weight:600;
            cursor:pointer;transition:all 0.2s;font-size:14px;
        }
        .modal-btn-cancel{
            background:#f0f4f8;color:#566573;border:1px solid #e0e7ff;
        }
        .modal-btn-cancel:hover{background:#e8ecf1;}
        .modal-btn-confirm{
            background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;
        }
        .modal-btn-confirm:hover{
            transform:translateY(-2px);box-shadow:0 4px 12px rgba(102,126,234,0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Agregar Hardware</h1>
            <p>Registra un nuevo componente en el inventario</p>
        </div>
        <div class="content">
            <?php if ($flashError): ?><div class="flash error"><?= h($flashError) ?></div><?php endif; ?>
            
            <form id="createForm" method="post" action="crear.php">
                <div class="form-group <?= isset($errors['id_marca']) ? 'has-error' : '' ?>">
                    <label>Marca <span class="required">*</span></label>
                    <select name="id_marca" id="marca" required>
                        <option value="">-- Seleccione una marca --</option>
                        <!-- opciones se agregan mediante JS según categoría -->
                    </select>
                    <?php if (isset($errors['id_marca'])): ?><div class="error"><?= h(implode(', ', $errors['id_marca'])) ?></div><?php endif; ?>
                </div>

                <div class="form-group <?= isset($errors['modelo']) ? 'has-error' : '' ?>">
                    <label>Modelo/Descripción <span class="required">*</span></label>
                    <input type="text" name="modelo" id="modelo" value="<?= h($data['modelo'] ?? '') ?>" required>
                    <?php if (isset($errors['modelo'])): ?><div class="error"><?= h(implode(', ', $errors['modelo'])) ?></div><?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group <?= isset($errors['precio']) ? 'has-error' : '' ?>">
                        <label>Precio <span class="required">*</span></label>
                        <input type="number" step="0.01" name="precio" id="precio" value="<?= h($data['precio'] ?? '') ?>" required>
                        <?php if (isset($errors['precio'])): ?><div class="error"><?= h(implode(', ', $errors['precio'])) ?></div><?php endif; ?>
                    </div>

                    <div class="form-group <?= isset($errors['stock']) ? 'has-error' : '' ?>">
                        <label>Stock <span class="required">*</span></label>
                        <input type="number" name="stock" id="stock" value="<?= h($data['stock'] ?? '') ?>" required>
                        <?php if (isset($errors['stock'])): ?><div class="error"><?= h(implode(', ', $errors['stock'])) ?></div><?php endif; ?>
                    </div>
                </div>

                <div class="form-group <?= isset($errors['id_categoria']) ? 'has-error' : '' ?>">
                    <label>Categoría <span class="required">*</span></label>
                    <select name="id_categoria" id="categoria" required>
                        <option value="">-- Seleccione una categoría --</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= h($cat['id_categoria']) ?>" <?= (isset($data['id_categoria']) && $data['id_categoria']==$cat['id_categoria']) ? 'selected' : '' ?>>
                                    <?= h($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (isset($errors['id_categoria'])): ?><div class="error"><?= h(implode(', ', $errors['id_categoria'])) ?></div><?php endif; ?>
                </div>

                <div class="form-actions">
                    <button class="btn btn-secondary" type="button" onclick="location.href='index.php'">Cancelar</button>
                    <button class="btn btn-primary" type="button" id="btnSubmit">Confirmar y guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal-overlay" id="confirmModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-icon">✓</div>
                <h2 class="modal-title">Confirmar datos</h2>
            </div>
            <div class="modal-content">
                <p style="color:#566573;margin-bottom:16px;font-size:14px;">
                    Verifica que los datos sean correctos antes de guardar:
                </p>
                <div class="modal-field">
                    <span class="modal-field-label">Marca</span>
                    <span class="modal-field-value" id="confirmMarca"></span>
                </div>
                <div class="modal-field">
                    <span class="modal-field-label">Modelo</span>
                    <span class="modal-field-value" id="confirmModelo"></span>
                </div>
                <div class="modal-field">
                    <span class="modal-field-label">Categoría</span>
                    <span class="modal-field-value" id="confirmCategoria"></span>
                </div>
                <div class="modal-field">
                    <span class="modal-field-label">Precio</span>
                    <span class="modal-field-value" id="confirmPrecio"></span>
                </div>
                <div class="modal-field">
                    <span class="modal-field-label">Stock inicial</span>
                    <span class="modal-field-value" id="confirmStock"></span>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="modal-btn modal-btn-cancel" id="modalCancel">Editar</button>
                <button type="button" class="modal-btn modal-btn-confirm" id="modalConfirm">Guardar hardware</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createForm');
            const btnSubmit = document.getElementById('btnSubmit');
            const confirmModal = document.getElementById('confirmModal');
            const modalCancel = document.getElementById('modalCancel');
            const modalConfirm = document.getElementById('modalConfirm');
            
            const inputs = {
                marca: document.getElementById('marca'),
                modelo: document.getElementById('modelo'),
                precio: document.getElementById('precio'),
                stock: document.getElementById('stock'),
                categoria: document.getElementById('categoria')
            };

            // helper to fill marca select based on category
            const brandsByCategory = <?= json_encode($brandsByCategory) ?>;
            function populateCreateBrands(catId) {
                const sel = inputs.marca;
                sel.innerHTML = '<option value="">-- Seleccione una marca --</option>';
                sel.disabled = !catId;
                if (!catId) return;
                (brandsByCategory[catId]||[]).forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.id_marca;
                    opt.textContent = b.nombre;
                    sel.appendChild(opt);
                });
            }

            inputs.categoria.addEventListener('change', function() {
                populateCreateBrands(this.value);
            });
            // si venimos con categoría preseleccionada (errores de validación), llenar marcas
            if (inputs.categoria.value) {
                populateCreateBrands(inputs.categoria.value);
                if (inputs.marca.value) {
                    // mantener selección previa
                    inputs.marca.value = inputs.marca.value;
                }
            }

            const confirms = {
                marca: document.getElementById('confirmMarca'),
                modelo: document.getElementById('confirmModelo'),
                precio: document.getElementById('confirmPrecio'),
                stock: document.getElementById('confirmStock'),
                categoria: document.getElementById('confirmCategoria')
            };

            // Mostrar modal de confirmación
            btnSubmit.addEventListener('click', function() {
                // Validar campos básicos
                if (!inputs.marca.value.trim()) {
                    inputs.marca.focus();
                    return;
                }
                if (!inputs.modelo.value.trim()) {
                    inputs.modelo.focus();
                    return;
                }
                if (!inputs.precio.value) {
                    inputs.precio.focus();
                    return;
                }
                if (!inputs.stock.value) {
                    inputs.stock.focus();
                    return;
                }
                if (!inputs.categoria.value) {
                    inputs.categoria.focus();
                    return;
                }

                // Llenar modal con datos
                // mostrar nombre de la marca (no su id)
                confirms.marca.textContent = inputs.marca.options[inputs.marca.selectedIndex]?.text || '';
                confirms.modelo.textContent = inputs.modelo.value;
                confirms.precio.textContent = '$' + parseFloat(inputs.precio.value).toFixed(2);
                confirms.stock.textContent = inputs.stock.value + ' unidades';
                
                const selectedCat = inputs.categoria.options[inputs.categoria.selectedIndex];
                confirms.categoria.textContent = selectedCat.text;

                // Mostrar modal
                confirmModal.classList.add('active');
                modalConfirm.focus();
            });

            // Cerrar modal
            function closeModal() {
                confirmModal.classList.remove('active');
            }

            modalCancel.addEventListener('click', closeModal);
            confirmModal.addEventListener('click', function(e) {
                if (e.target === confirmModal) closeModal();
            });

            // Confirmar y enviar AJAX
            modalConfirm.addEventListener('click', function() {
                modalConfirm.disabled = true;
                modalConfirm.style.opacity = '0.6';

                const formData = new FormData(form);
                fetch('/hardware/crear.php', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(r => r.json())
                .then(json => {
                    if (json.success) {
                        showFlash('✓ ' + (json.message || 'Hardware creado exitosamente'), 'success');
                        closeModal();
                        // Limpiar formulario
                        form.reset();
                        inputs.marca.focus();
                    } else {
                        const errMsg = json.error || (json.errors ? JSON.stringify(json.errors) : 'Error desconocido');
                        showFlash('✗ ' + errMsg, 'error');
                    }
                })
                .catch(err => {
                    showFlash('✗ Error de conexión: ' + err.message, 'error');
                })
                .finally(() => {
                    modalConfirm.disabled = false;
                    modalConfirm.style.opacity = '1';
                });
            });

            // Helper para mostrar flash
            function showFlash(message, type = 'success') {
                const container = document.querySelector('.content');
                const div = document.createElement('div');
                div.className = `flash ${type}`;
                div.innerHTML = message;
                container.insertBefore(div, container.firstChild);
                setTimeout(() => div.remove(), 4000);
            }

            // Dismiss flash after 4 seconds
            setTimeout(function(){
                document.querySelectorAll('.flash').forEach(el => el.remove());
            }, 4000);
        });
    </script>
</body>
</html>
