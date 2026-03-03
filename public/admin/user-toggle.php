<?php
declare(strict_types=1);

/**
 * Front controller: Cambiar estado de usuario
 * Ruta: GET /admin/user-toggle.php?id=X - Cambiar activeado/desactivado y redirigir
 */

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$container = require __DIR__ . '/../../config/di-container.php';

$session = $_SESSION ?? [];
$currentUser = $session['user'] ?? null;

// Verificar que sea admin
if (!$currentUser || ($currentUser['role'] ?? 0) !== 1) {
    http_response_code(403);
    die('Acceso denegado');
}

// Obtener ID del usuario
$userId = (int)($_GET['id'] ?? 0);
if ($userId <= 0) {
    http_response_code(400);
    die('ID de usuario inválido');
}

// Obtener controlador
try {
    $controllerClass = 'App\Controllers\UserManagementController';
    if (!class_exists($controllerClass)) {
        throw new Exception("Controlador no encontrado: $controllerClass");
    }
    $controller = new $controllerClass($container);
} catch (Exception $e) {
    http_response_code(500);
    die('Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

// Procesar cambio de estado
try {
    $controller->toggleState($userId);
} catch (Exception $e) {
    http_response_code(500);
    die('Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}