<?php
/**
 * Lista de usuarios registrados en el sistema
 * Variables: users, search, title, currentUser
 */

function h($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
$flashSuccess = $this->getFlash('success') ?? null;
$flashError = $this->getFlash('error') ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= h($title) ?> - <?= h(APP_NAME) ?></title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f5f5f5;padding:20px;}
        .container{max-width:1200px;margin:0 auto;background:white;border-radius:15px;box-shadow:0 2px 10px rgba(0,0,0,0.1);overflow:hidden;}
        h1{color:#667eea;font-size:1.8em;margin-bottom:10px;}
        .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:30px;}
        .header h1{color:white;margin:0;}
        .content{padding:20px;}
        .user-info{text-align:right;margin-bottom:15px;font-size:0.95em;}
        .user-info a{color:#667eea;text-decoration:none;margin-left:15px;}
        table{width:100%;border-collapse:collapse;margin-top:20px;}
        th{background:#f8f9fa;color:#667eea;padding:12px;text-align:left;font-weight:600;border-bottom:2px solid #667eea;}
        td{padding:12px;border-bottom:1px solid #e9ecef;}
        tr:hover{background:#f8f9fa;}
        .badge{display:inline-block;padding:4px 10px;border-radius:12px;font-size:0.85em;font-weight:bold;}
        .badge-admin{background:#dc3545;color:white;}
        .badge-almacenero{background:#17a2b8;color:white;}
        .badge-tecnico{background:#28a745;color:white;}
        .badge-active{background:#d4edda;color:#155724;}
        .badge-inactive{background:#f8d7da;color:#721c24;}
        .btn{display:inline-block;padding:6px 12px;border:none;border-radius:6px;font-size:0.9em;font-weight:600;cursor:pointer;text-decoration:none;transition:background .3s;}
        .btn-edit{background:#667eea;color:white;margin-right:5px;}
        .btn-edit:hover{background:#5568d3;}
        .btn-toggle{background:#ffc107;color:#333;margin-right:5px;}
        .btn-toggle:hover{background:#ffb300;}
        .btn-delete{background:#dc3545;color:white;}
        .btn-delete:hover{background:#c82333;}
        .search-form{margin-bottom:20px;}
        .search-form input{padding:8px 12px;border:2px solid #e0e0e0;border-radius:6px;width:250px;transition:border-color .3s;}
        .search-form input:focus{outline:none;border-color:#667eea;}
        .search-form button{padding:8px 20px;background:#667eea;color:white;border:none;border-radius:6px;cursor:pointer;font-weight:600;}
        .search-form button:hover{background:#5568d3;}
        .flash{padding:12px;border-radius:6px;margin-bottom:15px;}
        .flash.success{background:#d4edda;color:#155724;}
        .flash.error{background:#f8d7da;color:#721c24;}
        /* modal styles (copied from hardware list) */
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
        .modal-errors {
            background:#fff5f5;border-left:4px solid #dc3545;padding:12px;border-radius:8px;
            color:#721c24;font-size:13px;margin-bottom:16px;display:none;
        }
        .modal-errors.show { display:block; }
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= h($title) ?></h1>
        </div>
        <div class="content">
            <div class="user-info" style="display:flex;justify-content:space-between;align-items:center;">
                <div>
                    Usuario: <strong><?= h($currentUser['username']) ?></strong>
                    <?php if (!empty($currentUser['role_name'])): ?>
                        (<?= h($currentUser['role_name']) ?>)
                    <?php endif; ?>
                </div>
                <div>
                    <a href="/hardware/index.php">Inventario</a>
                    <a href="/auth/logout.php">Cerrar sesión</a>
                    <a href="#" id="btnRegister">Registrar usuario</a>
                    <a href="/admin/users.php">Gestión de Usuarios</a>
                </div>
            </div>

            <?php if ($flashSuccess): ?><div class="flash success"><?= h($flashSuccess) ?></div><?php endif; ?>
            <?php if ($flashError): ?><div class="flash error"><?= h($flashError) ?></div><?php endif; ?>

            <form method="get" class="search-form">
                <input type="text" name="search" value="<?= h($search) ?>" placeholder="Buscar por usuario o rol">
                <button type="submit">Buscar</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= h($user['id_usuario']) ?></td>
                        <td><?= h($user['username']) ?></td>
                        <td>
                            <span class="badge badge-<?= strtolower(str_replace(' ', '-', $user['rol_nombre'] ?? '')) ?>">
                                <?= h($user['rol_nombre'] ?? 'Sin rol') ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= ($user['estado'] == 1) ? 'badge-active' : 'badge-inactive' ?>">
                                <?= ($user['estado'] == 1) ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td><?= h(substr($user['created_at'], 0, 10)) ?></td>
                        <td>
                            <a class="btn btn-edit" href="/admin/user-edit.php?id=<?= h($user['id_usuario']) ?>">Editar</a>
                            <a class="btn btn-toggle" href="/admin/user-toggle.php?id=<?= h($user['id_usuario']) ?>" 
                               onclick="return confirm('¿Cambiar estado?');">
                                <?= ($user['estado'] == 1) ? 'Desactivar' : 'Activar' ?>
                            </a>
                            <a class="btn btn-delete" href="/admin/user-delete.php?id=<?= h($user['id_usuario']) ?>" 
                               onclick="return confirm('¿Eliminar este usuario?');">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Registro modal -->
        <div class="modal-overlay" id="registerModal">
            <div class="modal-box">
                <div class="modal-header">
                    <div class="modal-icon">👤</div>
                    <h2 class="modal-title">Registrar Usuario</h2>
                </div>
                <div class="modal-errors" id="registerErrors"></div>
                <form id="registerForm">
                    <div class="modal-input-group">
                        <label>Usuario <span style="color:#dc3545">*</span></label>
                        <input type="text" id="regUsername" name="username" required>
                    </div>
                    <div class="modal-input-group">
                        <label>Contraseña <span style="color:#dc3545">*</span></label>
                        <input type="password" id="regPassword" name="password" required>
                    </div>
                    <div class="modal-input-group">
                        <label>Rol <span style="color:#dc3545">*</span></label>
                        <select id="regRole" name="role" required>
                            <option value="">-- seleccione --</option>
                            <?php if (!empty($roles)): ?>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= h($role['id_rol']) ?>"><?= h($role['nombre']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </form>
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" id="registerCancel">Cancelar</button>
                    <button type="button" class="modal-btn modal-btn-confirm" id="registerConfirm">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // flash message helper (allows dynamic messages when using AJAX)
        function showFlash(message, type='success') {
            const container = document.querySelector('.container');
            const div = document.createElement('div');
            div.className = `flash ${type}`;
            div.textContent = message;
            container.insertBefore(div, container.firstChild);
            setTimeout(() => div.remove(), 4000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function(){
                document.querySelectorAll('.flash').forEach(el => el.remove());
            }, 3000);

            const btnRegister = document.getElementById('btnRegister');
            const registerModal = document.getElementById('registerModal');
            const registerCancel = document.getElementById('registerCancel');
            const registerConfirm = document.getElementById('registerConfirm');
            const registerErrors = document.getElementById('registerErrors');

            if (btnRegister) {
                btnRegister.addEventListener('click', function(e) {
                    e.preventDefault();
                    registerErrors.classList.remove('show');
                    registerErrors.textContent = '';
                    document.getElementById('regUsername').value = '';
                    document.getElementById('regPassword').value = '';
                    document.getElementById('regRole').value = '';
                    registerModal.classList.add('active');
                });
            }

            function closeRegisterModal() {
                registerModal.classList.remove('active');
            }
            registerCancel.addEventListener('click', closeRegisterModal);
            registerModal.addEventListener('click', function(e) {
                if (e.target === registerModal) closeRegisterModal();
            });

            registerConfirm.addEventListener('click', function() {
                const username = document.getElementById('regUsername').value.trim();
                const password = document.getElementById('regPassword').value.trim();
                const role = document.getElementById('regRole').value;

                if (!username || !password || !role) {
                    registerErrors.textContent = 'Completa todos los campos obligatorios';
                    registerErrors.classList.add('show');
                    return;
                }

                const formData = new FormData(document.getElementById('registerForm'));

                registerConfirm.disabled = true;
                registerConfirm.style.opacity = '0.6';

                fetch('/auth/register.php', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(r => r.json())
                .then(json => {
                    if (json.success) {
                        showFlash('Usuario creado correctamente', 'success');
                        closeRegisterModal();
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        const errMsg = json.error || (json.errors ? JSON.stringify(json.errors) : 'Error desconocido');
                        registerErrors.textContent = errMsg;
                        registerErrors.classList.add('show');
                    }
                })
                .catch(err => {
                    registerErrors.textContent = 'Error de conexión: ' + err.message;
                    registerErrors.classList.add('show');
                })
                .finally(() => {
                    registerConfirm.disabled = false;
                    registerConfirm.style.opacity = '1';
                });
            });
        });
    </script>
</body>
</html>
