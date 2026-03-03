<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Procesador;
use App\Models\TarjetaGrafica;
use App\Models\MemoriaRAM;
use App\Models\Almacenamiento;
use App\Models\PlacaBase;
use App\Models\FuentePoder;
use App\Models\Hardware;
use App\Repositories\HardwareRepository;

/**
 * Controlador de Hardware — CRUD completo + búsqueda.
 * Soporta 6 tipos de hardware con auto-categoría.
 */
class HardwareController
{
    private HardwareRepository $repo;

    /**
     * Mapa tipo_clase → categoría automática.
     */
    private const TIPO_CATEGORIA = [
        'Procesador'      => 'CPU',
        'TarjetaGrafica'  => 'GPU',
        'MemoriaRAM'      => 'RAM',
        'Almacenamiento'  => 'SSD',
        'PlacaBase'       => 'Motherboard',
        'FuentePoder'     => 'PSU',
    ];

    public function __construct()
    {
        $this->repo = new HardwareRepository();
    }

    public function index(): void
    {
        $query = trim($_GET['q'] ?? '');
        $items = ($query !== '') ? $this->repo->search($query) : $this->repo->findAll();
        $alertas = count(array_filter($items, fn($item) => $item->esStockCritico()));
        require VIEWS_PATH . '/hardware/index.php';
    }

    public function crear(): void
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $datos = [];
        $errores = [];
        require VIEWS_PATH . '/hardware/crear.php';
    }

    public function guardar(): void
    {
        $tokenRecibido = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenRecibido)) {
            die('Token CSRF inválido');
        }

        $datos = $this->sanitizarDatos();
        $errores = $this->validarDatos($datos);

        if (!empty($errores)) {
            if (!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            require VIEWS_PATH . '/hardware/crear.php';
            return;
        }

        $item = $this->crearObjeto($datos);
        $this->repo->save($item);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: ' . BASE_URL . '?controller=hardware&action=index&msg=creado');
        exit;
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $item = $this->repo->findById($id);

        if ($item === null) {
            header('Location: ' . BASE_URL . '?controller=hardware&action=index&msg=no_encontrado');
            exit;
        }

        if (!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        $datos = $this->extraerDatos($item);
        $errores = [];
        require VIEWS_PATH . '/hardware/editar.php';
    }

    public function actualizar(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $tokenRecibido = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenRecibido)) {
            die('Token CSRF inválido');
        }

        if ($this->repo->findById($id) === null) {
            header('Location: ' . BASE_URL . '?controller=hardware&action=index&msg=no_encontrado');
            exit;
        }

        $datos = $this->sanitizarDatos();
        $errores = $this->validarDatos($datos);

        if (!empty($errores)) {
            if (!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            require VIEWS_PATH . '/hardware/editar.php';
            return;
        }

        $item = $this->crearObjeto($datos);
        $item->setId($id);
        $this->repo->update($item);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: ' . BASE_URL . '?controller=hardware&action=index&msg=actualizado');
        exit;
    }

    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) $this->repo->delete($id);
        header('Location: ' . BASE_URL . '?controller=hardware&action=index&msg=eliminado');
        exit;
    }

    // ══════════════════════════════════════════════════════════
    //  PRIVADOS
    // ══════════════════════════════════════════════════════════

    private function sanitizarDatos(): array
    {
        $tipo = $_POST['tipo'] ?? 'Procesador';
        return [
            'tipo'              => $tipo,
            'marca'             => trim($_POST['marca'] ?? ''),
            'modelo'            => trim($_POST['modelo'] ?? ''),
            'precio'            => (float) ($_POST['precio'] ?? 0),
            'stock'             => (int) ($_POST['stock'] ?? 0),
            'categoria'         => self::TIPO_CATEGORIA[$tipo] ?? $tipo,
            'etiquetado'        => isset($_POST['etiquetado']),
            'vida_util_meses'   => (int) ($_POST['vida_util_meses'] ?? 36),
            'estado'            => $_POST['estado'] ?? 'Llegada',
            // Procesador
            'nucleos'           => (int) ($_POST['nucleos'] ?? 0),
            'frecuencia'        => trim($_POST['frecuencia'] ?? ''),
            // Tarjeta Gráfica
            'vram'              => trim($_POST['vram'] ?? ''),
            // Memoria RAM
            'ram_capacidad'     => trim($_POST['ram_capacidad'] ?? ''),
            'ram_tipo'          => trim($_POST['ram_tipo'] ?? ''),
            'ram_velocidad'     => trim($_POST['ram_velocidad'] ?? ''),
            // Almacenamiento
            'alm_capacidad'     => trim($_POST['alm_capacidad'] ?? ''),
            'alm_tipo'          => trim($_POST['alm_tipo'] ?? ''),
            'alm_velocidad'     => trim($_POST['alm_velocidad'] ?? ''),
            // Placa Base
            'socket'            => trim($_POST['socket'] ?? ''),
            'formato'           => trim($_POST['formato'] ?? ''),
            // Fuente de Poder
            'potencia'          => trim($_POST['potencia'] ?? ''),
            'certificacion'     => trim($_POST['certificacion'] ?? ''),
        ];
    }

    private function validarDatos(array $d): array
    {
        $e = [];
        if (empty($d['marca']))   $e['marca'][]  = 'La marca es requerida';
        if (empty($d['modelo']))  $e['modelo'][]  = 'El modelo es requerido';
        if ($d['precio'] <= 0)    $e['precio'][]  = 'El precio debe ser mayor a 0';
        if ($d['stock'] < 0)      $e['stock'][]   = 'El stock no puede ser negativo';
        if (!in_array($d['estado'], ['Llegada','Inventariado','Baja'])) $e['estado'][] = 'Estado inválido';

        match($d['tipo']) {
            'Procesador' => (function() use ($d, &$e) {
                if ($d['nucleos'] <= 0)          $e['nucleos'][]    = 'Núcleos debe ser mayor a 0';
                if (empty($d['frecuencia']))     $e['frecuencia'][] = 'La frecuencia es requerida';
            })(),
            'TarjetaGrafica' => (function() use ($d, &$e) {
                if (empty($d['vram'])) $e['vram'][] = 'La VRAM es requerida';
            })(),
            'MemoriaRAM' => (function() use ($d, &$e) {
                if (empty($d['ram_capacidad']))  $e['ram_capacidad'][]  = 'La capacidad es requerida';
                if (empty($d['ram_tipo']))       $e['ram_tipo'][]       = 'El tipo es requerido';
                if (empty($d['ram_velocidad']))  $e['ram_velocidad'][]  = 'La velocidad es requerida';
            })(),
            'Almacenamiento' => (function() use ($d, &$e) {
                if (empty($d['alm_capacidad']))  $e['alm_capacidad'][] = 'La capacidad es requerida';
                if (empty($d['alm_tipo']))       $e['alm_tipo'][]      = 'El tipo es requerido';
            })(),
            'PlacaBase' => (function() use ($d, &$e) {
                if (empty($d['socket']))  $e['socket'][]  = 'El socket es requerido';
                if (empty($d['formato'])) $e['formato'][] = 'El formato es requerido';
            })(),
            'FuentePoder' => (function() use ($d, &$e) {
                if (empty($d['potencia']))      $e['potencia'][]      = 'La potencia es requerida';
                if (empty($d['certificacion'])) $e['certificacion'][] = 'La certificación es requerida';
            })(),
            default => $e['tipo'][] = 'Tipo inválido',
        };

        return $e;
    }

    private function crearObjeto(array $d): Hardware
    {
        $uid = (int) ($_SESSION['usuario_id'] ?? 1);
        $cat = $d['categoria'];

        return match($d['tipo']) {
            'Procesador' => new Procesador(null, $d['marca'], $d['modelo'], $d['precio'], $d['stock'], $cat,
                $d['nucleos'], $d['frecuencia'], $d['etiquetado'], $d['vida_util_meses'], $d['estado'], $uid),
            'TarjetaGrafica' => new TarjetaGrafica(null, $d['marca'], $d['modelo'], $d['precio'], $d['stock'], $cat,
                $d['vram'], $d['etiquetado'], $d['vida_util_meses'], $d['estado'], $uid),
            'MemoriaRAM' => new MemoriaRAM(null, $d['marca'], $d['modelo'], $d['precio'], $d['stock'], $cat,
                $d['ram_capacidad'], $d['ram_tipo'], $d['ram_velocidad'], $d['etiquetado'], $d['vida_util_meses'], $d['estado'], $uid),
            'Almacenamiento' => new Almacenamiento(null, $d['marca'], $d['modelo'], $d['precio'], $d['stock'], $cat,
                $d['alm_capacidad'], $d['alm_tipo'], $d['alm_velocidad'], $d['etiquetado'], $d['vida_util_meses'], $d['estado'], $uid),
            'PlacaBase' => new PlacaBase(null, $d['marca'], $d['modelo'], $d['precio'], $d['stock'], $cat,
                $d['socket'], $d['formato'], $d['etiquetado'], $d['vida_util_meses'], $d['estado'], $uid),
            'FuentePoder' => new FuentePoder(null, $d['marca'], $d['modelo'], $d['precio'], $d['stock'], $cat,
                $d['potencia'], $d['certificacion'], $d['etiquetado'], $d['vida_util_meses'], $d['estado'], $uid),
        };
    }

    /**
     * Extrae datos de un objeto Hardware existente para prellenar el form de edición.
     */
    private function extraerDatos(Hardware $item): array
    {
        $d = [
            'tipo'            => $item->getTipoClase(),
            'marca'           => $item->getMarca(),
            'modelo'          => $item->getModelo(),
            'precio'          => $item->getPrecio(),
            'stock'           => $item->getStock(),
            'categoria'       => $item->getCategoria(),
            'etiquetado'      => $item->getEtiquetado(),
            'vida_util_meses' => $item->getVidaUtilMeses(),
            'estado'          => $item->getEstado(),
            'nucleos' => 0, 'frecuencia' => '', 'vram' => '',
            'ram_capacidad' => '', 'ram_tipo' => '', 'ram_velocidad' => '',
            'alm_capacidad' => '', 'alm_tipo' => '', 'alm_velocidad' => '',
            'socket' => '', 'formato' => '', 'potencia' => '', 'certificacion' => '',
        ];

        if ($item instanceof Procesador)      { $d['nucleos'] = $item->getNucleos(); $d['frecuencia'] = $item->getFrecuencia(); }
        if ($item instanceof TarjetaGrafica)   { $d['vram'] = $item->getVram(); }
        if ($item instanceof MemoriaRAM)       { $d['ram_capacidad'] = $item->getCapacidad(); $d['ram_tipo'] = $item->getTipo(); $d['ram_velocidad'] = $item->getVelocidad(); }
        if ($item instanceof Almacenamiento)   { $d['alm_capacidad'] = $item->getCapacidad(); $d['alm_tipo'] = $item->getTipoAlmacenamiento(); $d['alm_velocidad'] = $item->getVelocidadLectura() ?? ''; }
        if ($item instanceof PlacaBase)        { $d['socket'] = $item->getSocket(); $d['formato'] = $item->getFormato(); }
        if ($item instanceof FuentePoder)      { $d['potencia'] = $item->getPotencia(); $d['certificacion'] = $item->getCertificacion(); }

        return $d;
    }
}
