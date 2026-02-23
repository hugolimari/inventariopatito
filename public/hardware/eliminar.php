<?php
declare(strict_types=1);

/**
 * HU 05 — Eliminar hardware por ID y redirigir al catálogo.
 */

// Cargar autoloader ANTES de session_start()
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../vendor/autoload.php';

session_start();

use App\Repositories\HardwareRepository;

// Obtener ID del parámetro GET
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    $repo = new HardwareRepository();
    $repo->delete($id);
}

// Redirigir siempre al catálogo
header('Location: index.php');
exit;
