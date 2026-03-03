<?php
/**
 * Script de diagnóstico para verificar la conexión a BD y usuarios disponibles.
 * Ejecutar desde línea de comandos: php scripts/check_db.php
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

echo "=== Diagnóstico de Base de Datos ===\n\n";

// Intentar conexión
try {
    $cfg = new DatabaseConfig();
    $pdo = new PDO($cfg->getDSN(), $cfg->getUsername(), $cfg->getPassword());
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Conexión a BD exitosa.\n";
    echo "  Host: {$cfg->getHost()}\n";
    echo "  BD: {$cfg->getDatabase()}\n\n";
} catch (PDOException $e) {
    echo "✗ Error de conexión: " . $e->getMessage() . "\n";
    exit(1);
}

// Verificar tabla usuarios
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
    $count = $stmt->fetchColumn();
    echo "✓ Tabla 'usuarios' existe.\n";
    echo "  Total de usuarios: $count\n\n";
} catch (PDOException $e) {
    echo "✗ Error al consultar tabla 'usuarios': " . $e->getMessage() . "\n";
    exit(1);
}

// Listar usuarios
if ($count > 0) {
    echo "Usuarios disponibles:\n";
    $stmt = $pdo->query("SELECT id_usuario, username, id_rol FROM usuarios");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['username']} (ID: {$row['id_usuario']}, Rol: {$row['id_rol']})\n";
    }
} else {
    echo "⚠ No hay usuarios en la base de datos.\n";
    echo "  Ejecuta: mysql -u root < insert_test_users.sql\n";
}

echo "\n✓ Diagnóstico completado.\n";
