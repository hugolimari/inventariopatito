<?php
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php';
$cfg=new DatabaseConfig();
$pdo=new PDO($cfg->getDSN(),$cfg->getUsername(),$cfg->getPassword());
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

try {
    $pdo->exec("INSERT INTO movimientos(tipo,id_usuario,id_tecnico,observacion) VALUES ('SALIDA',1,NULL,'test')");
    echo "inserted raw movement\n";
} catch (Exception $e) {
    echo "raw insert failed: " . $e->getMessage() . "\n";
}
