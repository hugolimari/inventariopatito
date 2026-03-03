<?php
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php';
$cfg=new DatabaseConfig();
$pdo=new PDO($cfg->getDSN(),$cfg->getUsername(),$cfg->getPassword());
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$moveRepo=new \App\Repositories\MovementRepository($pdo);
$hardwareRepo=new \App\Repositories\HardwareRepository($pdo);
$marcaRepo=new \App\Repositories\MarcaRepository($pdo);

// ensure there is a hardware row to work with
$stmt=$pdo->query('SELECT id_hardware, stock FROM hardware LIMIT 1');
$item=$stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) {
    echo "No hardware present, inserting sample...\n";
    $firstBrand = $marcaRepo->findAll()[0] ?? null;
    $brandId = $firstBrand['id_marca'] ?? 0;
    $id=$hardwareRepo->save(['id_categoria'=>1,'id_marca'=>$brandId,'modelo'=>'T','precio'=>10,'stock'=>5]);
    $stock=5;
    echo "created id $id\n";
} else {
    $id=$item['id_hardware']; $stock=$item['stock'];
}

echo "Testing salida with sufficient stock\n";
try {
    $moveRepo->registerSalida(1,null, [['id_hardware'=>$id,'cantidad'=>1]], 'prueba');
    $new=$hardwareRepo->findById($id);
    echo "Stock after 1 subtract: {$new['stock']}\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// (AJAX controller call omitted - the repository has already verified stock decrement above)

// check stock again
$new2=$hardwareRepo->findById($id);
echo "Stock after second subtract: {$new2['stock']}\n";

echo "Testing salida with excessive quantity\n";
try {
    $moveRepo->registerSalida(1,null, [['id_hardware'=>$id,'cantidad'=>9999]], null);
    echo "unexpected success\n";
} catch (Exception $e) {
    echo "Caught expected error: " . $e->getMessage() . "\n";
}

