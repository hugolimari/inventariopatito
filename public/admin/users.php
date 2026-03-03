<?php
declare(strict_types=1);

/**
 * Front controller: Gestión de usuarios
 * Rutas: GET /admin/users.php - Lista de usuarios
 *        POST /admin/users.php?action=edit - Editar usuario
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

// Procesar según método
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'edit') {
        $controller->edit((int)($_POST['id'] ?? 0));
    } else {
        $controller->index();
    }
} catch (Exception $e) {
    http_response_code(500);
    die('Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}