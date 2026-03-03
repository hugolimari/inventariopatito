<?php
/**
 * Vista: Formulario para registrar un nuevo usuario.
 * Variables: $datos (array), $errores (array)
 * Se incluye dentro de layout.php.
 */
?>

<div class="page-header">
    <div>
        <h1>➕ Registrar Nuevo Usuario</h1>
        <span class="subtitle">Crear una cuenta en el sistema</span>
    </div>
    <a href="<?= BASE_URL ?>?controller=usuario&action=index" class="btn btn-secondary">← Volver a usuarios</a>
</div>

<div class="card" style="max-width: 600px;">
    <form method="POST" action="<?= BASE_URL ?>?controller=usuario&action=guardar">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="form-group <?= isset($errores['nombre_completo']) ? 'has-error' : '' ?>">
            <label>Nombre Completo <span class="required">*</span></label>
            <input type="text" name="nombre_completo" value="<?= htmlspecialchars($datos['nombre_completo'] ?? '') ?>" placeholder="Ej: Juan Pérez">
            <?php if (isset($errores['nombre_completo'])): ?>
                <?php foreach ($errores['nombre_completo'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="form-group <?= isset($errores['username']) ? 'has-error' : '' ?>">
            <label>Username <span class="required">*</span></label>
            <input type="text" name="username" value="<?= htmlspecialchars($datos['username'] ?? '') ?>" placeholder="Ej: jperez">
            <?php if (isset($errores['username'])): ?>
                <?php foreach ($errores['username'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="form-group <?= isset($errores['password']) ? 'has-error' : '' ?>">
            <label>Contraseña <span class="required">*</span></label>
            <input type="password" name="password" placeholder="Mínimo 6 caracteres">
            <?php if (isset($errores['password'])): ?>
                <?php foreach ($errores['password'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="form-group <?= isset($errores['rol']) ? 'has-error' : '' ?>">
            <label>Rol <span class="required">*</span></label>
            <select name="rol">
                <option value="almacenero" <?= ($datos['rol'] ?? 'almacenero') === 'almacenero' ? 'selected' : '' ?>>Almacenero</option>
                <option value="admin" <?= ($datos['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>
            <?php if (isset($errores['rol'])): ?>
                <?php foreach ($errores['rol'] as $e): ?><div class="error-text"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div style="margin-top: 24px; display: flex; gap: 10px;">
            <a href="<?= BASE_URL ?>?controller=usuario&action=index" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Registrar Usuario</button>
        </div>
    </form>
</div>
