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
                    <a href="/auth/register.php">Registrar usuario</a>
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