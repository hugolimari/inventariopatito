<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\AuthRepository;

class AuthController extends BaseController
{
    private AuthRepository $authRepo;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->authRepo = $container->get('AuthRepository');
    }

    public function showLogin(array $data = []): void
    {
        // Renderiza el formulario de login; puede recibir errores en $data
        $this->render('auth/login', $data);
    }

    public function login(): void
    {
        if ($this->getMethod() !== 'POST') {
            $this->redirect('/auth/login.php');
        }

        $username = $this->getPost('username');
        $password = $this->getPost('password');

        // Debug: Log qué se recibe (temporal)
        error_log("LOGIN ATTEMPT - Username: '$username', Password received: " . (!empty($password) ? 'YES (len=' . strlen($password) . ')' : 'NO'));

        try {
            $user = $this->authRepo->findByUsername($username);
        } catch (\Exception $e) {
            error_log("DB ERROR: " . $e->getMessage());
            $this->showLogin(['error' => 'Error de conexión a la base de datos: ' . $e->getMessage()]);
            return;
        }

        if (!$user) {
            error_log("USER NOT FOUND: '$username'");
            $this->showLogin(['error' => 'Usuario o contraseña inválidos.']);
            return;
        }

        $verify = password_verify($password, $user->getPasswordHash());
        error_log("PASSWORD VERIFY: " . ($verify ? 'TRUE' : 'FALSE') . " | Hash: " . substr($user->getPasswordHash(), 0, 20) . "...");

        if (!$verify) {
            $this->showLogin(['error' => 'Usuario o contraseña inválidos.']);
            return;
        }

        if (!$user->isActive()) {
            error_log("USER INACTIVE: '$username'");
            $this->showLogin(['error' => 'Usuario inactivo.']);
            return;
        }

        // sesión segura
        error_log("LOGIN SUCCESS: '$username'");
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'role' => $user->getRoleId(),
        ];

        $this->redirect('/hardware/index.php');
    }

    public function logout(): void
    {
        // destruir sesión y borrar cookie
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();

        $this->redirect('/auth/login.php');
    }

    /**
     * Permite al admin crear nuevos usuarios (almacenero o técnico).
     */
    public function register(): void
    {
        $this->ensureAuthenticated();
        $current = $this->getCurrentUser();
        if (($current['role'] ?? null) !== 1) {
            $this->error('No autorizado', 403);
        }

        // GET -> mostrar formulario
        if ($this->getMethod() === 'GET') {
            $roles = $this->container->get('RoleRepository')->findAll();
            // excluir admin
            $roles = array_filter($roles, fn($r) => $r['id_rol'] !== 1);
            $this->render('auth/register', [
                'data' => [],
                'errors' => [],
                'roles' => $roles
            ]);
            return;
        }

        if ($this->getMethod() !== 'POST') {
            $this->error('Método no permitido', 405);
        }

        $data = [
            'username' => $this->getPost('username'),
            'password' => $this->getPost('password'),
            'role' => $this->getPost('role')
        ];

        $errors = $this->validate($data, [
            'username' => 'required|string|min:3|max:50',
            'password' => 'required|string|min:6',
            'role' => 'required|integer|in:2,3'
        ]);

        if (!empty($errors)) {
            $roles = $this->container->get('RoleRepository')->findAll();
            $roles = array_filter($roles, fn($r) => $r['id_rol'] !== 1);
            $this->render('auth/register', [
                'data' => $data,
                'errors' => $errors,
                'roles' => $roles
            ]);
            return;
        }

        try {
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $this->authRepo->createUser($data['username'], $hash, (int) $data['role']);
            $this->setFlash('success', 'Usuario creado correctamente');
            $this->redirect('/auth/register.php');
        } catch (\Exception $e) {
            // guardar mensaje de error y volver a formulario
            $this->setFlash('error', 'Error al crear usuario: ' . $e->getMessage());
            $this->redirect('/auth/register.php');
        }
    }
}
