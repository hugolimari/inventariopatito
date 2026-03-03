<?php
/**
 * Script de debug para verificar hashes y password_verify.
 * Ejecutar: php scripts/debug_password.php
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

echo "=== Debug de Contraseñas ===\n\n";

// Conectar
try {
    $cfg = new DatabaseConfig();
    $pdo = new PDO($cfg->getDSN(), $cfg->getUsername(), $cfg->getPassword());
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "✗ Error de conexión: " . $e->getMessage() . "\n";
    exit(1);
}

// Obtener el usuario admin
$stmt = $pdo->query("SELECT id_usuario, username, password_hash FROM usuarios WHERE username = 'admin'");
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "✗ Usuario 'admin' no encontrado.\n";
    exit(1);
}

echo "Usuario encontrado: {$user['username']}\n";
echo "ID: {$user['id_usuario']}\n";
echo "Hash almacenado: {$user['password_hash']}\n\n";

// Intentar verificar con diferentes contraseñas
$passwords = [
    'Admin@123',
    'admin',
    'admin123',
    'Admin123',
    'password',
    'Admin@2026',
];

echo "Probando password_verify() con diferentes contraseñas:\n";
foreach ($passwords as $pwd) {
    $result = password_verify($pwd, $user['password_hash']) ? '✓' : '✗';
    echo "  $result '$pwd'\n";
}

echo "\n";

// Si ninguna funciona, generar un nuevo hash
echo "Si ninguna contraseña funciona, aquí están los hashes generados nuevamente:\n";
foreach ($passwords as $pwd) {
    $new_hash = password_hash($pwd, PASSWORD_DEFAULT);
    echo "\n'$pwd':\n";
    echo "$new_hash\n";
}

echo "\nPara actualizar el usuario admin, ejecuta:\n";
echo "UPDATE usuarios SET password_hash = '[nuevo_hash]' WHERE username = 'admin';\n";
