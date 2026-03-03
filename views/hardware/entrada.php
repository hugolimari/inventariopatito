<?php
/**
 * Formulario para registrar entrada de un componente
 * Variables esperadas:
 *   - id
 *   - hardware (array con datos del componente)
 *   - data (cantidad, observacion)
 *   - errors (array)
 *   - currentUser
 */

function h($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
$flashSuccess = $this->getFlash('success') ?? null;
$flashError = $this->getFlash('error') ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar entrada - <?= h(APP_NAME) ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;padding:20px;}
        .container{max-width:600px;margin:0 auto;background:white;border-radius:15px;box-shadow:0 10px 40px rgba(0,0,0,0.2);overflow:hidden;}
        .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:25px;display:flex;justify-content:space-between;align-items:center;}
        .header h1{margin:0;font-size:1.5em;}
        .header .userinfo{font-size:0.9em;opacity:0.9;}
        .content{padding:30px;}
        .hardware-info{background:#f8f9fa;padding:15px;border-radius:8px;margin-bottom:20px;border-left:4px solid #667eea;}
        .hardware-info strong{color:#667eea;}
        .form-group{margin-bottom:15px;}
        label{display:block;margin-bottom:5px;font-weight:600;color:#2c3e50;}
        input[type=text],input[type=number]{width:100%;padding:10px;border:2px solid #e0e0e0;border-radius:6px;font-size:14px;transition:border-color .3s;}
        input:focus{border-color:#667eea;outline:none;box-shadow:0 0 0 3px rgba(102,126,234,0.1);}
        .has-error input{border-color:#dc3545;}
        .error{color:#dc3545;font-size:0.9em;margin-top:3px;}
        .required{color:#dc3545;}
        .btn{padding:10px 20px;border:none;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;transition:background .3s;}
        .btn-primary{background:#667eea;color:white;}
        .btn-primary:hover{background:#5568d3;}
        .btn-secondary{background:#6c757d;color:white;}
        .btn-secondary:hover{background:#5a6268;}
        .button-group{display:flex;gap:10px;margin-top:20px;}
        .flash{padding:12px;border-radius:5px;margin-bottom:15px;font-weight:500;}
        .flash.success{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}
        .flash.error{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Registrar entrada</h1>
                <p style="font-size:0.9em;margin-top:5px;">Reabastecimiento de componentes</p>
            </div>
            <?php if (!empty($currentUser)): ?>
                <div class="userinfo">
                    <strong><?= h($currentUser['username']) ?></strong><br>
                    <?php if (!empty($currentUser['role_name'])): ?>
                        <?= h($currentUser['role_name']) ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="content">
            <?php if ($flashSuccess): ?><div class="flash success"><?= h($flashSuccess) ?></div><?php endif; ?>
            <?php if ($flashError): ?><div class="flash error"><?= h($flashError) ?></div><?php endif; ?>

            <?php if (!empty($hardware)): ?>
                <div class="hardware-info">
                    <strong>Componente:</strong> <?= h($hardware['marca'] ?? '') ?> <?= h($hardware['modelo'] ?? '') ?><br>
                    <strong>Categoría:</strong> <?= h($hardware['categoria_nombre'] ?? '') ?><br>
                    <strong>Stock actual:</strong> <?= $hardware['stock'] ?? 0 ?>
                </div>
            <?php endif; ?>

            <form method="post" action="entrada.php?id=<?= h((string)$id) ?>">
                <div class="form-group <?= isset($errors['cantidad']) ? 'has-error' : '' ?>">
                    <label>Cantidad a agregar <span class="required">*</span></label>
                    <input type="number" name="cantidad" value="<?= h($data['cantidad'] ?? '') ?>" min="1" required>
                    <?php if (isset($errors['cantidad'])): ?><div class="error"><?= h(implode(', ', $errors['cantidad'])) ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Observación (ej: nº factura, proveedor)</label>
                    <input type="text" name="observacion" value="<?= h($data['observacion'] ?? '') ?>" placeholder="Ej: Factura FV-2026-001 Proveedor ABC">
                </div>
                <div class="button-group">
                    <button class="btn btn-secondary" type="button" onclick="location.href='index.php'">Cancelar</button>
                    <button class="btn btn-primary" type="submit">Registrar entrada</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function(){
                document.querySelectorAll('.flash').forEach(el => el.style.display = 'none');
            }, 3000);
        });
    </script>
</body>
</html>
