<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\AuthRepository;

class UserManagementController extends BaseController
{
    private AuthRepository $authRepo;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->authRepo = $container->get('AuthRepository');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->ensureAuthenticated();
    }

    /**
     * Lista todos los usuarios (solo para admin)
     */
    public function index(): void
    {
        $current = $this->getCurrentUser();
        if (($current['role'] ?? 0) !== 1) {
            $this->error('No autorizado', 403);
        }

        $search = $this->getQuery('search', '');
        $users = $this->authRepo->findAll();

        if (!empty($search)) {
            $users = array_filter(
                $users,
                fn($u) => stripos($u['username'], $search) !== false || 
                          stripos($u['rol_nombre'] ?? '', $search) !== false
            );
        }

        // attach role name
        $roleRepo = $this->container->get('RoleRepository');
        if ($current) {
            $roleInfo = $roleRepo->findById((int)($current['role'] ?? 0));
            $current['role_name'] = $roleInfo['nombre'] ?? null;
        }

        // load available roles for registration modal (exclude admin)
        $roles = $roleRepo->findAll();
        $roles = array_filter($roles, fn($r) => $r['id_rol'] !== 1);

        $this->render('users/list', [
            'users' => $users,
            'search' => $search,
            'title' => 'Gestión de Usuarios',
            'currentUser' => $current,
            'roles' => $roles
        ]);
    }

    /**
     * Edita un usuario (cambiar rol, estado, contraseña)
     */
    public function edit(int $id): void
    {
        $current = $this->getCurrentUser();
        if (($current['role'] ?? 0) !== 1) {
            $this->error('No autorizado', 403);
        }

        $user = $this->authRepo->findById($id);
        if (!$user) {
            $this->error('Usuario no encontrado', 404);
        }

        // GET - mostrar formulario
        if ($this->getMethod() === 'GET') {
            $roles = $this->container->get('RoleRepository')->findAll();
            $this->render('users/edit', [
                'user' => $user,
                'roles' => $roles,
                'data' => [],
                'errors' => [],
                'title' => 'Editar Usuario',
                'currentUser' => array_merge($current, ['role_name' => $this->container->get('RoleRepository')->findById((int)($current['role'] ?? 0))['nombre'] ?? null])
            ]);
            return;
        }

        if ($this->getMethod() !== 'POST') {
            $this->error('Método no permitido', 405);
        }

        $data = [
            'id_rol' => $this->getPost('id_rol'),
            'estado' => $this->getPost('estado'),
            'password' => $this->getPost('password')
        ];

        $errors = $this->validate([
            'id_rol' => $data['id_rol'],
            'estado' => $data['estado']
        ], [
            'id_rol' => 'required|integer|min:1',
            'estado' => 'required|integer|in:0,1'
        ]);

        if (!empty($data['password'])) {
            $passErrors = $this->validate(['password' => $data['password']], [
                'password' => 'string|min:6'
            ]);
            $errors = array_merge($errors, $passErrors);
        }

        if (!empty($errors)) {
            $roles = $this->container->get('RoleRepository')->findAll();
            $roleInfo = $this->container->get('RoleRepository')->findById((int)($current['role'] ?? 0));
            $this->render('users/edit', [
                'user' => $user,
                'roles' => $roles,
                'data' => $data,
                'errors' => $errors,
                'title' => 'Editar Usuario',
                'currentUser' => array_merge($current, ['role_name' => $roleInfo['nombre'] ?? null])
            ]);
            return;
        }

        try {
            $updateData = [
                'id_rol' => (int) $data['id_rol'],
                'estado' => (int) $data['estado']
            ];
            if (!empty($data['password'])) {
                $updateData['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $this->authRepo->updateUser($id, $updateData);
            $this->setFlash('success', 'Usuario actualizado correctamente');
            $this->redirect('/admin/users.php');
        } catch (\Exception $e) {
            $this->error('Error al actualizar usuario: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Elimina un usuario (solo admin)
     */
    public function delete(int $id): void
    {
        $current = $this->getCurrentUser();
        if (($current['role'] ?? 0) !== 1) {
            $this->error('No autorizado', 403);
        }

        // impedir que el admin se elimine a sí mismo
        if ($id === $current['id']) {
            $this->error('No puedes eliminar tu propia cuenta', 403);
        }

        if (!$this->authRepo->findById($id)) {
            $this->error('Usuario no encontrado', 404);
        }

        try {
            $this->authRepo->deleteUser($id);
            $this->setFlash('success', 'Usuario eliminado');
            $this->redirect('/admin/users.php');
        } catch (\Exception $e) {
            $this->error('Error al eliminar usuario: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Alterna estado de un usuario (activo/inactivo)
     */
    public function toggleState(int $id): void
    {
        $current = $this->getCurrentUser();
        if (($current['role'] ?? 0) !== 1) {
            $this->error('No autorizado', 403);
        }

        if (!$this->authRepo->findById($id)) {
            $this->error('Usuario no encontrado', 404);
        }

        try {
            $this->authRepo->toggleUserState($id);
            $this->setFlash('success', 'Estado del usuario actualizado');
            $this->redirect('/admin/users.php');
        } catch (\Exception $e) {
            $this->error('Error al cambiar estado: ' . $e->getMessage(), 500);
        }
    }
}
