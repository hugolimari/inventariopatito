<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\HardwareRepository;
use App\Repositories\CategoryRepository;
use App\Utils\DIContainer;

class HardwareController extends BaseController {
    private HardwareRepository $hardwareRepo;
    private CategoryRepository $categoryRepo;

    public function __construct(DIContainer $container) {
        parent::__construct($container);
        // iniciar sesión para métodos que necesiten datos de flash etc.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // asegurar autenticación en todas las acciones
        $this->ensureAuthenticated();

        $this->hardwareRepo = $container->get('HardwareRepository');
        $this->categoryRepo = $container->get('CategoryRepository');
    }

    public function index(): void {
        $page = (int) $this->getQuery('page', 1);
        $search = $this->getQuery('search', '');
        $selectedCategory = (int) $this->getQuery('category', 0);

        $allItems = $this->hardwareRepo->findAll();

        if (!empty($search)) {
            // search by brand name or model
            $allItems = array_filter(
                $allItems,
                fn($item) => stripos($item['marca_nombre'] ?? '', $search) !== false
                    || stripos($item['modelo'] ?? '', $search) !== false
            );
        }

        if ($selectedCategory > 0) {
            $allItems = array_filter(
                $allItems,
                fn($item) => (int)($item['id_categoria'] ?? 0) === $selectedCategory
            );
        }
        
        $itemsPerPage = 10;
        $totalItems = count($allItems);
        $totalPages = ceil($totalItems / $itemsPerPage);
        $page = max(1, min($page, $totalPages));
        
        $offset = ($page - 1) * $itemsPerPage;
        $items = array_slice($allItems, $offset, $itemsPerPage);
        
        $currentUser = $_SESSION['user'] ?? null;
        if ($currentUser) {
            $roleRepo = $this->container->get('RoleRepository');
            $role = $roleRepo->findById((int)($currentUser['role'] ?? 0));
            $currentUser['role_name'] = $role['nombre'] ?? null;
        }

        $categories = $this->categoryRepo->findAll();
        $brands = $this->container->get('MarcaRepository')->findAll();

        $this->render('hardware/list', [
            'items' => $items,
            'total' => $totalItems,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'categories' => $categories,
            'brands' => $brands,
            'selectedCategory' => $selectedCategory,
            'title' => 'Inventario de Hardware',
            'currentUser' => $currentUser
        ]);
    }

    public function show(int $id): void {
        $item = $this->hardwareRepo->findById($id);
        
        if (!$item) {
            $this->error('Hardware no encontrado', 404);
        }
        
        $this->render('hardware/show', [
            'item' => $item,
            'title' => "{$item['marca_nombre']} - {$item['modelo']}"
        ]);
    }

    public function create(): void {
        // if GET request, render the create form with categories
        if ($this->getMethod() === 'GET') {
            $categories = $this->categoryRepo->findAll();
            $brands = $this->container->get('MarcaRepository')->findAll();
            $this->render('hardware/create', [
                'data' => [],
                'errors' => [],
                'categories' => $categories,
                'brands' => $brands
            ]);
            return;
        }

        // only POST is allowed from here on
        if ($this->getMethod() !== 'POST') {
            $this->error('Método no permitido. Use POST.', 405);
        }
        
        $data = [
            'id_marca' => $this->getPost('id_marca'),
            'modelo' => $this->getPost('modelo'),
            'precio' => $this->getPost('precio'),
            'stock' => $this->getPost('stock'),
            'id_categoria' => $this->getPost('id_categoria')
        ];
        
        $errors = $this->validate($data, [
            'id_marca' => 'required|integer|min:1',
            'modelo' => 'required|string|min:2',
            'precio' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'id_categoria' => 'required|integer|min:1'
        ]);
        
        if (!empty($errors)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'errors' => $errors], 422);
            } else {
                $categories = $this->categoryRepo->findAll();
                $this->render('hardware/create', [
                    'data' => $data,
                    'errors' => $errors,
                    'categories' => $categories
                ]);
                return;
            }
        }
        
        try {
            $newId = $this->hardwareRepo->save($data);
            
            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => 'Hardware creado exitosamente', 'id' => $newId], 201);
            } else {
                $this->setFlash('success', 'Hardware creado exitosamente');
                $this->redirect('/hardware/index.php');
            }
        } catch (\Exception $e) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            $this->error('Error al crear hardware: ' . $e->getMessage(), 500);
        }
    }

    public function delete(int $id): void {
        // allow GET for simple link-based deletes; POST/DELETE preferred
        $method = $this->getMethod();
        if (!in_array($method, ['DELETE', 'POST', 'GET'], true)) {
            $this->error('Método no permitido', 405);
        }
        
        if (!$this->hardwareRepo->findById($id)) {
            $this->error('Hardware no encontrado', 404);
        }
        
        try {
            $this->hardwareRepo->delete($id);
            
            if ($this->isAjax()) {
                $this->success(['message' => 'Hardware eliminado exitosamente']);
            } else {
                $this->setFlash('success', 'Hardware eliminado');
                $this->redirect('/hardware/index.php');
            }
        } catch (\Exception $e) {
            $this->error('Error al eliminar: ' . $e->getMessage(), 500);
        }
    }

    public function update(int $id): void {
        if ($this->getMethod() !== 'PUT' && $this->getMethod() !== 'POST') {
            $this->error('Método no permitido', 405);
        }
        
        $item = $this->hardwareRepo->findById($id);
        if (!$item) {
            $this->error('Hardware no encontrado', 404);
        }
        
        $data = $this->getMethod() === 'PUT' 
            ? $this->getJsonBody()
            : $_POST;
        
        $errors = $this->validate($data, [
            'id_marca' => 'integer|min:1',
            'modelo' => 'string|min:2',
            'precio' => 'numeric|min:0.01',
            'stock' => 'integer|min:0'
        ]);
        
        if (!empty($errors)) {
            $this->error('Datos inválidos', 422, ['errors' => $errors]);
        }
        
        // Build update array with only provided fields
        $updateData = [];
        if (!empty($data['id_marca'])) $updateData['id_marca'] = (int) $data['id_marca'];
        if (!empty($data['modelo'])) $updateData['modelo'] = $data['modelo'];
        if (!empty($data['precio'])) $updateData['precio'] = (float) $data['precio'];
        if (isset($data['stock'])) $updateData['stock'] = (int) $data['stock'];
        
        try {
            $this->hardwareRepo->update($id, $updateData);
            $this->success(['message' => 'Hardware actualizado']);
        } catch (\Exception $e) {
            $this->error('Error al actualizar: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Muestra el formulario de edición y procesa actualizaciones.
     *
     * Aunque originalmente sólo los almaceneros podían acceder, el rol
     * administrador también debe tener permiso para editar componentes, así
     * que la comprobación de permiso acepta ambos (roles 1=y admin, 2=y almacenero).
     */
    public function edit(int $id): void {
        $current = $this->getCurrentUser();
        // permite administrador (1) o almacenero (2)
        if (!in_array(($current['role'] ?? 0), [1,2], true)) {
            $this->error('No autorizado', 403);
        }

        $item = $this->hardwareRepo->findById($id);
        if (!$item) {
            $this->error('Hardware no encontrado', 404);
        }

        // GET -> mostrar formulario con datos actuales
        if ($this->getMethod() === 'GET') {
            $categories = $this->categoryRepo->findAll();
            $brands = $this->container->get('MarcaRepository')->findAll();
            $roleInfo = $this->container->get('RoleRepository')->findById((int)($current['role'] ?? 0));
            $currentWithRole = array_merge($current, ['role_name' => $roleInfo['nombre'] ?? null]);
            $this->render('hardware/edit', [
                'item' => $item,
                'categories' => $categories,
                'brands' => $brands,
                'errors' => [],
                'title' => 'Editar componente',
                'currentUser' => $currentWithRole
            ]);
            return;
        }

        if ($this->getMethod() !== 'POST') {
            $this->error('Método no permitido', 405);
        }

        $data = [
            'id_marca' => $this->getPost('id_marca'),
            'modelo' => $this->getPost('modelo'),
            'precio' => $this->getPost('precio'),
            'stock' => $this->getPost('stock'),
            'id_categoria' => $this->getPost('id_categoria')
        ];

        $errors = $this->validate($data, [
            'id_marca' => 'required|integer|min:1',
            'modelo' => 'required|string|min:2',
            'precio' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'id_categoria' => 'required|integer|min:1'
        ]);

        if (!empty($errors)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'errors' => $errors], 422);
            } else {
                $categories = $this->categoryRepo->findAll();
                $roleInfo = $this->container->get('RoleRepository')->findById((int)($current['role'] ?? 0));
                $currentWithRole = array_merge($current, ['role_name' => $roleInfo['nombre'] ?? null]);
                $this->render('hardware/edit', [
                    'item' => array_merge($item, $data),
                    'categories' => $categories,
                    'errors' => $errors,
                    'title' => 'Editar componente',
                    'currentUser' => $currentWithRole
                ]);
            }
            return;
        }

        try {
            $updateData = [
                'id_marca' => (int) $data['id_marca'],
                'modelo' => (string) $data['modelo'],
                'precio' => (float) $data['precio'],
                'stock' => (int) $data['stock'],
                'id_categoria' => (int) $data['id_categoria']
            ];
            $this->hardwareRepo->update($id, $updateData);
            
            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => 'Componente actualizado correctamente']);
            } else {
                $this->setFlash('success', 'Componente actualizado correctamente');
                $this->redirect('/hardware/index.php');
            }
        } catch (\Exception $e) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            $this->error('Error al actualizar: ' . $e->getMessage(), 500);
        }
    }
}
