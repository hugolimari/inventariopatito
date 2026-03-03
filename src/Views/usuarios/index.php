<?php
/**
 * Vista: Lista de usuarios (solo admin).
 * Variables: $usuarios (array), $msg (string|null)
 * Se incluye dentro de layout.php.
 */
?>

<?php if ($msg === 'creado'): ?>
    <div class="alert alert-success">✅ Usuario registrado exitosamente.</div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1>👥 Gestión de Usuarios</h1>
        <span class="subtitle">Administrar cuentas del sistema</span>
    </div>
    <a href="<?= BASE_URL ?>?controller=usuario&action=crear" class="btn btn-primary">➕ Registrar Usuario</a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Username</th>
                <th>Rol</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= $u->getId() ?></td>
                    <td><strong><?= htmlspecialchars($u->getNombreCompleto()) ?></strong></td>
                    <td><?= htmlspecialchars($u->getUsername()) ?></td>
                    <td>
                        <span class="badge <?= $u->esAdmin() ? 'badge-info' : 'badge-success' ?>">
                            <?= htmlspecialchars($u->getRol()) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
