<?php
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php';

$cfg = new DatabaseConfig();
$pdo = new PDO($cfg->getDSN(), $cfg->getUsername(), $cfg->getPassword());
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$authRepo = new \App\Repositories\AuthRepository($pdo);

echo "Testing user creation...\n";
try {
    $newId = $authRepo->createUser('testuser', password_hash('secret123', PASSWORD_DEFAULT), 2);
    echo "User created with ID $newId\n";
    // cleanup
    $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id_usuario = :id');
    $stmt->execute(['id' => $newId]);
    echo "Cleanup done.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
