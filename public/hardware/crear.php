<?php
// Front controller for create hardware form / submission
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../vendor/autoload.php';

session_start();

$container = require __DIR__ . '/../../config/di-container.php';
$controller = new \App\Controllers\HardwareController($container);

// controller handles GET/POST internally
$controller->create();
