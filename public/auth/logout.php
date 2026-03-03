<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../vendor/autoload.php';

session_start();

$container = require __DIR__ . '/../../config/di-container.php';
$controller = new \App\Controllers\AuthController($container);
$controller->logout();
