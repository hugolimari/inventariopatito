<?php
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php';

session_start();
$_SESSION['user'] = ['id'=>1,'username'=>'admin','role'=>3]; // treat admin as technician for test

$container = require __DIR__ . '/config/di-container.php';
$controller = new \App\Controllers\MovementController($container);

// prepare a hardware item
$hwRepo = $container->get('HardwareRepository');
$marcaRepo = $container->get('MarcaRepository');
$hw = $hwRepo->findAll();
if (empty($hw)) {
    $firstBrand = $marcaRepo->findAll()[0] ?? null;
    $brandId = $firstBrand['id_marca'] ?? 0;
    $id = $hwRepo->save(['id_categoria'=>1,'id_marca'=>$brandId,'modelo'=>'Y','precio'=>1,'stock'=>3]);
} else {
    $id = $hw[0]['id_hardware'];
    // reset stock to 3
    $pdo = $container->get('PDO');
    $pdo->exec("UPDATE hardware SET stock=3 WHERE id_hardware=$id");
}

// GET request
$_SERVER['REQUEST_METHOD']='GET';
ob_start();
$controller->salida($id);
$content = ob_get_clean();
echo "GET salida page length: " . strlen($content) . "\n";

// POST invalid quantity
$_SERVER['REQUEST_METHOD']='POST';
$_POST['cantidad']='5';
ob_start();
$controller->salida($id);
$cont2 = ob_get_clean();
echo "POST invalid quantity (too high) length: " . strlen($cont2) . "\n";

// POST valid quantity
$_POST['cantidad']='2';
ob_start();
$controller->salida($id);
$cont3 = ob_get_clean();
echo "POST valid quantity length (should redirect) : " . strlen($cont3) . "\n";

// verify stock decreased
$after = $hwRepo->findById($id);
echo "Stock now: " . $after['stock'] . "\n";

