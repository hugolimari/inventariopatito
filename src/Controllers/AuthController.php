<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UsuarioRepository;

/**
 * Controlador de Autenticación — Login y Logout.
 */
class AuthController
{
    private UsuarioRepository $usuarioRepo;

    public function __construct()
    {
        $this->usuarioRepo = new UsuarioRepository();
    }

    /**
     * Muestra el formulario de login.
     */
    public function loginForm(): void
    {
        // Si ya está logueado, redirigir al catálogo
        if (isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '?controller=hardware&action=index');
            exit;
        }

        // Generar CSRF token
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        require VIEWS_PATH . '/auth/login.php';
    }

    /**
     * Procesa el login validando credenciales con password_verify().
     */
    public function login(): void
    {
        // Verificar CSRF
        $tokenRecibido = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenRecibido)) {
            die('Token CSRF inválido');
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = 'Todos los campos son obligatorios.';
            header('Location: ' . BASE_URL . '?controller=auth&action=loginForm');
            exit;
        }

        $usuario = $this->usuarioRepo->findByUsername($username);

        if ($usuario === null || !password_verify($password, $usuario->getPasswordHash())) {
            $_SESSION['login_error'] = 'Usuario o contraseña incorrectos.';
            header('Location: ' . BASE_URL . '?controller=auth&action=loginForm');
            exit;
        }

        // Login exitoso — regenerar ID de sesión (OWASP)
        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario->getId();
        $_SESSION['usuario_nombre'] = $usuario->getNombreCompleto();
        $_SESSION['usuario_username'] = $usuario->getUsername();
        $_SESSION['usuario_rol'] = $usuario->getRol();

        // Regenerar CSRF
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header('Location: ' . BASE_URL . '?controller=hardware&action=index');
        exit;
    }

    /**
     * Cierra la sesión y redirige al login.
     */
    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        session_destroy();
        header('Location: ' . BASE_URL . '?controller=auth&action=loginForm');
        exit;
    }
}
