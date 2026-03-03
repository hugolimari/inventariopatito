<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UsuarioRepository;

/**
 * Controlador de Usuarios — Solo accesible por admin.
 */
class UsuarioController
{
    private UsuarioRepository $repo;

    public function __construct()
    {
        $this->repo = new UsuarioRepository();
    }

    /**
     * Lista todos los usuarios (solo admin).
     */
    public function index(): void
    {
        $this->verificarAdmin();

        $usuarios = $this->repo->findAll();
        $msg = $_GET['msg'] ?? null;

        require VIEWS_PATH . '/usuarios/index.php';
    }

    /**
     * Muestra el formulario para crear un usuario.
     */
    public function crear(): void
    {
        $this->verificarAdmin();

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $datos = [];
        $errores = [];

        require VIEWS_PATH . '/usuarios/crear.php';
    }

    /**
     * Procesa el registro de un nuevo usuario.
     */
    public function guardar(): void
    {
        $this->verificarAdmin();

        // Verificar CSRF
        $tokenRecibido = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenRecibido)) {
            die('Token CSRF inválido');
        }

        $datos = [
            'nombre_completo' => trim($_POST['nombre_completo'] ?? ''),
            'username'        => trim($_POST['username'] ?? ''),
            'password'        => $_POST['password'] ?? '',
            'rol'             => $_POST['rol'] ?? 'almacenero',
        ];

        $errores = [];

        if (empty($datos['nombre_completo'])) {
            $errores['nombre_completo'][] = 'El nombre completo es requerido';
        }
        if (empty($datos['username'])) {
            $errores['username'][] = 'El username es requerido';
        } elseif ($this->repo->findByUsername($datos['username']) !== null) {
            $errores['username'][] = 'Este username ya está en uso';
        }
        if (strlen($datos['password']) < 6) {
            $errores['password'][] = 'La contraseña debe tener al menos 6 caracteres';
        }
        if (!in_array($datos['rol'], ['admin', 'almacenero'])) {
            $errores['rol'][] = 'Rol inválido';
        }

        if (!empty($errores)) {
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            require VIEWS_PATH . '/usuarios/crear.php';
            return;
        }

        $this->repo->save(
            $datos['nombre_completo'],
            $datos['username'],
            $datos['password'],
            $datos['rol']
        );

        // Regenerar CSRF
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header('Location: ' . BASE_URL . '?controller=usuario&action=index&msg=creado');
        exit;
    }

    /**
     * Verifica que el usuario actual sea admin.
     */
    private function verificarAdmin(): void
    {
        if (($_SESSION['usuario_rol'] ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . '?controller=hardware&action=index');
            exit;
        }
    }
}
