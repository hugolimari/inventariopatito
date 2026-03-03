<?php
/**
 * Edit hardware form
 * Variables: item, categories, errors, title, currentUser
 */

function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
$flashSuccess = $this->getFlash('success') ?? null;
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? 'Editar componente') ?> - <?= h(APP_NAME) ?></title>
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
            color:white;padding:40px 30px;
        }
        .header-top{
            display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;
        }
        .header-title h1{
            font-size:28px;margin-bottom:8px;font-weight:700;
        }
        .header-user{
            text-align:right;font-size:13px;opacity:0.9;
        }
        .header-user strong{display:block;font-size:14px;margin-bottom:4px;}
        .header-links{
            margin-top:12px;display:flex;flex-direction:column;gap:6px;
        }
        .header-links a{
            color:white;text-decoration:none;font-size:12px;opacity:0.8;
        }
        .header-links a:hover{opacity:1;}
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
        input:disabled{
            background:#e8ecf1;color:#999;
        }
        .form-group.has-error input,
        .form-group.has-error select{
            border-color:#dc3545;background:#fff5f5;
        }
        .errors{
            background:#fff5f5;border-left:4px solid #dc3545;
            color:#721c24;padding:12px 14px;border-radius:10px;
            margin-bottom:20px;font-size:13px;
        }
        .errors ul{margin:8px 0 0 20px;}
        .errors li{margin:4px 0;}
        .flash{
            padding:12px 14px;border-radius:10px;margin-bottom:20px;
            border-left:4px solid;font-size:13px;
        }
        .flash.success{
            background:#d4edda;color:#155724;border-left-color:#28a745;
        }
        .flash.error{
            background:#fff5f5;color:#721c24;border-left-color:#dc3545;
        }
        .form-actions{
            display:flex;gap:12px;margin-top:32px;
        }
        .btn{
            flex:1;padding:12px 20px;border:none;border-radius:10px;
            font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;
            transition:all 0.2s;text-align:center;
        }
        .btn-primary{
            background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
            color:white;
        }
        .btn-primary:hover{
            transform:translateY(-2px);
            box-shadow:0 4px 12px rgba(102,126,234,0.4);
        }
        .btn-secondary{
            background:#f0f4f8;color:#566573;border:1px solid #e0e7ff;
        }
        .btn-secondary:hover{background:#e8ecf1;}
        
        /* Modal */
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
        .modal-content{margin-bottom:24px;}
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
            <div class="header-top">
                <div class="header-title">
                    <h1><?= h($title ?? 'Editar componente') ?></h1>
                </div>
                <div class="header-user">
                    <strong><?= h($currentUser['username'] ?? '') ?></strong>
                    <?php if (!empty($currentUser['role_name'])): ?>
                        <div><?= h($currentUser['role_name']) ?></div>
                    <?php endif; ?>
                    <div class="header-links">
                        <a href="/auth/logout.php">Cerrar sesión</a>
                        <a href="/admin/users.php">Gestión de Usuarios</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <?php if ($flashSuccess): ?><div class="flash success">✓ <?= h($flashSuccess) ?></div><?php endif; ?>
            <?php if ($flashError): ?><div class="flash error">✗ <?= h($flashError) ?></div><?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <strong>Errores encontrados:</strong>
                    <ul><?php foreach ($errors as $field => $msgs): foreach ($msgs as $m): ?><li><?= h($m) ?></li><?php endforeach; endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form id="editForm" method="post" action="/hardware/editar.php?id=<?= h($item['id_hardware']) ?>">
                <div class="form-group">
                    <label>ID interno</label>
                    <input type="text" value="<?= h($item['id_hardware']) ?>" disabled>
                </div>

                <div class="form-group <?= isset($errors['id_categoria']) ? 'has-error' : '' ?>">
                    <label for="id_categoria">Categoría <span class="required">*</span></label>
                    <select id="id_categoria" name="id_categoria" required>
                        <option value="">-- Seleccionar categoría --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= h($c['id_categoria']) ?>" <?= ($c['id_categoria'] == ($item['id_categoria'] ?? '')) ? 'selected' : '' ?>>
                                <?= h($c['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['id_categoria'])): ?><div class="error" style="color:#dc3545;font-size:13px;margin-top:4px;"><?= h(implode(', ', $errors['id_categoria'])) ?></div><?php endif; ?>
                </div>

                <div class="form-group <?= isset($errors['id_marca']) ? 'has-error' : '' ?>">
                    <label for="id_marca">Marca <span class="required">*</span></label>
                    <select id="id_marca" name="id_marca" required>
                        <option value="">-- Seleccione una marca --</option>
                        <?php
                        // si ya hay categoría seleccionada, mostrar sus marcas
                        $selectedCat = $item['id_categoria'] ?? null;
                        if ($selectedCat && isset($brandsByCategory[$selectedCat])):
                            foreach ($brandsByCategory[$selectedCat] as $b): ?>
                                <option value="<?= h($b['id_marca']) ?>" <?= ($b['id_marca'] == ($item['id_marca'] ?? '')) ? 'selected' : '' ?>>
                                    <?= h($b['nombre']) ?>
                                </option>
                            <?php endforeach;
                        endif;
                        ?>
                    </select>
                    <?php if (isset($errors['id_marca'])): ?><div class="error" style="color:#dc3545;font-size:13px;margin-top:4px;"><?= h(implode(', ', $errors['id_marca'])) ?></div><?php endif; ?>
                </div>

                <div class="form-group <?= isset($errors['modelo']) ? 'has-error' : '' ?>">
                    <label for="modelo">Descripción (modelo) <span class="required">*</span></label>
                    <input id="modelo" name="modelo" value="<?= h($item['modelo'] ?? '') ?>" required>
                    <?php if (isset($errors['modelo'])): ?><div class="error" style="color:#dc3545;font-size:13px;margin-top:4px;"><?= h(implode(', ', $errors['modelo'])) ?></div><?php endif; ?>
                </div>

                <div class="form-actions">
                    <a href="/hardware/index.php" class="btn btn-secondary">Cancelar</a>
                    <button type="button" class="btn btn-primary" id="btnSubmit">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal-overlay" id="confirmModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-icon">✓</div>
                <h2 class="modal-title">Confirmar cambios</h2>
            </div>
            <div class="modal-content">
                <p style="color:#566573;margin-bottom:16px;font-size:14px;">
                    Verifica que los cambios sean correctos:
                </p>
                <div class="modal-field">
                    <span class="modal-field-label">ID</span>
                    <span class="modal-field-value"><?= h($item['id_hardware']) ?></span>
                </div>
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
            </div>
            <div class="modal-actions">
                <button type="button" class="modal-btn modal-btn-cancel" id="modalCancel">Editar</button>
                <button type="button" class="modal-btn modal-btn-confirm" id="modalConfirm">Guardar cambios</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editForm');
            const btnSubmit = document.getElementById('btnSubmit');
            const confirmModal = document.getElementById('confirmModal');
            const modalCancel = document.getElementById('modalCancel');
            const modalConfirm = document.getElementById('modalConfirm');
            
            const modeloInput = document.getElementById('modelo');
            const categoriaSelect = document.getElementById('id_categoria');
            const marcaSelect = document.getElementById('id_marca');
            const confirmMarca = document.getElementById('confirmMarca');
            const confirmModelo = document.getElementById('confirmModelo');
            const confirmCategoria = document.getElementById('confirmCategoria');

            // marca helper
            const brandsByCategory = <?= json_encode($brandsByCategory) ?>;
            function populateEditBrands(catId, selectedId=null) {
                const sel = marcaSelect;
                sel.innerHTML = '<option value="">-- Seleccione una marca --</option>';
                sel.disabled = !catId;
                if (!catId) return;
                (brandsByCategory[catId]||[]).forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.id_marca;
                    opt.textContent = b.nombre;
                    if (selectedId && selectedId == b.id_marca) opt.selected = true;
                    sel.appendChild(opt);
                });
            }
            // llenar marcas inicialmente con la categoría/brand existentes
            populateEditBrands(categoriaSelect.value, <?= json_encode($item['id_marca'] ?? null) ?>);
            // actualizar marcas cuando cambia categoría
            categoriaSelect.addEventListener('change', function() {
                populateEditBrands(this.value);
            });

            btnSubmit.addEventListener('click', function() {
                if (!marcaSelect.value) {
                    marcaSelect.focus();
                    return;
                }
                if (!modeloInput.value.trim()) {
                    modeloInput.focus();
                    return;
                }
                if (!categoriaSelect.value) {
                    categoriaSelect.focus();
                    return;
                }

                // mostrar nombre de marca y modelo
                confirmMarca.textContent = marcaSelect.options[marcaSelect.selectedIndex]?.text || '';
                confirmModelo.textContent = modeloInput.value;
                const selectedCat = categoriaSelect.options[categoriaSelect.selectedIndex];
                confirmCategoria.textContent = selectedCat.text;

                confirmModal.classList.add('active');
                modalConfirm.focus();
            });

            function closeModal() {
                confirmModal.classList.remove('active');
            }

            modalCancel.addEventListener('click', closeModal);
            confirmModal.addEventListener('click', function(e) {
                if (e.target === confirmModal) closeModal();
            });

            modalConfirm.addEventListener('click', function() {
                modalConfirm.disabled = true;
                modalConfirm.style.opacity = '0.6';

                const formData = new FormData(form);
                const urlParams = new URLSearchParams(window.location.search);
                const hwId = urlParams.get('id');

                fetch(`/hardware/editar.php?id=${hwId}`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(r => r.json())
                .then(json => {
                    if (json.success) {
                        showFlash('✓ ' + (json.message || 'Componente actualizado correctamente'), 'success');
                        closeModal();
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

            function showFlash(message, type = 'success') {
                const content = document.querySelector('.content');
                const div = document.createElement('div');
                div.className = `flash ${type}`;
                div.innerHTML = message;
                content.insertBefore(div, content.firstChild);
                setTimeout(() => div.remove(), 4000);
            }

            setTimeout(function(){
                document.querySelectorAll('.flash').forEach(el => el.remove());
            }, 4000);
        });
    </script>
</body>
</html>
