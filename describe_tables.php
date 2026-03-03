<?php
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php';
$cfg=new DatabaseConfig();
$pdo=new PDO($cfg->getDSN(),$cfg->getUsername(),$cfg->getPassword());
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

echo "movimientos:\n";
print_r($pdo->query('DESCRIBE movimientos')->fetchAll(PDO::FETCH_ASSOC));

echo "detalle_movimiento:\n";
print_r($pdo->query('DESCRIBE detalle_movimiento')->fetchAll(PDO::FETCH_ASSOC));
