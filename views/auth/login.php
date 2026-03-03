<?php
/**
 * Formulario de acceso al sistema
 * Variables disponibles:
 *   - error (string) mensaje de error opcional
 */
function h($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - <?= h(APP_NAME) ?></title>
    <style>
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f5f5f5;padding:50px;}
        .card{width:320px;margin:0 auto;background:white;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        h1{text-align:center;color:#667eea;font-size:1.5em;margin-bottom:20px;}
        .form-group{margin-bottom:15px;}
        label{display:block;margin-bottom:5px;color:#333;}
        input{width:100%;padding:8px;border:1px solid #ccc;border-radius:5px;}
        .btn{width:100%;padding:10px;background:#667eea;color:white;border:none;border-radius:5px;font-size:1em;cursor:pointer;}
        .error{color:#dc3545;margin-bottom:15px;text-align:center;}
    </style>
</head>
<body>
    <div class="card">
        <h1>Acceder</h1>
        <?php if (!empty($error)): ?>
            <div class="error"><?= h($error) ?></div>
        <?php endif; ?>
        <form method="post" action="login.php">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <button class="btn" type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>