<?php
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php';
$cfg = new DatabaseConfig();
$pdo = new PDO($cfg->getDSN(), $cfg->getUsername(), $cfg->getPassword());
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->query('DESCRIBE usuarios');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
echo "\nRoles table:\n";
$stmt = $pdo->query('SELECT * FROM roles');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
