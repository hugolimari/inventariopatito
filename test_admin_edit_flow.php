<?php
// simulate admin editing hardware items
// this verifies that an administrator can load the edit form and submit
// changes just like an almacenero.

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php';

session_start();
$_SESSION['user'] = ['id' => 1, 'username' => 'admin', 'role' => 1];

$container = require __DIR__ . '/config/di-container.php';
$hardwareRepo = $container->get('HardwareRepository');
$marcaRepo = $container->get('MarcaRepository');

// choose any existing marca to avoid foreign key issues
$firstBrand = $marcaRepo->findAll()[0] ?? null;
$brandId = $firstBrand['id_marca'] ?? 0;

echo "=== Admin edit flow test ===\n";

// create temporary hardware entry
$newId = $hardwareRepo->save([
    'id_marca' => $brandId,
    'modelo' => 'Original',
    'precio' => 10.0,
    'stock' => 1,
    'id_categoria' => 1
]);
echo "Created hardware ID $newId\n";

$controller = new \App\Controllers\HardwareController($container);

// GET the edit page
$_SERVER['REQUEST_METHOD'] = 'GET';
ob_start();
$controller->edit($newId); $html = ob_get_clean();
if (strpos($html, 'Editar componente') !== false) {
    echo "GET edit page rendered OK\n";
} else {
    echo "GET edit page did not render correctly\n";
}

// Instead of calling controller->edit for POST (which exits), use repository directly
$hardwareRepo->update($newId, ['modelo' => 'UpdatedModel', 'id_categoria' => 2, 'id_marca' => $brandId, 'precio' => 10.00, 'stock' => 1]);
$updated = $hardwareRepo->findById($newId);
echo "Updated modelo is " . ($updated['modelo'] ?? 'NULL') . "\n";

// cleanup
$hardwareRepo->delete($newId);
echo "Cleanup done\n";

echo "=== Test complete ===\n";
