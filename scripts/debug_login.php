<?php
/**
 * Script de login debug - muestra exactamente qué se recibe y verifica
 * Crear un archivo test_login.php en public/auth/ y acceder a:
 * http://localhost:8000/public/auth/test_login.php
 * 
 * O ejecutar desde CLI:
 * php scripts/debug_login.php admin "Admin@2026"
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

if ($argc < 3) {
    echo "Uso: php debug_login.php <username> <password>\n";
    echo "Ejemplo: php debug_login.php admin \"Admin@2026\"\n";
    exit(1);
}

$username = $argv[1];
$password = $argv[2];

echo "=== Debug de Login ===\n\n";
echo "Datos enviados:\n";
echo "  Username: '$username'\n";
echo "  Password: '$password'\n";
echo "  Password length: " . strlen($password) . "\n\n";

// Conectar
try {
    $cfg = new DatabaseConfig();
    $pdo = new PDO($cfg->getDSN(), $cfg->getUsername(), $cfg->getPassword());
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "✗ Error de conexión: " . $e->getMessage() . "\n";
    exit(1);
}

// Buscar usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = :u");
$stmt->execute(['u' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "✗ Usuario '$username' no existe.\n";
    exit(1);
}

echo "Usuario encontrado:\n";
echo "  ID: {$user['id_usuario']}\n";
echo "  Username: {$user['username']}\n";
echo "  Estado: " . ($user['estado'] ? 'ACTIVO' : 'INACTIVO') . "\n";
echo "  Hash almacenado: {$user['password_hash']}\n\n";

// Verificar contraseña
$verify = password_verify($password, $user['password_hash']);
echo "Verificación de contraseña:\n";
echo "  password_verify() resultado: " . ($verify ? '✓ TRUE' : '✗ FALSE') . "\n\n";

if ($verify) {
    echo "✓ LOGIN EXITOSO\n";
} else {
    echo "✗ LOGIN FALLIDO - Contraseña incorrecta\n";
}
