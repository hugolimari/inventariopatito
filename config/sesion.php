<?php
declare(strict_types=1);

/**
 * Configuración de sesiones seguras (OWASP).
 * Este archivo debe requerirse ANTES de cualquier session_start().
 */

// Solo configurar si la sesión no ha iniciado aún
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');       // No accesible por JavaScript
    ini_set('session.cookie_samesite', 'Strict');  // Protección CSRF
    ini_set('session.use_strict_mode', '1');       // Rechaza IDs de sesión no inicializados
    ini_set('session.cookie_secure', '0');         // '1' en producción con HTTPS
    ini_set('session.use_only_cookies', '1');      // Solo cookies para sesiones

    session_start();

    // Regenerar ID de sesión periódicamente (cada 30 minutos)
    $regenerateInterval = 1800;
    if (!isset($_SESSION['_last_regeneration'])) {
        $_SESSION['_last_regeneration'] = time();
        session_regenerate_id(true);
    } elseif (time() - $_SESSION['_last_regeneration'] > $regenerateInterval) {
        $_SESSION['_last_regeneration'] = time();
        session_regenerate_id(true);
    }
}
