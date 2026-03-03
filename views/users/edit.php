<?php
/**
 * Formulario para editar usuario
 * Variables: user, roles, errors, title, currentUser
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
        .container{max-width:600px;margin:0 auto;background:white;border-radius:15px;box-shadow:0 2px 10px rgba(0,0,0,0.1);overflow:hidden;}
        .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:30px;}
        .header h1{color:white;margin:0;}
        .content{padding:30px;}
        .form-group{margin-bottom:20px;}
        label{display:block;color:#333;font-weight:600;margin-bottom:6px;}
        input[type="text"],input[type="password"],select{width:100%;padding:10px;border:2px solid #e0e0e0;border-radius:6px;font-size:1em;transition:border-color .3s;}
        input[type="text"]:focus,input[type="password"]:focus,select:focus{outline:none;border-color:#667eea;}
        input[type="text"]:disabled{background:#f5f5f5;color:#999;cursor:not-allowed;}
        .help-text{font-size:0.85em;color:#666;margin-top:4px;}
        .form-check{display:flex;align-items:center;gap:10px;margin-bottom:20px;}
        .form-check input[type="checkbox"]{width:18px;height:18px;cursor:pointer;}
        .form-check label{margin:0;cursor:pointer;}
        .buttons{display:flex;gap:10px;margin-top:30px;}
        .btn{padding:10px 20px;border:none;border-radius:6px;font-weight:600;cursor:pointer;text-decoration:none;font-size:1em;transition:background .3s;}
        .btn-primary{background:#667eea;color:white;}
        .btn-primary:hover{background:#5568d3;}
        .btn-secondary{background:#6c757d;color:white;}
        .btn-secondary:hover{background:#5a6268;}
        .errors{background:#f8d7da;color:#721c24;padding:12px;border-radius:6px;margin-bottom:15px;border:1px solid #f5c6cb;}
        .error-list{margin:5px 0 0 20px;padding:0;}
        .flash{padding:12px;border-radius:6px;margin-bottom:15px;}
        .flash.success{background:#d4edda;color:#155724;}
        .flash.error{background:#f8d7da;color:#721c24;}
        .back-link{color:#667eea;text-decoration:none;}
        .back-link:hover{text-decoration:underline;}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= h($title) ?></h1>
            <div style="position:absolute;right:20px;top:20px;color:white;">
                <span><?= h($currentUser['username'] ?? '') ?> <?php if (!empty($currentUser['role_name'])): ?>(<?= h($currentUser['role_name']) ?>)<?php endif; ?></span>
                <div style="margin-top:8px;">
                    <a href="/auth/logout.php" style="color:white;margin-right:10px;">Cerrar sesión</a>
                    <a href="/admin/users.php" style="color:white;">Gestión de Usuarios</a>
                </div>
            </div>
        </div>
        <div class="content">
            <?php if ($flashSuccess): ?><div class="flash success"><?= h($flashSuccess) ?></div><?php endif; ?>
            <?php if ($flashError): ?><div class="flash error"><?= h($flashError) ?></div><?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <strong>Errores de validación:</strong>
                    <ul class="error-list">
                        <?php foreach ($errors as $field => $messages): ?>
                            <?php foreach ($messages as $msg): ?>
                                <li><?= h($msg) ?></li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="/admin/user-edit.php?id=<?= h($user['id_usuario']) ?>">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" value="<?= h($user['username']) ?>" disabled>
                    <span class="help-text">El nombre de usuario no se puede cambiar</span>
                </div>

                <div class="form-group">
                    <label for="id_rol">Rol</label>
                    <select id="id_rol" name="id_rol" required>
                        <option value="">-- Seleccionar rol --</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= h($role['id_rol']) ?>" <?= ($role['id_rol'] == $user['id_rol']) ? 'selected' : '' ?>>
                                <?= h($role['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="estado" name="estado" value="1" <?= ($user['estado'] == 1) ? 'checked' : '' ?>>
                    <label for="estado">Usuario activo</label>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña (dejar en blanco para no cambiar)</label>
                    <input type="password" id="password" name="password" placeholder="Nueva contraseña (opcional)">
                    <span class="help-text">Mínimo 8 caracteres. Incluir mayúsculas, minúsculas, números y caracteres especiales.</span>
                </div>

                <div class="buttons">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <a href="/admin/users.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>

            <div style="margin-top:20px;">
                <a href="/admin/users.php" class="back-link">← Volver a usuarios</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function(){
                document.querySelectorAll('.flash').forEach(el => el.remove());
            }, 3000);
        });
    </script>
</body>
</html>