<?php
/**
 * Registro de nuevo usuario.
 * Variables:
 *   - data (array old input)
 *   - errors (array de validación)
 *   - roles (lista de roles disponibles, excluir admin)
 */

function h($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
$flashSuccess = $this->getFlash('success') ?? null;
$flashError = $this->getFlash('error') ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario - <?= h(APP_NAME) ?></title>
    <style>
        /* reuse similar styles as login/create */
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f5f5f5;padding:20px;}
        .container{max-width:500px;margin:0 auto;background:white;border-radius:15px;box-shadow:0 2px 10px rgba(0,0,0,0.1);overflow:hidden;}
        .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;}
        .header h1{margin:0;font-size:1.5em;}
        .content{padding:20px;}
        .form-group{margin-bottom:15px;}
        label{display:block;margin-bottom:5px;font-weight:500;}
        input,select{width:100%;padding:8px;border:2px solid #e0e0e0;border-radius:6px;transition:border-color .3s;}
        input:focus,select:focus{border-color:#667eea;outline:none;}
        .has-error input,.has-error select{border-color:#dc3545;}
        .error{color:#dc3545;font-size:0.9em;}
        .btn{padding:8px 16px;border:none;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;}
        .btn-primary{background:#667eea;color:white;}
        .btn-secondary{background:#6c757d;color:white;}
        .flash{padding:10px;border-radius:5px;margin-bottom:15px;}
        .flash.success{background:#d4edda;color:#155724;}
        .flash.error{background:#f8d7da;color:#721c24;}
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1>Registrar Usuario</h1></div>
        <div class="content">
            <?php if ($flashSuccess): ?><div class="flash success"><?= h($flashSuccess) ?></div><?php endif; ?>
            <?php if ($flashError): ?><div class="flash error"><?= h($flashError) ?></div><?php endif; ?>
            <form method="post" action="register.php">
                <div class="form-group <?= isset($errors['username']) ? 'has-error' : '' ?>">
                    <label>Usuario <span style="color:#dc3545">*</span></label>
                    <input type="text" name="username" value="<?= h($data['username'] ?? '') ?>">
                    <?php if (isset($errors['username'])): ?><div class="error"><?= h(implode(', ', $errors['username'])) ?></div><?php endif; ?>
                </div>
                <div class="form-group <?= isset($errors['password']) ? 'has-error' : '' ?>">
                    <label>Contraseña <span style="color:#dc3545">*</span></label>
                    <input type="password" name="password">
                    <?php if (isset($errors['password'])): ?><div class="error"><?= h(implode(', ', $errors['password'])) ?></div><?php endif; ?>
                </div>
                <div class="form-group <?= isset($errors['role']) ? 'has-error' : '' ?>">
                    <label>Rol <span style="color:#dc3545">*</span></label>
                    <select name="role">
                        <option value="">-- seleccione --</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= h($role['id_rol']) ?>" <?= (isset($data['role']) && $data['role'] == $role['id_rol']) ? 'selected' : '' ?>>
                                <?= h($role['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['role'])): ?><div class="error"><?= h(implode(', ', $errors['role'])) ?></div><?php endif; ?>
                </div>
                <button class="btn btn-secondary" type="button" onclick="location.href='login.php'">Cancelar</button>
                <button class="btn btn-primary" type="submit">Registrar</button>
            </form>
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