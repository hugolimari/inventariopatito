<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Procesador;
use App\Models\TarjetaGrafica;
use App\Repositories\HardwareRepository;

/**
 * Controlador de Hardware — CRUD completo + búsqueda.
 */
class HardwareController
{
    private HardwareRepository $repo;

    public function __construct()
    {
        $this->repo = new HardwareRepository();
    }

    /**
     * Lista el catálogo con búsqueda opcional.
     */
    public function index(): void
    {
        $query = trim($_GET['q'] ?? '');

        if ($query !== '') {
            $items = $this->repo->search($query);
        } else {
            $items = $this->repo->findAll();
        }

        $alertas = count(array_filter($items, fn($item) => $item->esStockCritico()));

        require VIEWS_PATH . '/hardware/index.php';
    }

    /**
     * Muestra el formulario para crear hardware.
     */
    public function crear(): void
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $datos = [];
        $errores = [];

        require VIEWS_PATH . '/hardware/crear.php';
    }

    /**
     * Procesa el formulario de creación.
     */
    public function guardar(): void
    {
        // Verificar CSRF
        $tokenRecibido = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenRecibido)) {
            die('Token CSRF inválido');
        }

        $datos = $this->sanitizarDatos();
        $errores = $this->validarDatos($datos);

        if (!empty($errores)) {
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            require VIEWS_PATH . '/hardware/crear.php';
            return;
        }

        $item = $this->crearObjeto($datos);
        $this->repo->save($item);

        // Regenerar CSRF
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header('Location: ' . BASE_URL . '?controller=hardware&action=index&msg=creado');
        exit;
    }

    /**
     * Muestra el formulario de edición con datos prellenados.
     */
    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $item = $this->repo->findById($id);

        if ($item === null) {
            header('Location: ' . BASE_URL . '?controller=hardware&action=index&msg=no_encontrado');
            exit;
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Prellenar datos desde el objeto
        $datos = [
            'tipo'            => ($item instanceof Procesador) ? 'procesador' : 'tarjeta_grafica',
            'marca'           => $item->getMarca(),
            'modelo'          => $item->getModelo(),
            'precio'          => $item->getPrecio(),
            'stock'           => $item->getStock(),
            'categoria'       => $item->getCategoria(),
            'etiquetado'      => $item->getEtiquetado(),
            'vida_util_meses' => $item->getVidaUtilMeses(),
            'estado'          => $item->getEstado(),
            'nucleos'         => ($item instanceof Procesador) ? $item->getNucleos() : 0,
            'frecuencia'      => ($item instanceof Procesador) ? $item->getFrecuencia() : '',
            'vram'            => ($item instanceof TarjetaGrafica) ? $item->getVram() : '',
        ];
        $errores = [];

        require VIEWS_PATH . '/hardware/editar.php';
    }

    /**
     * Procesa la actualización de un hardware.
     */
    public function actualizar(): void
    {
        $id = (int) ($_POST['id'] ?? 0);

        // Verificar CSRF
        $tokenRecibido = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenRecibido)) {
            die('Token CSRF inválido');
        }

        $itemExistente = $this->repo->findById($id);
        if ($itemExistente === null) {
            header('Location: ' . BASE_URL . '?controller=hardware&action=index&msg=no_encontrado');
            exit;
        }

        $datos = $this->sanitizarDatos();
        $errores = $this->validarDatos($datos);

        if (!empty($errores)) {
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            require VIEWS_PATH . '/hardware/editar.php';
            return;
        }

        $item = $this->crearObjeto($datos);
        $item->setId($id);
        $this->repo->update($item);

        // Regenerar CSRF
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header('Location: ' . BASE_URL . '?controller=hardware&action=index&msg=actualizado');
        exit;
    }

    /**
     * Elimina un hardware por ID.
     */
    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id > 0) {
            $this->repo->delete($id);
        }

        header('Location: ' . BASE_URL . '?controller=hardware&action=index&msg=eliminado');
        exit;
    }

    // ── Métodos privados de ayuda ────────────────────────────

    /**
     * Sanitiza los datos del POST.
     */
    private function sanitizarDatos(): array
    {
        return [
            'tipo'            => $_POST['tipo'] ?? 'procesador',
            'marca'           => trim($_POST['marca'] ?? ''),
            'modelo'          => trim($_POST['modelo'] ?? ''),
            'precio'          => (float) ($_POST['precio'] ?? 0),
            'stock'           => (int) ($_POST['stock'] ?? 0),
            'categoria'       => trim($_POST['categoria'] ?? ''),
            'etiquetado'      => isset($_POST['etiquetado']),
            'vida_util_meses' => (int) ($_POST['vida_util_meses'] ?? 36),
            'estado'          => $_POST['estado'] ?? 'Llegada',
            'nucleos'         => (int) ($_POST['nucleos'] ?? 0),
            'frecuencia'      => trim($_POST['frecuencia'] ?? ''),
            'vram'            => trim($_POST['vram'] ?? ''),
        ];
    }

    /**
     * Valida los datos y retorna array de errores.
     */
    private function validarDatos(array $datos): array
    {
        $errores = [];

        if (empty($datos['marca'])) {
            $errores['marca'][] = 'La marca es requerida';
        }
        if (empty($datos['modelo'])) {
            $errores['modelo'][] = 'El modelo es requerido';
        }
        if ($datos['precio'] <= 0) {
            $errores['precio'][] = 'El precio debe ser mayor a 0';
        }
        if ($datos['stock'] < 0) {
            $errores['stock'][] = 'El stock no puede ser negativo';
        }
        if (empty($datos['categoria'])) {
            $errores['categoria'][] = 'La categoría es requerida';
        }
        if (!in_array($datos['estado'], ['Llegada', 'Inventariado', 'Baja'])) {
            $errores['estado'][] = 'Estado inválido';
        }

        if ($datos['tipo'] === 'procesador') {
            if ($datos['nucleos'] <= 0) {
                $errores['nucleos'][] = 'El número de núcleos debe ser mayor a 0';
            }
            if (empty($datos['frecuencia'])) {
                $errores['frecuencia'][] = 'La frecuencia es requerida';
            }
        } elseif ($datos['tipo'] === 'tarjeta_grafica') {
            if (empty($datos['vram'])) {
                $errores['vram'][] = 'La VRAM es requerida';
            }
        }

        return $errores;
    }

    /**
     * Crea un objeto Procesador o TarjetaGrafica a partir de datos sanitizados.
     */
    private function crearObjeto(array $datos): Procesador|TarjetaGrafica
    {
        $usuarioId = (int) ($_SESSION['usuario_id'] ?? 1);

        if ($datos['tipo'] === 'procesador') {
            return new Procesador(
                id: null,
                marca: $datos['marca'],
                modelo: $datos['modelo'],
                precio: $datos['precio'],
                stock: $datos['stock'],
                categoria: $datos['categoria'],
                nucleos: $datos['nucleos'],
                frecuencia: $datos['frecuencia'],
                etiquetado: $datos['etiquetado'],
                vida_util_meses: $datos['vida_util_meses'],
                estado: $datos['estado'],
                usuario_id: $usuarioId
            );
        }

        return new TarjetaGrafica(
            id: null,
            marca: $datos['marca'],
            modelo: $datos['modelo'],
            precio: $datos['precio'],
            stock: $datos['stock'],
            categoria: $datos['categoria'],
            vram: $datos['vram'],
            etiquetado: $datos['etiquetado'],
            vida_util_meses: $datos['vida_util_meses'],
            estado: $datos['estado'],
            usuario_id: $usuarioId
        );
    }
}
