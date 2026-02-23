<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Hardware;
use App\Models\Procesador;
use App\Models\TarjetaGrafica;

/**
 * Repositorio de hardware con persistencia en sesión.
 * Los datos se mantienen mientras el navegador esté abierto.
 * 
 * IMPORTANTE: session_start() debe llamarse ANTES de instanciar esta clase.
 */
class HardwareRepository
{
    private array $items = [];
    private int $nextId = 1;

    public function __construct()
    {
        $this->loadFromSession();
    }

    /**
     * Carga datos de la sesión, o genera datos de prueba si es la primera vez.
     */
    private function loadFromSession(): void
    {
        if (isset($_SESSION['hardware_items']) && is_array($_SESSION['hardware_items'])) {
            $this->items = $_SESSION['hardware_items'];
            $this->nextId = $_SESSION['hardware_nextId'] ?? 1;
        } else {
            // Primera visita: cargar datos de prueba
            $this->seedData();
            $this->saveToSession();
        }
    }

    /**
     * Persiste el estado actual en la sesión.
     */
    private function saveToSession(): void
    {
        $_SESSION['hardware_items'] = $this->items;
        $_SESSION['hardware_nextId'] = $this->nextId;
    }

    /**
     * Datos de prueba: 2 procesadores y 2 tarjetas gráficas.
     */
    private function seedData(): void
    {
        $hardwareData = [
            new Procesador(
                id: null,
                marca: 'AMD',
                modelo: 'Ryzen 5 5600X',
                precio: 199.99,
                stock: 15,
                categoria: 'Procesadores',
                nucleos: 6,
                frecuencia: '4.6GHz'
            ),
            new Procesador(
                id: null,
                marca: 'Intel',
                modelo: 'Core i7-13700K',
                precio: 409.99,
                stock: 2,  // ← Stock crítico para demostrar HU 10
                categoria: 'Procesadores',
                nucleos: 16,
                frecuencia: '5.4GHz'
            ),
            new TarjetaGrafica(
                id: null,
                marca: 'NVIDIA',
                modelo: 'GeForce RTX 4070',
                precio: 599.99,
                stock: 8,
                categoria: 'Tarjetas Gráficas',
                vram: '12GB GDDR6X'
            ),
            new TarjetaGrafica(
                id: null,
                marca: 'AMD',
                modelo: 'Radeon RX 7600',
                precio: 269.99,
                stock: 1,  // ← Stock crítico para demostrar HU 10
                categoria: 'Tarjetas Gráficas',
                vram: '8GB GDDR6'
            ),
        ];

        foreach ($hardwareData as $item) {
            $this->save($item);
        }
    }

    // ── CRUD ─────────────────────────────────────────────────

    /**
     * Guarda un item de hardware (nuevo o existente).
     */
    public function save(Hardware $item): void
    {
        if ($item->getId() === null) {
            $item->setId($this->nextId++);
        }
        $this->items[$item->getId()] = $item;
        $this->saveToSession();
    }

    /**
     * Obtiene todos los items de hardware.
     */
    public function findAll(): array
    {
        return array_values($this->items);
    }

    /**
     * Busca un item por su ID.
     */
    public function findById(int $id): ?Hardware
    {
        return $this->items[$id] ?? null;
    }

    /**
     * Elimina un item por su ID.
     */
    public function delete(int $id): void
    {
        unset($this->items[$id]);
        $this->saveToSession();
    }
}
