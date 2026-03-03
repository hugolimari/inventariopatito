<?php
// simulate admin session and call register method
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php';

session_start();
$_SESSION['user'] = ['id'=>1,'username'=>'admin','role'=>1];

$container = require __DIR__ . '/config/di-container.php';
$controller = new \App\Controllers\AuthController($container);

// simulate GET
$_SERVER['REQUEST_METHOD'] = 'GET';
ob_start();
$controller->register();
$output = ob_get_clean();

echo "Rendered register page length: " . strlen($output) . " bytes\n";

// simulate POST with invalid data
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['username'] = '';
$_POST['password'] = '';
$_POST['role'] = '';
ob_start();
$controller->register();
$output2 = ob_get_clean();
echo "Post with invalid data returned, length: " . strlen($output2) . " bytes\n";

// simulate POST with valid data
$_POST['username'] = 'newuser';
$_POST['password'] = 'mypass123';
$_POST['role'] = '2';
ob_start();
$controller->register();
$output3 = ob_get_clean();
echo "Post with valid data (should redirect) length: " . strlen($output3) . " bytes\n";

// verify insertion
$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE username = :u');
$stmt->execute(['u' => 'newuser']);
$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
if ($userRow) {
    echo "User inserted with id {$userRow['id_usuario']} and role {$userRow['id_rol']}\n";
    // cleanup
    $pdo->prepare('DELETE FROM usuarios WHERE id_usuario = :id')->execute(['id'=>$userRow['id_usuario']]);
    echo "Cleanup done\n";
} else {
    echo "Insertion failed\n";
}

echo "Done\n";