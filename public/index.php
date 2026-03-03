<?php
declare(strict_types=1);

/**
 * ═══════════════════════════════════════════════════════════
 *  FRONT CONTROLLER — Enrutador Frontal del Sistema
 * ═══════════════════════════════════════════════════════════
 * 
 * Todas las peticiones HTTP pasan por este archivo.
 * Rutas: ?controller=X&action=Y
 * 
 * Ejemplo:
 *   index.php?controller=hardware&action=index
 *   index.php?controller=auth&action=loginForm
 */

// ── 1. Cargar dependencias ──────────────────────────────────
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/sesion.php';  // Inicia sesión segura

// ── 2. Generar CSRF token si no existe ──────────────────────
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── 3. Leer parámetros de ruta ──────────────────────────────
$controller = $_GET['controller'] ?? 'auth';
$action     = $_GET['action']     ?? 'loginForm';

// ── 4. Rutas públicas (no requieren autenticación) ──────────
$rutasPublicas = [
    'auth' => ['loginForm', 'login'],
];

// ── 5. Verificar autenticación ──────────────────────────────
$esPublica = isset($rutasPublicas[$controller]) && in_array($action, $rutasPublicas[$controller]);

if (!$esPublica && !isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?controller=auth&action=loginForm');
    exit;
}

// ── 6. Mapa de controladores ────────────────────────────────
$controladores = [
    'auth'     => \App\Controllers\AuthController::class,
    'hardware' => \App\Controllers\HardwareController::class,
    'usuario'  => \App\Controllers\UsuarioController::class,
];

// ── 7. Despachar la petición ────────────────────────────────
if (!isset($controladores[$controller])) {
    http_response_code(404);
    echo '<h1>404 — Controlador no encontrado</h1>';
    exit;
}

$claseControlador = $controladores[$controller];
$instancia = new $claseControlador();

if (!method_exists($instancia, $action)) {
    http_response_code(404);
    echo '<h1>404 — Acción no encontrada</h1>';
    exit;
}

// ── 8. Determinar si usar layout ────────────────────────────
// El login tiene su propio HTML; el resto usa layout.php
$sinLayout = ['loginForm', 'login', 'logout'];

if ($controller === 'auth' && in_array($action, $sinLayout)) {
    // Auth maneja su propia vista completa
    $instancia->$action();
} else {
    // Capturar salida del controlador para inyectar en layout
    ob_start();
    $instancia->$action();
    $contenido = ob_get_clean();

    // Determinar título
    $titulos = [
        'hardware' => [
            'index'     => 'Catálogo',
            'crear'     => 'Agregar Hardware',
            'editar'    => 'Editar Hardware',
            'guardar'   => 'Agregar Hardware',
            'actualizar'=> 'Editar Hardware',
        ],
        'usuario' => [
            'index'  => 'Usuarios',
            'crear'  => 'Registrar Usuario',
            'guardar'=> 'Registrar Usuario',
        ],
    ];
    $titulo = $titulos[$controller][$action] ?? APP_NAME;

    require VIEWS_PATH . '/layout.php';
}