<?php
// Front controller for editing a hardware item
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$container = require __DIR__ . '/../../config/di-container.php';
$currentUser = $_SESSION['user'] ?? null;

// ensure id param
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    die('ID inválido');
}

$controllerClass = 'App\\Controllers\\HardwareController';
if (!class_exists($controllerClass)) {
    http_response_code(500);
    die('Controlador no encontrado');
}

$controller = new $controllerClass($container);
$controller->edit($id);
